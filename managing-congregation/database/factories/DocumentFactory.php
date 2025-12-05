<?php

namespace Database\Factories;

use App\Enums\DocumentCategory;
use App\Models\Community;
use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'file_path' => 'documents/' . $this->faker->uuid . '.pdf',
            'file_name' => $this->faker->word . '.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => $this->faker->numberBetween(1024, 10485760),
            'category' => $this->faker->randomElement(DocumentCategory::cases()),
            'community_id' => Community::factory(),
            'uploaded_by' => User::factory(),
        ];
    }
}
