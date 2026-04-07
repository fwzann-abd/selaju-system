<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
class ScheduleController extends Controller {
    public function index() {
        $schedules = Schedule::with(['classroom', 'teacher', 'subject'])->latest()->get();
        return response()->json(['status' => 'success', 'data' => ScheduleResource::collection($schedules)]);
    }
    public function store(StoreScheduleRequest $request) {
        $schedule = Schedule::create($request->validated());
        return response()->json(['status' => 'success', 'message' => 'Schedule created', 'data' => new ScheduleResource($schedule)], 201);
    }
    public function show(Schedule $schedule) {
        $schedule->load(['classroom', 'teacher', 'subject']);
        return response()->json(['status' => 'success', 'data' => new ScheduleResource($schedule)]);
    }
    public function update(UpdateScheduleRequest $request, Schedule $schedule) {
        $schedule->update($request->validated());
        return response()->json(['status' => 'success', 'message' => 'Schedule updated', 'data' => new ScheduleResource($schedule)]);
    }
    public function destroy(Schedule $schedule) {
        $schedule->delete();
        return response()->json(['status' => 'success', 'message' => 'Schedule deleted']);
    }
}