<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\CourseMaterial;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentStatsController extends Controller
{
    /**
     * Return dynamic statistics for the authenticated student.
     */
    public function index(Request $request): JsonResponse
    {
        $student = $request->user()?->student;

        if (! $student) {
            return response()->json(['message' => 'Profile Siswa belum dikonfigurasi.'], 403);
        }

        // Classrooms the student belongs to
        $classroomIds = $student->classrooms()->pluck('classrooms.id');

        // Schedule IDs from those classrooms
        $scheduleIds = Schedule::whereIn('classroom_id', $classroomIds)->pluck('id');

        // Total active schedules
        $totalSchedules = $scheduleIds->count();

        // Total materials available in student's classrooms
        $totalMaterials = CourseMaterial::whereIn('schedule_id', $scheduleIds)
            ->where('is_published', true)
            ->count();

        // Total assignments available in student's classrooms
        $totalAssignments = Assignment::whereIn('classroom_id', $classroomIds)
            ->where('is_published', true)
            ->count();

        // Attendance — all records for this student
        $attendanceCounts = Attendance::where('student_id', $student->id)
            ->whereIn('schedule_id', $scheduleIds)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $attendanceTotal = $attendanceCounts->sum();
        $presentCount = $attendanceCounts->get('present', 0);
        $attendanceRate = $attendanceTotal > 0
            ? round(($presentCount / $attendanceTotal) * 100, 1)
            : null;

        // Pending assignments (not yet submitted)
        $submittedIds = \App\Models\AssignmentSubmission::where('student_id', $student->id)
            ->pluck('assignment_id');

        $pendingAssignments = Assignment::whereIn('classroom_id', $classroomIds)
            ->where('is_published', true)
            ->where('due_date', '>=', now())
            ->whereNotIn('id', $submittedIds)
            ->count();

        return response()->json([
            'data' => [
                'total_schedules' => $totalSchedules,
                'total_materials' => $totalMaterials,
                'total_assignments' => $totalAssignments,
                'pending_assignments' => $pendingAssignments,
                'attendance' => [
                    'rate_percent' => $attendanceRate,
                    'present' => (int) $presentCount,
                    'absent' => (int) $attendanceCounts->get('absent', 0),
                    'late' => (int) $attendanceCounts->get('late', 0),
                    'excused' => (int) $attendanceCounts->get('excused', 0),
                    'total' => $attendanceTotal,
                ],
            ],
        ]);
    }
}
