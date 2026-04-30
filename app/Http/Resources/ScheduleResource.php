<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class ScheduleResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'classroom' => new ClassroomResource($this->whenLoaded('classroom')),
            'teacher' => new TeacherResource($this->whenLoaded('teacher')),
            'subject' => new SubjectResource($this->whenLoaded('subject')),
            'room' => $this->whenLoaded('room', fn () => [
                'id' => $this->room->id,
                'name' => $this->room->name,
                'building' => $this->room->building,
            ]),
        ];
    }
}