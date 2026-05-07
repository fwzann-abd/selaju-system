<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\CourseMaterial;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeacherStatsController extends Controller
{
    /**
     * Return dynamic statistics for the authenticated teacher.
     */
    public function index(Request $request): JsonResponse
    {
        $teacher = $request->user()?->teacher;

        if (! $teacher) {
            return response()->json(['message' => 'Profile Teacher belum dikonfigurasi.'], 403);
        }

        // All schedule IDs owned by this teacher
        $scheduleIds = Schedule::where('teacher_id', $teacher->id)->pluck('id');

        // Total schedules / classes taught
        $totalSchedules = $scheduleIds->count();

        // Total unique students across all teacher's classrooms
        $classroomIds = Schedule::where('teacher_id', $teacher->id)
            ->distinct()
            ->pluck('classroom_id');

        $totalStudents = \App\Models\Classroom::whereIn('id', $classroomIds)
            ->withCount('students')
            ->get()
            ->sum('students_count');

        // Total materials uploaded by this teacher
        $totalMaterials = CourseMaterial::where('teacher_id', $teacher->id)->count();

        // Total assignments created by this teacher
        $totalAssignments = Assignment::where('teacher_id', $teacher->id)->count();

        // Attendance summary across all teacher's schedules (current month)
        $currentMonth = now()->format('Y-m');
        $attendanceCounts = Attendance::whereIn('schedule_id', $scheduleIds)
            ->whereRaw("TO_CHAR(date, 'YYYY-MM') = ?", [$currentMonth])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $attendanceTotal = $attendanceCounts->sum();
        $presentCount = $attendanceCounts->get('present', 0);
        $attendanceRate = $attendanceTotal > 0
            ? round(($presentCount / $attendanceTotal) * 100, 1)
            : null;

        return response()->json([
            'data' => [
                'total_schedules' => $totalSchedules,
                'total_students' => $totalStudents,
                'total_materials' => $totalMaterials,
                'total_assignments' => $totalAssignments,
                'attendance' => [
                    'rate_percent' => $attendanceRate,
                    'period' => $currentMonth,
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
