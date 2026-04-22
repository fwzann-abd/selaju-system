<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserGroup>
 */
class UserGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'status' => true,
        ];
    }

    /**
     * State: super_admin group.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'super_admin',
        ]);
    }

    /**
     * State: teacher group.
     */
    public function teacher(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'teacher',
        ]);
    }
}
