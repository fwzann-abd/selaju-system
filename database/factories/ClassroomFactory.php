<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Classroom>
 */
class ClassroomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'tingkat' => $this->faker->randomElement(['10', '11', '12']),
            'jurusan' => $this->faker->randomElement(['IPA', 'IPS', 'Bahasa']),
            'rombel' => $this->faker->randomElement(['1', '2', '3']),
            'teacher_id' => \App\Models\Teacher::factory(),
            'academic_year' => '2024/2025',
        ];
    }
}
