<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Room;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        // Buat beberapa ruangan terlebih dahulu
        $rooms = [
            Room::firstOrCreate(['name' => 'Ruang A1'], ['building' => 'Gedung A', 'capacity' => 40]),
            Room::firstOrCreate(['name' => 'Ruang A2'], ['building' => 'Gedung A', 'capacity' => 40]),
            Room::firstOrCreate(['name' => 'Ruang B1'], ['building' => 'Gedung B', 'capacity' => 35]),
            Room::firstOrCreate(['name' => 'Ruang B2'], ['building' => 'Gedung B', 'capacity' => 35]),
            Room::firstOrCreate(['name' => 'Lab Komputer'], ['building' => 'Gedung C', 'capacity' => 30]),
        ];

        // Get data dari existing records atau buat default
        $classrooms = Classroom::limit(5)->get();
        if ($classrooms->count() == 0) {
            return;
        }

        $teachers = Teacher::limit(5)->get();
        if ($teachers->count() == 0) {
            return;
        }

        $subjects = Subject::limit(5)->get();
        if ($subjects->count() == 0) {
            return;
        }

        // Data jadwal yang realistis - 10 jadwal
        $schedules = [
            [
                'classroom_id' => $classrooms[0]->id,
                'teacher_id' => $teachers[0]->id,
                'subject_id' => $subjects[0]->id,
                'room_id' => $rooms[0]->id,
                'day' => 'Senin',
                'start_time' => '07:00',
                'end_time' => '08:30',
            ],
            [
                'classroom_id' => $classrooms[0]->id,
                'teacher_id' => $teachers[1]->id,
                'subject_id' => $subjects[1]->id,
                'room_id' => $rooms[1]->id,
                'day' => 'Senin',
                'start_time' => '08:30',
                'end_time' => '10:00',
            ],
            [
                'classroom_id' => $classrooms[1]->id,
                'teacher_id' => $teachers[2]->id,
                'subject_id' => $subjects[2]->id,
                'room_id' => $rooms[2]->id,
                'day' => 'Senin',
                'start_time' => '10:15',
                'end_time' => '11:45',
            ],
            [
                'classroom_id' => $classrooms[0]->id,
                'teacher_id' => $teachers[3]->id,
                'subject_id' => $subjects[3]->id,
                'room_id' => $rooms[3]->id,
                'day' => 'Senin',
                'start_time' => '11:45',
                'end_time' => '13:15',
            ],
            [
                'classroom_id' => $classrooms[2]->id,
                'teacher_id' => $teachers[4]->id,
                'subject_id' => $subjects[4]->id,
                'room_id' => $rooms[4]->id,
                'day' => 'Selasa',
                'start_time' => '07:00',
                'end_time' => '08:30',
            ],
            [
                'classroom_id' => $classrooms[1]->id,
                'teacher_id' => $teachers[0]->id,
                'subject_id' => $subjects[0]->id,
                'room_id' => $rooms[0]->id,
                'day' => 'Selasa',
                'start_time' => '08:30',
                'end_time' => '10:00',
            ],
            [
                'classroom_id' => $classrooms[0]->id,
                'teacher_id' => $teachers[1]->id,
                'subject_id' => $subjects[1]->id,
                'room_id' => $rooms[1]->id,
                'day' => 'Rabu',
                'start_time' => '10:15',
                'end_time' => '11:45',
            ],
            [
                'classroom_id' => $classrooms[2]->id,
                'teacher_id' => $teachers[2]->id,
                'subject_id' => $subjects[2]->id,
                'room_id' => $rooms[2]->id,
                'day' => 'Rabu',
                'start_time' => '13:00',
                'end_time' => '14:30',
            ],
            [
                'classroom_id' => $classrooms[3]->id,
                'teacher_id' => $teachers[3]->id,
                'subject_id' => $subjects[3]->id,
                'room_id' => $rooms[3]->id,
                'day' => 'Kamis',
                'start_time' => '07:00',
                'end_time' => '08:30',
            ],
            [
                'classroom_id' => $classrooms[0]->id,
                'teacher_id' => $teachers[4]->id,
                'subject_id' => $subjects[4]->id,
                'room_id' => $rooms[4]->id,
                'day' => 'Jumat',
                'start_time' => '08:30',
                'end_time' => '10:00',
            ],
        ];

        foreach ($schedules as $schedule) {
            Schedule::firstOrCreate(
                [
                    'classroom_id' => $schedule['classroom_id'],
                    'day' => $schedule['day'],
                    'start_time' => $schedule['start_time'],
                ],
                $schedule
            );
        }
    }
}
