<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => null,
            'donor_name' => $this->faker->name(),
            'donor_ig' => '@'.$this->faker->userName(),
            'amount' => $this->faker->numberBetween(10000, 500000),
            'message' => $this->faker->sentence(),
            'payment_method' => $this->faker->randomElement(['va', 'qris', 'manual_transfer']),
            'payment_status' => 'pending',
        ];
    }

    /**
     * State: paid donation.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);
    }
}
