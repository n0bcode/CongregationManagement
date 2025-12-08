<?php

namespace Database\Factories;

use App\Models\Community;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'community_id' => Community::factory(),
            'manager_id' => Member::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['planned', 'in_progress', 'completed', 'on_hold']),
            'budget' => $this->faker->randomFloat(2, 1000, 100000),
        ];
    }
}
