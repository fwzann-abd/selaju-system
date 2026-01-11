<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EplinAttendance;
use App\Models\EplinOfficer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EplinAttendanceController extends Controller
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
        $query = EplinAttendance::with([
            'student' => fn ($q) => $q->select('id', 'name', 'student_number'),
        ]);

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('attendance_date', '>=', $request->query('from_date'));
        }
        if ($request->has('to_date')) {
            $query->whereDate('attendance_date', '<=', $request->query('to_date'));
        }

        // Filter by student
        if ($request->has('student_id')) {
            $query->where('student_id', $request->query('student_id'));
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $attendances = $query->latest('attendance_date')->paginate(20);

        return response()->json($attendances);
    }

    public function show(EplinAttendance $attendance): JsonResponse
    {
        $attendance->load('student');

        return response()->json($attendance);
    }

    public function store(Request $request): JsonResponse
    {
        if (! $this->isOfficer($request)) {
            return response()->json(['message' => 'Hanya petugas yang dapat membuat data kehadiran'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|uuid|exists:students,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:on_time,late,absent,excused',
            'arrival_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        // Prevent duplicate attendance for same student on same date
        $existing = EplinAttendance::where('student_id', $validated['student_id'])
            ->whereDate('attendance_date', $validated['attendance_date'])
            ->first();

        if ($existing) {
            return response()->json(['message' => 'Attendance already recorded for this student on this date'], 409);
        }

        $attendance = EplinAttendance::create($validated);
        $attendance->load('student');

        return response()->json($attendance, 201);
    }

    public function update(EplinAttendance $attendance, Request $request): JsonResponse
    {
        if (! $this->isOfficer($request)) {
            return response()->json(['message' => 'Hanya petugas yang dapat mengubah data kehadiran'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:on_time,late,absent,excused',
            'arrival_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        return response()->json($attendance);
    }

    public function destroy(EplinAttendance $attendance, Request $request): JsonResponse
    {
        if (! $this->isOfficer($request)) {
            return response()->json(['message' => 'Hanya petugas yang dapat menghapus data kehadiran'], 403);
        }

        $attendance->delete();

        return response()->json(['message' => 'Attendance record deleted successfully']);
    }
}
