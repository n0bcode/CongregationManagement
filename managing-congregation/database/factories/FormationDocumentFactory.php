<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\FormationDocument;
use App\Models\FormationEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FormationDocument>
 */
class FormationDocumentFactory extends Factory
{
    protected $model = FormationDocument::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'formation_event_id' => FormationEvent::factory(),
            'file_name' => $this->faker->word().'.pdf',
            'file_path' => 'formation-documents/'.$this->faker->uuid().'.pdf',
            'document_type' => null,
            'file_size' => $this->faker->numberBetween(10000, 5000000),
            'mime_type' => 'application/pdf',
            'uploaded_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the document is a baptismal certificate.
     */
    public function baptismalCertificate(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'Baptismal Certificate',
            'file_name' => 'baptismal-certificate.pdf',
            'mime_type' => 'application/pdf',
        ]);
    }

    /**
     * Indicate that the document is a health report.
     */
    public function healthReport(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'Health Report',
            'file_name' => 'health-report.pdf',
            'mime_type' => 'application/pdf',
        ]);
    }

    /**
     * Indicate that the document is a vow application.
     */
    public function vowApplication(): static
    {
        return $this->state(fn (array $attributes) => [
            'document_type' => 'Vow Application',
            'file_name' => 'vow-application.pdf',
            'mime_type' => 'application/pdf',
        ]);
    }

    /**
     * Indicate that the document is an image (JPG).
     */
    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_name' => 'document.jpg',
            'file_path' => 'formation-documents/'.$this->faker->uuid().'.jpg',
            'mime_type' => 'image/jpeg',
            'file_size' => $this->faker->numberBetween(50000, 2000000),
        ]);
    }
}
