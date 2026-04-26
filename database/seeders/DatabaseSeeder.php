<?php

namespace Database\Seeders;

use App\Models\User;
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
        // User::factory(10)->create();

        $this->call(SuperAdminSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(UserGroupSeeder::class);

        // LMS Specific Seeder
        $this->call(LmsSeeder::class);
        $this->call(ScheduleSeeder::class);
        $this->call(SchoolSeeder::class);
        $this->call(CourseMaterialSeeder::class);
    }
}
