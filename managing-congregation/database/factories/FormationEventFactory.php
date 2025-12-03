<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\FormationStage;
use App\Models\FormationEvent;
use App\Models\Member;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormationEvent>
 */
class FormationEventFactory extends Factory
{
    protected $model = FormationEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'member_id' => Member::factory(),
            'stage' => fake()->randomElement(FormationStage::cases()),
            'started_at' => fake()->dateTimeBetween('-5 years', 'now'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the formation event is for Postulancy stage.
     */
    public function postulancy(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => FormationStage::Postulancy,
        ]);
    }

    /**
     * Indicate that the formation event is for Novitiate stage.
     */
    public function novitiate(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => FormationStage::Novitiate,
        ]);
    }

    /**
     * Indicate that the formation event is for First Vows stage.
     */
    public function firstVows(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => FormationStage::FirstVows,
        ]);
    }

    /**
     * Indicate that the formation event is for Final Vows stage.
     */
    public function finalVows(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => FormationStage::FinalVows,
        ]);
    }
}
