<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reminder>
 */
class ReminderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => $this->faker->randomElement(['birthday', 'vow_expiration', 'health_check', 'other']),
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'reminder_date' => $this->faker->dateTimeBetween('now', '+1 month'),
            'member_id' => Member::factory(),
            'community_id' => Community::factory(),
            'created_by' => User::factory(),
            'is_sent' => false,
            'sent_at' => null,
        ];
    }

    /**
     * Indicate that the reminder is sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }

    /**
     * Indicate that the reminder is overdue.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'reminder_date' => now()->subDay(),
        ]);
    }
}
