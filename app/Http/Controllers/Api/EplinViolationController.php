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
            \Log::info('isOfficer check - No user');

            return false;
        }

        $userUuid = $user->uuid ?? $user->getKey();

        // Find student yang associated dengan current account
        // Lalu cek apakah student itu adalah officer yang aktif
        $exists = EplinOfficer::where('is_active', true)
            ->whereHas('student', function ($q) use ($userUuid) {
                $q->where('account_id', $userUuid);
            })
            ->exists();

        \Log::info('isOfficer check', [
            'user_id' => $user->getKey(),
            'user_uuid' => $userUuid,
            'user_model' => class_basename($user),
            'is_officer' => $exists,
        ]);

        return $exists;
    }

    private function getOfficer(Request $request): ?EplinOfficer
    {
        $user = $request->user();
        if (! $user) {
            return null;
        }

        $userUuid = $user->uuid ?? $user->getKey();

        $officer = EplinOfficer::with('student')
            ->whereHas('student', function ($q) use ($userUuid) {
                $q->where('account_id', $userUuid);
            })
            ->where('is_active', true)
            ->first();

        \Log::info('Officer lookup', [
            'user_id' => $user->getKey(),
            'user_uuid' => $userUuid,
            'officer_found' => $officer ? $officer->id : 'not-found',
            'officer_is_active' => $officer?->is_active,
            'student_account_id' => $officer?->student?->account_id,
        ]);

        return $officer;
    }

    public function index(Request $request): JsonResponse
    {
        $query = EplinViolation::with([
            'student' => fn ($q) => $q->select('id', 'name', 'student_number'),
            'violationType',
            'recordedByOfficer.student',
        ]);

        // Default: filter by today's date
        $fromDate = $request->query('from_date', today()->toDateString());
        $toDate = $request->query('to_date', today()->toDateString());

        $query->whereDate('violation_date', '>=', $fromDate);
        $query->whereDate('violation_date', '<=', $toDate);

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
            'violation_date' => 'required|date',
            'description' => 'nullable|string',
            'evidence' => 'nullable|string',
        ]);

        // Find officer yang sesuai dengan current user
        $officer = $this->getOfficer($request);

        if (! $officer) {
            return response()->json(['message' => 'Officer tidak ditemukan'], 403);
        }

        $validated['recorded_by_officer_id'] = $officer->id;
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
