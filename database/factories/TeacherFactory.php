<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeacherFactory extends Factory
{
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $name = "$firstName $lastName";

        return [
            'account_id' => Account::factory()->state([
                'username' => 'guru_'.Str::slug($name).rand(1, 999),
                'email' => 'guru.'.Str::slug($name).rand(1, 999).'@sekolah.com',
            ]),
            'school_id' => School::factory(),
            'nip' => '19'.rand(70, 95).'0101'.rand(1000, 9999),
            'name' => $name,
        ];
    }
}
