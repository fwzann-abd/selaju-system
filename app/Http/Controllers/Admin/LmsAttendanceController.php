<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Classroom;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class LmsAttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Attendance::query()
            ->with(['student', 'schedule.classroom', 'schedule.teacher', 'schedule.subject'])
            ->latest('date');

        if ($request->filled('classroom_id')) {
            $query->whereHas('schedule', function ($q) use ($request): void {
                $q->where('classroom_id', $request->classroom_id);
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->paginate(20)->withQueryString();
        $classrooms = Classroom::query()->orderBy('name')->get();

        return view('admin.attendances.index', [
            'attendances' => $attendances,
            'classrooms' => $classrooms,
            'pageTitle' => 'Data Absensi KBM',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'LMS Melesat', 'url' => null],
                ['label' => 'Data Absensi', 'url' => null],
            ],
        ]);
    }
}
