<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceSession>
 */
class DeviceSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'];
        $os = ['Windows 10', 'Windows 11', 'MacOS', 'Ubuntu', 'iOS', 'Android'];
        $deviceTypes = ['web', 'mobile', 'tablet'];
        $locations = ['Jakarta, Indonesia', 'Bandung, Indonesia', 'Surabaya, Indonesia', 'Yogyakarta, Indonesia'];

        $browser = fake()->randomElement($browsers);
        $osName = fake()->randomElement($os);

        return [
            'account_id' => \App\Models\Account::factory(),
            'device_id' => fake()->uuid(),
            'device_name' => $browser . ' on ' . $osName,
            'device_type' => fake()->randomElement($deviceTypes),
            'browser' => $browser . ' ' . fake()->numberBetween(100, 130),
            'os' => $osName,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'location' => fake()->randomElement($locations),
            'last_activity' => fake()->dateTimeBetween('-7 days', 'now'),
            'is_active' => fake()->boolean(80), // 80% chance active
        ];
    }

    /**
     * Indicate that the device session is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'last_activity' => now(),
        ]);
    }

    /**
     * Indicate that the device session is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'last_activity' => fake()->dateTimeBetween('-30 days', '-7 days'),
        ]);
    }
}
