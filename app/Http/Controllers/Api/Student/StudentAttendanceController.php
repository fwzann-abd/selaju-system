<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceResource;
use App\Models\Attendance;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user()->student;
        if (! $student) {
            return response()->json(['message' => 'Profile Siswa belum dikonfigurasi.'], 403);
        }

        $attendances = Attendance::with('schedule.subject')
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => AttendanceResource::collection($attendances),
        ]);
    }
}
