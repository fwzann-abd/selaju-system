<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Schedule;
use Illuminate\Http\Request;

class LmsAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classrooms = Classroom::orderBy('name')->get();

        $query = Attendance::with(['student', 'schedule.classroom', 'schedule.teacher', 'schedule.subject'])
            ->latest('date');

        if ($request->filled('classroom_id')) {
            $query->whereHas('schedule', function ($q) use ($request) {
                $q->where('classroom_id', $request->classroom_id);
            });
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        $attendances = $query->paginate(20)->withQueryString();

        return view('admin.attendances.index', compact('attendances', 'classrooms'));
    }
}
