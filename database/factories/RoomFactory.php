<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Ruang '.$this->faker->numberBetween(1, 12),
            'building' => $this->faker->randomElement(['Gedung A', 'Gedung B', 'Gedung C']),
            'capacity' => $this->faker->randomElement([30, 35, 40, 45]),
        ];
    }
}
