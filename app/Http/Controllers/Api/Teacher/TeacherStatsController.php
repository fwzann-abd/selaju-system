<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\CourseMaterial;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $currentDate = now();

        // Attendance summary across all teacher's schedules (current month)
        $attendanceCounts = Attendance::whereIn('schedule_id', $scheduleIds)
            ->whereYear('date', $currentDate->year)
            ->whereMonth('date', $currentDate->month)
            ->selectRaw('LOWER(status) as status, COUNT(*) as total')
            ->groupBy(DB::raw('LOWER(status)'))
            ->pluck('total', 'status')
            ->reduce(function (array $counts, $total, $status) {
                $mappedStatus = match ($status) {
                    'present', 'hadir', 'late' => 'present',
                    'permit', 'izin', 'excused' => 'permit',
                    'sick', 'sakit' => 'sick',
                    'absent', 'alpa' => 'absent',
                    default => null,
                };

                if ($mappedStatus === null) {
                    return $counts;
                }

                $counts[$mappedStatus] = ($counts[$mappedStatus] ?? 0) + $total;

                return $counts;
            }, []);

        $attendanceCounts = collect($attendanceCounts);
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
                    'period' => $currentDate->format('Y-m'),
                    'present' => (int) $presentCount,
                    'permit' => (int) $attendanceCounts->get('permit', 0),
                    'sick' => (int) $attendanceCounts->get('sick', 0),
                    'absent' => (int) $attendanceCounts->get('absent', 0),
                    'total' => $attendanceTotal,
                ],
            ],
        ]);
    }
}
