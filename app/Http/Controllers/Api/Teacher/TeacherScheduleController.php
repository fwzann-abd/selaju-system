<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;

class TeacherScheduleController extends Controller
{
    public function index(Request $request)
    {
        $teacher = $request->user()->teacher;

        if (! $teacher) {
            return response()->json(['message' => 'Profile Teacher belum dikonfigurasi.'], 403);
        }

        $schedules = Schedule::with(['classroom.students', 'teacher', 'subject', 'room'])
            ->withCount(['attendances as today_attendance_count' => function ($query) {
                $query->whereDate('date', today());
            }])
            ->where('teacher_id', $teacher->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => ScheduleResource::collection($schedules),
        ]);
    }
}
