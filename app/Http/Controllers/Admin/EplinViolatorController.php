<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EplinViolation;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EplinViolatorController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('q');

        // Get distinct students with violations
        $violators = Student::query()
            ->with([
                'violations' => fn ($q) => $q->orderBy('violation_date', 'desc'),
            ])
            ->whereHas('violations')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('student_number', 'like', "%{$search}%");
            })
            ->withCount('violations')
            ->orderBy('violations_count', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('admin.eplin.violators.index', [
            'violators' => $violators,
            'search' => $search,
            'pageTitle' => 'Daftar Pelanggar',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Eplin', 'url' => '#'],
                ['label' => 'Pelanggar', 'url' => ''],
            ],
        ]);
    }

    public function show(Student $student): View
    {
        $violations = $student->violations()
            ->with('violationType', 'recordedByOfficer.student')
            ->orderBy('violation_date', 'desc')
            ->paginate(20);

        return view('admin.eplin.violators.show', [
            'student' => $student,
            'violations' => $violations,
            'pageTitle' => 'Detail Pelanggar',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Eplin', 'url' => '#'],
                ['label' => 'Pelanggar', 'url' => route('admin.eplin.violators.index')],
                ['label' => $student->name, 'url' => ''],
            ],
        ]);
    }

    public function edit(EplinViolation $violation): View
    {
        return view('admin.eplin.violators.edit', [
            'violation' => $violation->load('student', 'violationType', 'recordedByOfficer.student'),
            'pageTitle' => 'Edit Pelanggaran',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'Eplin', 'url' => '#'],
                ['label' => 'Pelanggar', 'url' => route('admin.eplin.violators.index')],
                ['label' => 'Edit', 'url' => ''],
            ],
        ]);
    }

    public function update(Request $request, EplinViolation $violation)
    {
        $data = $request->validate([
            'violation_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'evidence' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:recorded,under_review,verified,dismissed'],
        ]);

        $violation->update($data);

        return redirect()
            ->route('admin.eplin.violators.show', $violation->student_id)
            ->with('success', 'Pelanggaran berhasil diperbarui.');
    }

    public function destroy(EplinViolation $violation)
    {
        $studentId = $violation->student_id;
        // Soft delete
        $violation->delete();

        return redirect()
            ->route('admin.eplin.violators.show', $studentId)
            ->with('success', 'Pelanggaran berhasil dihapus.');
    }

    public function forceDestroy(EplinViolation $violation)
    {
        $studentId = $violation->student_id;
        // Permanent delete
        $violation->forceDelete();

        return redirect()
            ->route('admin.eplin.violators.show', $studentId)
            ->with('success', 'Pelanggaran berhasil dihapus secara permanen.');
    }

    public function destroyViolator(Student $student)
    {
        $count = $student->violations()->count();
        // Soft delete semua violations siswa
        $student->violations()->delete();

        return redirect()
            ->route('admin.eplin.violators.index')
            ->with('success', "Pelanggar dan {$count} pelanggaran berhasil dihapus.");
    }

    public function forceDestroyViolator(Student $student)
    {
        $count = $student->violations()->count();
        // Permanent delete semua violations siswa
        $student->violations()->forceDelete();

        return redirect()
            ->route('admin.eplin.violators.index')
            ->with('success', "Pelanggar dan {$count} pelanggaran berhasil dihapus secara permanen.");
    }
}
