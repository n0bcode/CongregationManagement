<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FormationDocument;
use App\Models\FormationEvent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    /**
     * Store a formation document.
     *
     * @param  UploadedFile  $file  The uploaded file
     * @param  FormationEvent  $event  The formation event to attach the document to
     * @param  string|null  $documentType  Optional document type label
     * @return FormationDocument The created document record
     */
    public function storeFormationDocument(
        UploadedFile $file,
        FormationEvent $event,
        ?string $documentType
    ): FormationDocument {
        // Generate unique filename: {timestamp}_{sanitized_original_name}
        $timestamp = time();
        $originalName = $file->getClientOriginalName();
        $sanitizedName = $this->sanitizeFilename($originalName);
        $uniqueFilename = "{$timestamp}_{$sanitizedName}";

        // Organize files by member and event
        $directory = "formation-documents/{$event->member_id}/{$event->id}";

        // Store file to private disk
        $filePath = $file->storeAs($directory, $uniqueFilename, 'local');

        // Create database record
        return FormationDocument::create([
            'formation_event_id' => $event->id,
            'file_name' => $originalName, // Store original name for display
            'file_path' => $filePath,
            'document_type' => $documentType,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'uploaded_by' => auth()->id(),
        ]);
    }

    /**
     * Delete a formation document.
     *
     * @param  FormationDocument  $document  The document to delete
     * @return bool Success status
     */
    public function deleteFormationDocument(FormationDocument $document): bool
    {
        // Soft delete database record (preserves audit trail)
        $document->delete();

        // Optionally delete physical file (keeping for recovery in this implementation)
        // Storage::delete($document->file_path);

        return true;
    }

    /**
     * Get the full storage path for a document.
     *
     * @param  FormationDocument  $document  The document
     * @return string The full storage path
     */
    public function getDocumentPath(FormationDocument $document): string
    {
        return Storage::path($document->file_path);
    }

    /**
     * Sanitize filename by removing special characters and spaces.
     *
     * @param  string  $filename  The original filename
     * @return string The sanitized filename
     */
    private function sanitizeFilename(string $filename): string
    {
        // Get file extension
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $nameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

        // Remove special characters, keep only alphanumeric, dash, and underscore
        $sanitized = preg_replace('/[^a-zA-Z0-9\-_]/', '-', $nameWithoutExtension);

        // Remove multiple consecutive dashes
        $sanitized = preg_replace('/-+/', '-', $sanitized);

        // Trim dashes from start and end
        $sanitized = trim($sanitized, '-');

        // Limit length to 100 characters
        $sanitized = Str::limit($sanitized, 100, '');

        return $sanitized.'.'.$extension;
    }
}
