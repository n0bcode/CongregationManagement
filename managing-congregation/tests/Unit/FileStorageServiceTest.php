<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\FormationDocument;
use App\Models\FormationEvent;
use App\Models\User;
use App\Services\FileStorageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileStorageServiceTest extends TestCase
{
    use RefreshDatabase;

    private FileStorageService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FileStorageService();
        Storage::fake('local');
    }

    public function test_store_formation_document_creates_file_and_database_record(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = FormationEvent::factory()->create();
        $file = UploadedFile::fake()->create('baptismal-certificate.pdf', 1024);

        $document = $this->service->storeFormationDocument($file, $event, 'Baptismal Certificate');

        // Assert database record created
        $this->assertDatabaseHas('formation_documents', [
            'formation_event_id' => $event->id,
            'file_name' => 'baptismal-certificate.pdf',
            'document_type' => 'Baptismal Certificate',
            'uploaded_by' => $user->id,
        ]);

        // Assert file exists in storage
        Storage::disk('local')->assertExists($document->file_path);

        // Assert document model attributes
        $this->assertEquals($event->id, $document->formation_event_id);
        $this->assertEquals('baptismal-certificate.pdf', $document->file_name);
        $this->assertEquals('Baptismal Certificate', $document->document_type);
        $this->assertEquals($file->getSize(), $document->file_size);
        $this->assertEquals($file->getMimeType(), $document->mime_type);
    }

    public function test_store_formation_document_sanitizes_filename(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = FormationEvent::factory()->create();
        $file = UploadedFile::fake()->create('My Document (2024) #1.pdf', 1024);

        $document = $this->service->storeFormationDocument($file, $event, null);

        // Assert original filename is preserved in database
        $this->assertEquals('My Document (2024) #1.pdf', $document->file_name);

        // Assert file path contains sanitized filename
        $this->assertStringContainsString('My-Document-2024-1.pdf', $document->file_path);
    }

    public function test_store_formation_document_organizes_files_by_member_and_event(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $event = FormationEvent::factory()->create();
        $file = UploadedFile::fake()->create('test.pdf', 1024);

        $document = $this->service->storeFormationDocument($file, $event, null);

        // Assert file path structure
        $expectedPath = "formation-documents/{$event->member_id}/{$event->id}";
        $this->assertStringContainsString($expectedPath, $document->file_path);
    }

    public function test_delete_formation_document_soft_deletes_record(): void
    {
        $document = FormationDocument::factory()->create();

        $result = $this->service->deleteFormationDocument($document);

        $this->assertTrue($result);
        $this->assertSoftDeleted('formation_documents', ['id' => $document->id]);
    }

    public function test_get_document_path_returns_full_storage_path(): void
    {
        Storage::fake('local');
        
        $document = FormationDocument::factory()->create([
            'file_path' => 'formation-documents/1/1/test.pdf',
        ]);

        $path = $this->service->getDocumentPath($document);

        $this->assertStringContainsString('formation-documents/1/1/test.pdf', $path);
    }
}
