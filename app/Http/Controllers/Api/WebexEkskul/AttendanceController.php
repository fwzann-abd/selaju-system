<?php

namespace App\Http\Controllers\Api\WebexEkskul;

use App\Http\Controllers\Controller;
use App\Models\WebexAttendance;
use App\Models\WebexEkskul;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(WebexEkskul $ekskul): JsonResponse
    {
        $attendances = $ekskul->attendances()
            ->with(['pengurus' => fn ($q) => $q->with('student:id,name,nis,email')])
            ->latest('attendance_date')
            ->paginate(15);

        return response()->json($attendances);
    }

    public function store(WebexEkskul $ekskul, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pengurus_id' => 'required|uuid|exists:webex_pengurus,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,sick,excused',
            'notes' => 'nullable|string',
        ]);

        // Verify pengurus belongs to this ekskul
        $pengurus = \App\Models\WebexPengurus::find($validated['pengurus_id']);
        if ($pengurus->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Invalid pengurus for this ekskul'], 422);
        }

        $attendance = $ekskul->attendances()->create($validated);
        $attendance->load(['pengurus' => fn ($q) => $q->with('student:id,name,nis,email')]);

        return response()->json($attendance, 201);
    }

    public function update(WebexEkskul $ekskul, WebexAttendance $attendance, Request $request): JsonResponse
    {
        if ($attendance->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:present,absent,sick,excused',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);
        $attendance->load(['pengurus' => fn ($q) => $q->with('student:id,name,nis,email')]);

        return response()->json($attendance);
    }

    public function destroy(WebexEkskul $ekskul, WebexAttendance $attendance): JsonResponse
    {
        if ($attendance->ekskul_id !== $ekskul->id) {
            return response()->json(['message' => 'Not found'], 404);
        }

        $attendance->delete();

        return response()->json(['message' => 'Attendance record deleted successfully']);
    }
}
