<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EplinOfficer;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EplinOfficerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $officers = EplinOfficer::query()
            ->with('student')
            ->when($search, function ($query, $search) {
                $query->whereHas('student', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.eplin.officers.index', [
            'officers' => $officers,
            'search' => $search,
            'pageTitle' => 'Daftar Petugas Eplin',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Eplin', 'url' => '#'],
                ['label' => 'Petugas', 'url' => ''],
            ],
        ]);
    }

    public function create(): View
    {
        $students = Student::with('account')
            ->orderBy('name')
            ->get();

        // Get already assigned students
        $assignedStudentIds = EplinOfficer::pluck('student_id')->toArray();
        $availableStudents = $students->filter(fn ($student) => ! in_array($student->id, $assignedStudentIds));

        return view('admin.eplin.officers.create', [
            'students' => $availableStudents,
            'pageTitle' => 'Tambah Petugas',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Eplin', 'url' => '#'],
                ['label' => 'Petugas', 'url' => route('admin.eplin.officers.index')],
                ['label' => 'Tambah', 'url' => ''],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,id', 'unique:eplin_officers,student_id'],
            'role' => ['required', 'string', 'in:petugas'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $data['is_active'] = true;

        EplinOfficer::create($data);

        return redirect()
            ->route('admin.eplin.officers.index')
            ->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit(EplinOfficer $officer): View
    {
        $students = Student::with('account')
            ->orderBy('name')
            ->get();

        return view('admin.eplin.officers.edit', [
            'officer' => $officer,
            'students' => $students,
            'pageTitle' => 'Edit Petugas',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Eplin', 'url' => '#'],
                ['label' => 'Petugas', 'url' => route('admin.eplin.officers.index')],
                ['label' => 'Edit', 'url' => ''],
            ],
        ]);
    }

    public function update(Request $request, EplinOfficer $officer)
    {
        $data = $request->validate([
            'student_id' => ['required', 'string', 'exists:students,id'],
            'role' => ['required', 'string', 'in:petugas'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $officer->update($data);

        return redirect()
            ->route('admin.eplin.officers.index')
            ->with('success', 'Petugas berhasil diperbarui.');
    }

    public function destroy(EplinOfficer $officer)
    {
        $officer->delete();

        return redirect()
            ->route('admin.eplin.officers.index')
            ->with('success', 'Petugas berhasil dihapus.');
    }
}
