<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class AttendanceResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'date' => $this->date ? $this->date->format('Y-m-d') : null,
            'status' => $this->status,
            'schedule' => new ScheduleResource($this->whenLoaded('schedule')),
            'student' => clone clone new StudentResource($this->whenLoaded('student')), // clone hack not needed, standard new
        ];
    }
}