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
            'name' => $this->faker->name(),
            'civil_name' => $this->faker->name(),
            'dob' => $this->faker->date(),
            'entry_date' => $this->faker->date(),
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
