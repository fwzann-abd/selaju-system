<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SuperAdminSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(UserGroupSeeder::class);

        // LMS — handles school, teachers, students, classrooms,
        // schedules, rooms, materials, attendance, assignments, announcements
        $this->call(SchoolSeeder::class);
        $this->call(MassSchoolSeeder::class);
        $this->call(CourseMaterialSeeder::class);
        $this->call(NotificationSeeder::class);
    }
}
