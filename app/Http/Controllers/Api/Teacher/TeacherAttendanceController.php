<?php
namespace App\Http\Controllers\Api\Teacher;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;

class TeacherAttendanceController extends Controller {
    public function store(StoreAttendanceRequest $request) {
        $validated = $request->validated();
        
        $schedule = Schedule::findOrFail($validated['schedule_id']);
        if ($schedule->teacher_id !== $request->user()->teacher->id) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        
        $date = $validated['date'];
        
        foreach($validated['attendances'] as $att) {
            Attendance::updateOrCreate(
               ['schedule_id' => $schedule->id, 'student_id' => $att['student_id'], 'date' => $date],
               ['status' => $att['status']]
            );
        }
        
        return response()->json(['status' => 'success', 'message' => 'Kehadiran berhasil disimpan.']);
    }
}