<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\ShowAttendanceSheetRequest;
use App\Http\Requests\StoreAttendanceRequest;
use App\Http\Resources\AttendanceResource;
use App\Http\Resources\AttendanceSheetStudentResource;
use App\Http\Resources\ScheduleResource;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TeacherAttendanceController extends Controller
{
    public function sheet(ShowAttendanceSheetRequest $request, Schedule $schedule): JsonResponse
    {
        $teacher = $request->user()?->teacher;

        if (! $teacher) {
            return response()->json(['message' => 'Profile Teacher belum dikonfigurasi.'], 403);
        }

        if ($schedule->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $attendanceDate = ($request->date('date') ?? now())->toDateString();

        $schedule->load([
            'classroom',
            'subject',
            'teacher',
            'classroom.students.account',
        ]);

        $attendanceMap = Attendance::query()
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $attendanceDate)
            ->get()
            ->keyBy('student_id');

        $students = $schedule->classroom->students
            ->sortBy('name')
            ->values();

        $students->each(function ($student) use ($attendanceMap): void {
            $student->setRelation('attendanceRecord', $attendanceMap->get($student->id));
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'date' => $attendanceDate,
                'schedule' => new ScheduleResource($schedule),
                'students' => AttendanceSheetStudentResource::collection($students)->resolve(),
                'summary' => [
                    'student_count' => $students->count(),
                    'recorded_count' => $attendanceMap->count(),
                ],
            ],
        ]);
    }

    public function store(StoreAttendanceRequest $request): JsonResponse
    {
        $teacher = $request->user()?->teacher;

        if (! $teacher) {
            return response()->json(['message' => 'Profile Teacher belum dikonfigurasi.'], 403);
        }

        $validated = $request->validated();

        $schedule = Schedule::query()
            ->with('classroom.students')
            ->findOrFail($validated['schedule_id']);

        if ($schedule->teacher_id !== $teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $submittedStudentIds = collect($validated['attendances'])
            ->pluck('student_id');

        $allowedStudentIds = $schedule->classroom->students
            ->pluck('id');

        $invalidStudentIds = $submittedStudentIds
            ->diff($allowedStudentIds)
            ->values();

        if ($invalidStudentIds->isNotEmpty()) {
            return response()->json([
                'message' => 'Terdapat siswa yang tidak terdaftar pada kelas jadwal ini.',
                'errors' => [
                    'attendances' => [
                        'Semua siswa harus terdaftar pada kelas yang sama dengan jadwal.',
                    ],
                    'student_ids' => $invalidStudentIds,
                ],
            ], 422);
        }

        DB::transaction(function () use ($validated, $schedule): void {
            foreach ($validated['attendances'] as $attendance) {
                Attendance::updateOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'student_id' => $attendance['student_id'],
                        'date' => $validated['date'],
                    ],
                    [
                        'status' => $attendance['status'],
                    ]
                );
            }
        });

        $savedAttendances = Attendance::query()
            ->with(['schedule.classroom', 'schedule.subject', 'student'])
            ->where('schedule_id', $schedule->id)
            ->whereDate('date', $validated['date'])
            ->whereIn('student_id', $submittedStudentIds)
            ->get()
            ->sortBy(fn (Attendance $attendance) => $attendance->student?->name)
            ->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Kehadiran berhasil disimpan.',
            'data' => AttendanceResource::collection($savedAttendances)->resolve(),
            'summary' => [
                'date' => $validated['date'],
                'schedule_id' => $schedule->id,
                'recorded_count' => $savedAttendances->count(),
                'present_count' => $savedAttendances->where('status', 'present')->count(),
                'absent_count' => $savedAttendances->where('status', 'absent')->count(),
                'late_count' => $savedAttendances->where('status', 'late')->count(),
                'excused_count' => $savedAttendances->where('status', 'excused')->count(),
            ],
        ]);
    }
}
