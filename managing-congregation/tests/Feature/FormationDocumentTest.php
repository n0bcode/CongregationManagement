<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\FormationDocument;
use App\Models\FormationEvent;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormationDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_authorized_user_can_upload_document(): void
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);
        $file = UploadedFile::fake()->create('baptismal-certificate.pdf', 1024);

        $response = $this->actingAs($user)->post(route('formation.documents.store', $event), [
            'formation_event_id' => $event->id,
            'file' => $file,
            'document_type' => 'Baptismal Certificate',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('formation_documents', [
            'formation_event_id' => $event->id,
            'document_type' => 'Baptismal Certificate',
            'uploaded_by' => $user->id,
        ]);

        $document = FormationDocument::where('formation_event_id', $event->id)->first();
        Storage::disk('local')->assertExists($document->file_path);
    }

    public function test_unauthorized_user_cannot_upload_document(): void
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);
        $event = FormationEvent::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 1024);

        $response = $this->actingAs($user)->post(route('formation.documents.store', $event), [
            'formation_event_id' => $event->id,
            'file' => $file,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('formation_documents', 0);
    }

    public function test_director_cannot_upload_document_for_member_in_different_community(): void
    {
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community1->id,
        ]);

        $member = Member::factory()->create(['community_id' => $community2->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);
        $file = UploadedFile::fake()->create('test.pdf', 1024);

        $response = $this->actingAs($user)->post(route('formation.documents.store', $event), [
            'formation_event_id' => $event->id,
            'file' => $file,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('formation_documents', 0);
    }

    public function test_file_validation_rejects_invalid_file_type(): void
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);
        $file = UploadedFile::fake()->create('document.txt', 1024);

        $response = $this->actingAs($user)->post(route('formation.documents.store', $event), [
            'formation_event_id' => $event->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('formation_documents', 0);
    }

    public function test_file_validation_rejects_oversized_file(): void
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);
        $file = UploadedFile::fake()->create('large-document.pdf', 6000); // 6MB > 5MB limit

        $response = $this->actingAs($user)->post(route('formation.documents.store', $event), [
            'formation_event_id' => $event->id,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('formation_documents', 0);
    }

    public function test_authorized_user_can_download_document(): void
    {
        Storage::fake('local');

        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);

        // Create a real file in storage
        $filePath = 'formation-documents/'.$member->id.'/'.$event->id.'/test.pdf';
        Storage::disk('local')->put($filePath, 'test content');

        $document = FormationDocument::factory()->create([
            'formation_event_id' => $event->id,
            'file_path' => $filePath,
            'file_name' => 'test.pdf',
        ]);

        $response = $this->actingAs($user)->get(route('formation.documents.download', $document));

        $response->assertOk();
        $response->assertDownload('test.pdf');
    }

    public function test_unauthorized_user_cannot_download_document(): void
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);
        $document = FormationDocument::factory()->create();

        $response = $this->actingAs($user)->get(route('formation.documents.download', $document));

        $response->assertForbidden();
    }

    public function test_director_cannot_download_document_for_member_in_different_community(): void
    {
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community1->id,
        ]);

        $member = Member::factory()->create(['community_id' => $community2->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);
        $document = FormationDocument::factory()->create(['formation_event_id' => $event->id]);

        $response = $this->actingAs($user)->get(route('formation.documents.download', $document));

        $response->assertForbidden();
    }

    public function test_authorized_user_can_delete_document(): void
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);
        $document = FormationDocument::factory()->create(['formation_event_id' => $event->id]);

        $response = $this->actingAs($user)->delete(route('formation.documents.destroy', $document));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSoftDeleted('formation_documents', ['id' => $document->id]);
    }

    public function test_files_are_stored_in_private_disk(): void
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        $event = FormationEvent::factory()->create(['member_id' => $member->id]);
        $file = UploadedFile::fake()->create('test.pdf', 1024);

        $this->actingAs($user)->post(route('formation.documents.store', $event), [
            'formation_event_id' => $event->id,
            'file' => $file,
        ]);

        $document = FormationDocument::where('formation_event_id', $event->id)->first();

        // Assert file is in formation-documents directory (private storage)
        $this->assertStringContainsString('formation-documents', $document->file_path);

        // Assert file exists in local (private) disk
        Storage::disk('local')->assertExists($document->file_path);

        // Assert file does NOT exist in public disk
        Storage::disk('public')->assertMissing($document->file_path);
    }

    public function test_general_role_can_access_all_community_documents(): void
    {
        Storage::fake('local');

        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        $user = User::factory()->create(['role' => UserRole::GENERAL]);

        $member1 = Member::factory()->create(['community_id' => $community1->id]);
        $member2 = Member::factory()->create(['community_id' => $community2->id]);

        $event1 = FormationEvent::factory()->create(['member_id' => $member1->id]);
        $event2 = FormationEvent::factory()->create(['member_id' => $member2->id]);

        $file1 = UploadedFile::fake()->create('test1.pdf', 1024);
        $file2 = UploadedFile::fake()->create('test2.pdf', 1024);

        // Upload to both communities
        $response1 = $this->actingAs($user)->post(route('formation.documents.store', $event1), [
            'formation_event_id' => $event1->id,
            'file' => $file1,
        ]);

        $response2 = $this->actingAs($user)->post(route('formation.documents.store', $event2), [
            'formation_event_id' => $event2->id,
            'file' => $file2,
        ]);

        $response1->assertRedirect();
        $response2->assertRedirect();
        $this->assertDatabaseCount('formation_documents', 2);
    }
}
