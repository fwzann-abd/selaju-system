<?php

namespace App\Http\Controllers\Admin\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Classroom;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Schedule::query()
            ->with(['classroom', 'teacher', 'subject', 'room'])
            ->orderBy('day')
            ->orderBy('start_time');

        if ($request->filled('classroom_id')) {
            $query->where('classroom_id', $request->classroom_id);
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        if ($request->filled('day')) {
            $query->where('day', $request->day);
        }

        $schedules = $query->get();

        $classrooms = Classroom::query()->orderBy('name')->get();
        $teachers = Teacher::query()->orderBy('name')->get();
        $subjects = Subject::query()->orderBy('name')->get();
        $rooms = Room::query()->orderBy('name')->get();

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        return view('admin.lms.schedules.index', compact(
            'schedules',
            'classrooms',
            'teachers',
            'subjects',
            'rooms',
            'days'
        ));
    }

    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        Schedule::create($request->validated());

        return redirect()
            ->route('admin.lms.schedules.index')
            ->with('success', 'Jadwal KBM berhasil ditambahkan.');
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()
            ->route('admin.lms.schedules.index')
            ->with('success', 'Jadwal KBM berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()
            ->route('admin.lms.schedules.index')
            ->with('success', 'Jadwal KBM berhasil dihapus.');
    }
}
