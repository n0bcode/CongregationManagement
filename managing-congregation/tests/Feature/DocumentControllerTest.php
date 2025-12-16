<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_director_can_view_documents()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);

        $response = $this->actingAs($director)->get(route('documents.index'));

        $response->assertStatus(200);
        $response->assertViewIs('documents.index');
    }

    public function test_director_can_upload_document()
    {
        Storage::fake('private');
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($director)->post(route('documents.store'), [
            'community_id' => $community->id,
            'title' => 'Test Document',
            'category' => \App\Enums\DocumentCategory::INTERNAL->value,
            'file' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $document = Document::where('title', 'Test Document')->first();
        $this->assertNotNull($document, 'Document was not created');
        $response->assertRedirect(route('documents.index'));
        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'community_id' => $community->id,
        ]);
    }

    public function test_director_cannot_view_other_community_documents()
    {
        $community1 = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community1->id]);

        $community2 = Community::factory()->create();
        $document = Document::factory()->create(['community_id' => $community2->id]);

        $response = $this->actingAs($director)->get(route('documents.show', $document));

        $response->assertStatus(403);
    }

    public function test_member_can_view_documents()
    {
        $community = Community::factory()->create();
        $member = User::factory()->create(['role' => UserRole::MEMBER, 'community_id' => $community->id]);

        $response = $this->actingAs($member)->get(route('documents.index'));
        $response->assertStatus(200);
    }

    public function test_member_cannot_upload_document()
    {
        Storage::fake('private');
        $community = Community::factory()->create();
        $member = User::factory()->create(['role' => UserRole::MEMBER, 'community_id' => $community->id]);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($member)->post(route('documents.store'), [
            'community_id' => $community->id,
            'title' => 'Unauthorized Document',
            'category' => \App\Enums\DocumentCategory::INTERNAL->value,
            'file' => $file,
        ]);

        $response->assertStatus(403);
    }
}
