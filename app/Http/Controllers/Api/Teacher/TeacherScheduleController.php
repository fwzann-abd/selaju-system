<?php
namespace App\Http\Controllers\Api\Teacher;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;

class TeacherScheduleController extends Controller {
    public function index(Request $request) {
        $teacher = $request->user()->teacher;
        
        if (!$teacher) {
            return response()->json(['message' => 'Profile Teacher belum dikonfigurasi.'], 403);
        }

        $schedules = Schedule::with(['classroom', 'subject', 'room'])
             ->where('teacher_id', $teacher->id)
             ->latest()
             ->get();

        return response()->json([
            'status' => 'success',
            'data' => ScheduleResource::collection($schedules)
        ]);
    }
}