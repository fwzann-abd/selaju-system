<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\Room;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $startHour = $this->faker->numberBetween(7, 15);
        $startTime = sprintf('%02d:00', $startHour);
        $endTime = sprintf('%02d:00', $startHour + 1);

        return [
            'classroom_id' => Classroom::factory(),
            'teacher_id' => Teacher::factory(),
            'subject_id' => Subject::factory(),
            'room_id' => Room::factory(),
            'day' => $this->faker->randomElement($days),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
