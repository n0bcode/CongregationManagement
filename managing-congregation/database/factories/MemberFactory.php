<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Member>
 */
class MemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'community_id' => \App\Models\Community::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'religious_name' => fake()->optional()->firstName(),
            'dob' => fake()->date(),
            'entry_date' => fake()->date(),
            'status' => 'Active',
        ];
    }
    public function forCommunity(\App\Models\Community $community): static
    {
        return $this->state(fn (array $attributes) => [
            'community_id' => $community->id,
        ]);
    }
}
