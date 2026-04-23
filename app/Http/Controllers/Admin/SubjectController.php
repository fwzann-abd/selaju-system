<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $subjects = Subject::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.subjects.index', compact('subjects', 'search'));
    }

    public function create(): View
    {
        $breadcrumb = [
            ['label' => 'Mata Pelajaran', 'url' => route('admin.subjects.index')],
            ['label' => 'Tambah Mata Pelajaran', 'url' => null],
        ];

        $types = $this->getSubjectTypes();

        return view('admin.subjects.create', [
            'breadcrumb' => $breadcrumb,
            'pageTitle' => 'Tambah Mata Pelajaran',
            'types' => $types,
        ]);
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function show(Subject $subject): View
    {
        $subject->load(['schedules.teacher', 'schedules.classroom']);

        return view('admin.subjects.show', [
            'subject' => $subject,
            'pageTitle' => $subject->name,
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'LMS Melesat', 'url' => null],
                ['label' => 'Mata Pelajaran', 'url' => route('admin.subjects.index')],
                ['label' => 'Detail', 'url' => null],
            ],
        ]);
    }

    public function edit(Subject $subject): View
    {
        $breadcrumb = [
            ['label' => 'Mata Pelajaran', 'url' => route('admin.subjects.index')],
            ['label' => 'Edit Mata Pelajaran', 'url' => null],
        ];

        $types = $this->getSubjectTypes();

        return view('admin.subjects.edit', [
            'breadcrumb' => $breadcrumb,
            'pageTitle' => 'Edit Mata Pelajaran',
            'subject' => $subject,
            'types' => $types,
        ]);
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    /**
     * @return array<string, string>
     */
    protected function getSubjectTypes(): array
    {
        return [
            'Vocational' => 'Vocational',
            'Theory' => 'Theory',
            'Practical' => 'Practical',
            'Workshop' => 'Workshop',
        ];
    }
}
