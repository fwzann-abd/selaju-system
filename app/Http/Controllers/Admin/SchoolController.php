<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        $schools = School::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('admin.schools.index', [
            'schools' => $schools,
            'search' => $search,
            'pageTitle' => 'Daftar Sekolah',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => '#'],
                ['label' => 'Sekolah', 'url' => ''],
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.schools.create', [
            'pageTitle' => 'Tambah Sekolah',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => '#'],
                ['label' => 'Sekolah', 'url' => route('admin.schools.index')],
                ['label' => 'Tambah', 'url' => ''],
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('schools', 'slug')],
        ]);

        $data['slug'] = $this->formatSlug($data['slug']);

        School::create($data);

        return redirect()
            ->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function edit(School $school): View
    {
        return view('admin.schools.edit', [
            'school' => $school,
            'pageTitle' => 'Edit Sekolah',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => '#'],
                ['label' => 'Sekolah', 'url' => route('admin.schools.index')],
                ['label' => 'Edit', 'url' => ''],
            ],
        ]);
    }

    public function update(Request $request, School $school)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('schools', 'slug')->ignore($school->id)],
        ]);

        $data['slug'] = $this->formatSlug($data['slug']);

        $school->update($data);

        return redirect()
            ->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $school->delete();

        return redirect()
            ->route('admin.schools.index')
            ->with('success', 'Sekolah berhasil dihapus.');
    }

    private function formatSlug(string $value): string
    {
        $slug = Str::slug($value);
        return $slug !== '' ? $slug : Str::uuid()->toString();
    }
}
