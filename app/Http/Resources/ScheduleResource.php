<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day' => $this->day,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'classroom' => $this->whenLoaded('classroom', function () {
                $classroom = $this->classroom;

                return [
                    'id' => $classroom->id,
                    'name' => $classroom->name,
                    'academic_year' => $classroom->academic_year,
                    'teacher' => $this->whenLoaded('teacher', fn () => [
                        'id' => $this->teacher->id,
                        'name' => $this->teacher->name,
                    ]),
                    'students' => $classroom->relationLoaded('students')
                        ? $classroom->students->map(fn ($s) => [
                            'id' => $s->id,
                            'name' => $s->name,
                            'student_number' => $s->student_number,
                            'gender' => $s->gender,
                        ])->values()
                        : [],
                ];
            }),
            'teacher' => $this->whenLoaded('teacher', fn () => new TeacherResource($this->teacher)),
            'subject' => $this->whenLoaded('subject', fn () => new SubjectResource($this->subject)),
            'room' => $this->whenLoaded('room', fn () => [
                'id' => $this->room->id,
                'name' => $this->room->name,
                'building' => $this->room->building,
            ]),
            'is_attended' => (bool) ($this->today_attendance_count ?? false),
        ];
    }
}
