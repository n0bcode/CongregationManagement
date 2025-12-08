<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\Project;
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
            'project_id' => Project::factory(),
            'category' => $this->faker->randomElement(['Utilities', 'Maintenance', 'Events', 'Salaries', 'Supplies']),
            'amount' => $this->faker->numberBetween(1000, 100000), // In cents
            'date' => $this->faker->date(),
            'description' => $this->faker->sentence(),
            'created_by' => User::factory(),
            'is_locked' => false,
        ];
    }
}
