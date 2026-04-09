<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nomor_participant' => $this->faker->unique()->numerify('P########'),
            'username' => $this->faker->unique()->userName(),
            'birth_date' => $this->faker->date(),
            'no_telp' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'photo' => null,
            'password' => bcrypt('password'),
            'is_active' => true,
        ];
    }
}
