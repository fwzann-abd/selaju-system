<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Classroom;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
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

        return view('admin.schedules.index', compact(
            'schedules',
            'classrooms',
            'teachers',
            'subjects',
            'rooms',
            'days'
        ));
    }

    public function create(): View
    {
        return view('admin.schedules.create', $this->formData() + [
            'pageTitle' => 'Tambah Jadwal KBM',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'LMS Melesat', 'url' => null],
                ['label' => 'Jadwal KBM', 'url' => route('admin.schedules.index')],
                ['label' => 'Tambah', 'url' => null],
            ],
        ]);
    }

    public function edit(Schedule $schedule): View
    {
        return view('admin.schedules.edit', $this->formData() + [
            'schedule' => $schedule,
            'pageTitle' => 'Edit Jadwal KBM',
            'breadcrumb' => [
                ['label' => 'Dashboard', 'url' => route('dashboard')],
                ['label' => 'LMS Melesat', 'url' => null],
                ['label' => 'Jadwal KBM', 'url' => route('admin.schedules.index')],
                ['label' => 'Edit', 'url' => null],
            ],
        ]);
    }

    public function store(StoreScheduleRequest $request): RedirectResponse
    {
        Schedule::create($request->validated());

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal KBM berhasil ditambahkan.');
    }

    public function update(UpdateScheduleRequest $request, Schedule $schedule): RedirectResponse
    {
        $schedule->update($request->validated());

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal KBM berhasil diperbarui.');
    }

    public function destroy(Schedule $schedule): RedirectResponse
    {
        $schedule->delete();

        return redirect()
            ->route('admin.schedules.index')
            ->with('success', 'Jadwal KBM berhasil dihapus.');
    }

    /**
     * AJAX endpoint for real-time schedule conflict detection.
     */
    public function checkConflict(Request $request): JsonResponse
    {
        $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'day' => ['required', 'string'],
            'start_time' => ['required'],
            'end_time' => ['required'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'exclude_id' => ['nullable', 'exists:schedules,id'],
        ]);

        $conflicts = [];

        $teacherConflict = Schedule::query()
            ->where('teacher_id', $request->teacher_id)
            ->where('day', $request->day)
            ->where(function ($q) use ($request) {
                $q->where(function ($q2) use ($request) {
                    $q2->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })
            ->when($request->exclude_id, fn ($q) => $q->where('id', '!=', $request->exclude_id))
            ->with(['classroom', 'subject'])
            ->first();

        if ($teacherConflict) {
            $conflicts[] = [
                'type' => 'teacher',
                'message' => "Guru sudah mengajar di {$teacherConflict->classroom->name} ({$teacherConflict->subject->name}) pukul {$teacherConflict->start_time} - {$teacherConflict->end_time}",
            ];
        }

        if ($request->filled('room_id')) {
            $roomConflict = Schedule::query()
                ->where('room_id', $request->room_id)
                ->where('day', $request->day)
                ->where(function ($q) use ($request) {
                    $q->where(function ($q2) use ($request) {
                        $q2->where('start_time', '<', $request->end_time)
                            ->where('end_time', '>', $request->start_time);
                    });
                })
                ->when($request->exclude_id, fn ($q) => $q->where('id', '!=', $request->exclude_id))
                ->with('room')
                ->first();

            if ($roomConflict) {
                $conflicts[] = [
                    'type' => 'room',
                    'message' => "Ruangan {$roomConflict->room->name} sudah digunakan pukul {$roomConflict->start_time} - {$roomConflict->end_time}",
                ];
            }
        }

        return response()->json([
            'hasConflict' => count($conflicts) > 0,
            'conflicts' => $conflicts,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function formData(): array
    {
        return [
            'classrooms' => Classroom::query()->orderBy('name')->get(),
            'teachers' => Teacher::query()->orderBy('name')->get(),
            'subjects' => Subject::query()->orderBy('name')->get(),
            'rooms' => Room::query()->orderBy('name')->get(),
            'days' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
        ];
    }
}
