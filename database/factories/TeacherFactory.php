<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TeacherFactory extends Factory
{
    public function definition(): array
    {
        $firstName = $this->faker->firstName();
        $lastName = $this->faker->lastName();
        $name = "$firstName $lastName";

        $account = Account::factory()->create([
            'username' => 'guru_'.Str::slug($name),
            'email' => 'guru.'.Str::slug($name).'@sekolah.com',
        ]);

        return [
            'account_id' => $account->uuid,
            'school_id' => $this->faker->uuid(),
            'nip' => '19'.rand(70, 95).'0101'.rand(1000, 9999),
            'name' => $name,
        ];
    }
}
