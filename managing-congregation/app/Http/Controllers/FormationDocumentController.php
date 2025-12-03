<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreFormationDocumentRequest;
use App\Models\FormationDocument;
use App\Models\FormationEvent;
use App\Services\FileStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FormationDocumentController extends Controller
{
    public function __construct(
        private readonly FileStorageService $fileStorageService
    ) {}

    /**
     * Store a newly uploaded document.
     */
    public function store(
        StoreFormationDocumentRequest $request,
        FormationEvent $event
    ): RedirectResponse {
        // Authorize using Policy
        $this->authorize('uploadDocument', $event);

        // Store the document
        $document = $this->fileStorageService->storeFormationDocument(
            $request->file('file'),
            $event,
            $request->input('document_type')
        );

        return redirect()
            ->back()
            ->with('success', 'Document uploaded successfully.');
    }

    /**
     * Download a formation document.
     */
    public function download(FormationDocument $document): StreamedResponse
    {
        // Eager load relationships to prevent N+1
        $document->load('formationEvent.member');

        // Authorize using Policy
        $this->authorize('downloadDocument', $document);

        // Serve the file with original filename
        return Storage::download($document->file_path, $document->file_name);
    }

    /**
     * Delete a formation document.
     */
    public function destroy(FormationDocument $document): RedirectResponse
    {
        // Eager load relationships for authorization
        $document->load('formationEvent.member');

        // Authorize using Policy
        $this->authorize('deleteDocument', $document);

        // Delete the document
        $this->fileStorageService->deleteFormationDocument($document);

        return redirect()
            ->back()
            ->with('success', 'Document deleted successfully.');
    }
}
