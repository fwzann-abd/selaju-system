<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Account;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q', '');
        $schoolId = $request->query('school_id', '');

        $teachers = Teacher::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhereHas('account', function ($accountQuery) use ($search) {
                            $accountQuery->where('username', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->when($schoolId, function ($query, $schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->with(['school:id,name', 'account:uuid,username,email'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $schools = School::select('id', 'name')->orderBy('name')->get();

        return view('admin.teachers.index', [
            'teachers' => $teachers,
            'schools' => $schools,
            'search' => $search,
            'selectedSchool' => $schoolId,
            'pageTitle' => 'Daftar Guru',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Guru', 'url' => null],
            ],
        ]);
    }

    public function create(): View
    {
        $schools = School::select('id', 'name')->orderBy('name')->get();

        return view('admin.teachers.create', [
            'schools' => $schools,
            'pageTitle' => 'Tambah Guru',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Guru', 'url' => route('admin.teachers.index')],
                ['label' => 'Tambah', 'url' => null],
            ],
        ]);
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated) {
            $account = Account::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_active' => true,
            ]);

            Teacher::create([
                'account_id' => $account->uuid,
                'school_id' => $validated['school_id'],
                'name' => $validated['name'],
                'nip' => $validated['nip'] ?? null,
            ]);
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher): View
    {
        $schools = School::select('id', 'name')->orderBy('name')->get();

        return view('admin.teachers.edit', [
            'teacher' => $teacher->load(['school:id,name', 'account:uuid,username,email']),
            'schools' => $schools,
            'pageTitle' => 'Edit Guru',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Guru', 'url' => route('admin.teachers.index')],
                ['label' => 'Edit', 'url' => null],
            ],
        ]);
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated, $teacher) {
            $account = $teacher->account;
            $account->update([
                'username' => $validated['username'],
                'email' => $validated['email'],
            ] + ($validated['password'] ? ['password' => Hash::make($validated['password'])] : []));

            $teacher->update([
                'school_id' => $validated['school_id'],
                'name' => $validated['name'],
                'nip' => $validated['nip'] ?? null,
            ]);
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $account = $teacher->account;

        DB::transaction(function () use ($teacher, $account) {
            if ($account) {
                $account->delete();
            } else {
                $teacher->delete();
            }
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}
