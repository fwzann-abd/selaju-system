<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClassroomRequest;
use App\Http\Requests\UpdateClassroomRequest;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentPosition;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassroomController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q', '');

        $classrooms = Classroom::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('level', 'like', "%{$search}%")
                        ->orWhere('major', 'like', "%{$search}%")
                        ->orWhere('academic_year', 'like', "%{$search}%");
                });
            })
            ->with(['teacher:id,name', 'school'])
            ->orderBy('academic_year', 'desc')
            ->orderBy('level')
            ->orderBy('major')
            ->orderBy('name')
            ->paginate(10);

        $pageTitle = 'Daftar Kelas';
        $breadcrumb = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Master Data', 'url' => null],
            ['label' => 'Kelas', 'url' => null],
        ];

        return view('admin.classrooms.index', compact('classrooms', 'search', 'pageTitle', 'breadcrumb'));
    }

    public function create(): View
    {
        $teachers = Teacher::select('id', 'name')->orderBy('name')->get();
        $currentYear = (int) date('Y');
        $academicYears = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $nextYear = $i + 1;
            $academicYears[] = "{$i}/{$nextYear}";
        }

        $tingkatan = [
            'X' => 'Kelas X',
            'XI' => 'Kelas XI',
            'XII' => 'Kelas XII',
        ];

        // Add more levels if needed
        $jurusan = [
            'AKL' => 'AKL',
            'MPL' => 'MPL',
            'TLG' => 'TLG',
            'PM' => 'PM',
            'TKF' => 'TKF',
            'TLM' => 'TLM',
            'DKV' => 'DKV',
            'PPL' => 'PPL',
            'TJK' => 'TJK',
            'TET' => 'TET',
        ];

        return view('admin.classrooms.create', [
            'teachers' => $teachers,
            'academicYears' => $academicYears,
            'tingkatan' => $tingkatan,
            'jurusan' => $jurusan,
            'pageTitle' => 'Tambah Kelas',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Kelas', 'url' => route('admin.classrooms.index')],
                ['label' => 'Tambah', 'url' => null],
            ],
        ]);
    }

    public function store(StoreClassroomRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Classroom::create($validated);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(Classroom $classroom): View
    {
        $teachers = Teacher::select('id', 'name')->orderBy('name')->get();
        $currentYear = (int) date('Y');
        $academicYears = [];
        for ($i = $currentYear - 2; $i <= $currentYear + 1; $i++) {
            $nextYear = $i + 1;
            $academicYears[] = "{$i}/{$nextYear}";
        }

        $tingkatan = [
            'X' => 'Kelas X',
            'XI' => 'Kelas XI',
            'XII' => 'Kelas XII',
        ];

        $jurusan = [
            'AKL' => 'AKL',
            'MPL' => 'MPL',
            'TLG' => 'TLG',
            'PM' => 'PM',
            'TKF' => 'TKF',
            'TLM' => 'TLM',
            'DKV' => 'DKV',
            'PPL' => 'PPL',
            'TJK' => 'TJK',
            'TET' => 'TET',
        ];

        return view('admin.classrooms.edit', [
            'classroom' => $classroom->load(['teacher:id,name', 'school']),
            'teachers' => $teachers,
            'academicYears' => $academicYears,
            'tingkatan' => $tingkatan,
            'jurusan' => $jurusan,
            'pageTitle' => 'Edit Kelas',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Kelas', 'url' => route('admin.classrooms.index')],
                ['label' => 'Edit', 'url' => null],
            ],
        ]);
    }

    public function update(UpdateClassroomRequest $request, Classroom $classroom): RedirectResponse
    {
        $validated = $request->validated();

        $classroom->update($validated);

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    public function show(Classroom $classroom): View
    {
        $classroom->load(['teacher:id,name', 'school', 'classroomStudents.student', 'classroomStudents.position']);

        $assignedStudentIds = $classroom->classroomStudents->pluck('student_id')->toArray();

        $availableStudents = Student::query()
            ->whereNotIn('id', $assignedStudentIds)
            ->orderBy('name')
            ->select('id', 'name', 'student_number')
            ->get();

        $positions = StudentPosition::query()->orderBy('name')->get();

        return view('admin.classrooms.show', [
            'classroom' => $classroom,
            'availableStudents' => $availableStudents,
            'positions' => $positions,
            'pageTitle' => 'Detail Kelas',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Master Data', 'url' => null],
                ['label' => 'Kelas', 'url' => route('admin.classrooms.index')],
                ['label' => 'Detail', 'url' => null],
            ],
        ]);
    }

    public function bulkAssign(Request $request, Classroom $classroom): RedirectResponse
    {
        if ($request->has('_remove')) {
            $request->validate(['student_id' => ['required', 'exists:students,id']]);
            $classroom->students()->detach($request->input('student_id'));

            return redirect()->route('admin.classrooms.show', $classroom)
                ->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
        }

        $validated = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['required', 'exists:students,id'],
            'position_id' => ['nullable', 'exists:student_positions,id'],
        ]);

        $pivotData = [];
        foreach ($validated['student_ids'] as $studentId) {
            $pivotData[$studentId] = [
                'student_position_id' => $validated['position_id'] ?? null,
            ];
        }

        $classroom->students()->syncWithoutDetaching($pivotData);

        return redirect()->route('admin.classrooms.show', $classroom)
            ->with('success', count($validated['student_ids']).' siswa berhasil ditambahkan ke kelas.');
    }

    public function destroy(Classroom $classroom): RedirectResponse
    {
        $classroom->delete();

        return redirect()->route('admin.classrooms.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
