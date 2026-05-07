<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\Request;

class StudentScheduleController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;

        if (! $student) {
            return response()->json(['message' => 'Profile Siswa belum dikonfigurasi.'], 403);
        }

        // Get classids the student is in
        $classroom_ids = $student->classrooms()->pluck('classrooms.id');

        $schedules = Schedule::with(['classroom.students', 'teacher', 'subject', 'room'])
            ->whereIn('classroom_id', $classroom_ids)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => ScheduleResource::collection($schedules),
        ]);
    }
}
