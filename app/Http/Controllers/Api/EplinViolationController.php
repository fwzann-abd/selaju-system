<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EplinOfficer;
use App\Models\EplinViolation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EplinViolationController extends Controller
{
    private function isOfficer(Request $request): bool
    {
        $user = $request->user();
        if (! $user) {
            return false;
        }

        return EplinOfficer::where('student_id', $user->id)
            ->where('is_active', true)
            ->exists();
    }

    public function index(Request $request): JsonResponse
    {
        $query = EplinViolation::with([
            'student' => fn ($q) => $q->select('id', 'nama', 'nisn'),
            'violationType',
            'recordedByOfficer.student',
        ]);

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('violation_date', '>=', $request->query('from_date'));
        }
        if ($request->has('to_date')) {
            $query->whereDate('violation_date', '<=', $request->query('to_date'));
        }

        // Filter by student
        if ($request->has('student_id')) {
            $query->where('student_id', $request->query('student_id'));
        }

        $violations = $query->latest('violation_date')->paginate(20);

        return response()->json($violations);
    }

    public function show(EplinViolation $violation): JsonResponse
    {
        $violation->load([
            'student',
            'violationType',
            'recordedByOfficer.student',
        ]);

        return response()->json($violation);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->isOfficer($request)) {
            return response()->json(['message' => 'Hanya petugas yang dapat membuat laporan pelanggaran'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
            'violation_type_id' => 'required|uuid|exists:eplin_violation_types,id',
            'recorded_by_officer_id' => 'required|uuid|exists:eplin_officers,id',
            'violation_date' => 'required|date',
            'description' => 'nullable|string',
            'evidence' => 'nullable|string',
        ]);

        $violation = EplinViolation::create($validated);
        $violation->load(['student', 'violationType', 'recordedByOfficer.student']);

        return response()->json($violation, 201);
    }

    public function update(EplinViolation $violation, Request $request): JsonResponse
    {
        if (! $this->isOfficer($request)) {
            return response()->json(['message' => 'Hanya petugas yang dapat mengubah laporan pelanggaran'], 403);
        }

        $validated = $request->validate([
            'violation_date' => 'required|date',
            'description' => 'nullable|string',
            'evidence' => 'nullable|string',
            'status' => 'required|in:recorded,under_review,verified,dismissed',
        ]);

        $violation->update($validated);

        return response()->json($violation);
    }

    public function destroy(EplinViolation $violation, Request $request): JsonResponse
    {
        if (! $this->isOfficer($request)) {
            return response()->json(['message' => 'Hanya petugas yang dapat menghapus laporan pelanggaran'], 403);
        }

        $violation->delete();

        return response()->json(['message' => 'Violation deleted successfully']);
    }
}
