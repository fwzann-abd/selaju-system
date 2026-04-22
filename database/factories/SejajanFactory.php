<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Sejajan>
 */
class SejajanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'account_id' => Account::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'photo' => null,
            'is_active' => true,
        ];
    }

    /**
     * State: inactive store.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
