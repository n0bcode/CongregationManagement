<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'community_id' => Community::factory(),
            'category' => $this->faker->randomElement(['Food', 'Utilities', 'Maintenance', 'Supplies', 'Other']),
            'amount' => $this->faker->numberBetween(1000, 100000), // $10.00 to $1000.00
            'date' => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'created_by' => User::factory(),
            'is_locked' => false,
            'locked_at' => null,
            'locked_by' => null,
        ];
    }

    /**
     * Indicate that the expense is locked.
     */
    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_locked' => true,
            'locked_at' => now(),
            'locked_by' => User::factory(),
        ]);
    }
}
