<?php

namespace App\Http\Requests;

use App\Models\Schedule;
use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'classroom_id' => 'required|uuid|exists:classrooms,id',
            'teacher_id' => 'required|uuid|exists:teachers,id',
            'subject_id' => 'required|exists:subjects,id',
            'room_id' => 'nullable|exists:rooms,id',
            'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->checkTeacherConflict()) {
                $validator->errors()->add('teacher_id', 'Guru ini tidak tersedia pada jam yang sama di hari ini.');
            }

            if ($this->checkRoomConflict()) {
                $validator->errors()->add('room_id', 'Ruangan ini tidak tersedia pada jam yang sama di hari ini.');
            }
        });
    }

    protected function checkTeacherConflict(): bool
    {
        $query = Schedule::where('teacher_id', $this->teacher_id)
            ->where('day', $this->day);

        return $query->where(function ($q) {
            $q->whereBetween('start_time', [$this->start_time, $this->end_time])
                ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                ->orWhere(function ($nested) {
                    $nested->where('start_time', '<', $this->start_time)
                        ->where('end_time', '>', $this->end_time);
                });
        })->exists();
    }

    protected function checkRoomConflict(): bool
    {
        if (! $this->room_id) {
            return false;
        }

        $query = Schedule::where('room_id', $this->room_id)
            ->where('day', $this->day);

        return $query->where(function ($q) {
            $q->whereBetween('start_time', [$this->start_time, $this->end_time])
                ->orWhereBetween('end_time', [$this->start_time, $this->end_time])
                ->orWhere(function ($nested) {
                    $nested->where('start_time', '<', $this->start_time)
                        ->where('end_time', '>', $this->end_time);
                });
        })->exists();
    }
}
