<?php

namespace App\Http\Controllers;

use App\Enums\DocumentCategory;
use App\Models\Community;
use App\Models\Document;
use App\Models\Folder;
use App\Models\Member;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Display a listing of documents
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Document::class);

        $query = Document::with(['folder', 'community', 'member', 'uploader'])
            ->latest();

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->category($request->category);
        }

        if ($request->filled('folder_id')) {
            $query->inFolder($request->folder_id);
        }

        if ($request->filled('community_id')) {
            $query->forCommunity($request->community_id);
        }

        if ($request->filled('member_id')) {
            $query->forMember($request->member_id);
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        $documents = $query->paginate(20);

        // Get data for filters
        $folders = Folder::roots()
            ->with('children')
            ->orderBy('name')
            ->get();

        $communities = Community::orderBy('name')->get();
        $categories = DocumentCategory::cases();

        return view('documents.index', compact(
            'documents',
            'folders',
            'communities',
            'categories'
        ));
    }

    /**
     * Show the form for creating a new document
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Document::class);

        $folders = Folder::roots()
            ->with('children')
            ->orderBy('name')
            ->get();

        $communities = Community::orderBy('name')->get();
        $members = Member::orderBy('first_name')->get();
        $categories = DocumentCategory::cases();

        // Pre-select folder if provided
        $selectedFolderId = $request->query('folder_id');
        $selectedMemberId = $request->query('member_id');

        return view('documents.create', compact(
            'folders',
            'communities',
            'members',
            'categories',
            'selectedFolderId',
            'selectedMemberId'
        ));
    }

    /**
     * Store a newly created document
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Document::class);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:'.implode(',', array_column(DocumentCategory::cases(), 'value')),
            'folder_id' => 'nullable|exists:folders,id',
            'community_id' => 'nullable|exists:communities,id',
            'member_id' => 'nullable|exists:members,id',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Upload file
        $file = $request->file('file');
        $path = $file->store('documents', 'private');

        // Create document record
        $document = Document::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'category' => $validated['category'],
            'folder_id' => $validated['folder_id'] ?? null,
            'community_id' => $validated['community_id'] ?? null,
            'member_id' => $validated['member_id'] ?? null,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('documents.show', $document)
            ->with('status', 'Document uploaded successfully.');
    }

    /**
     * Display the specified document
     */
    public function show(Document $document): View
    {
        $this->authorize('view', $document);

        $document->load(['folder', 'community', 'member', 'uploader']);

        return view('documents.show', compact('document'));
    }

    /**
     * Show the form for editing the specified document
     */
    public function edit(Document $document): View
    {
        $this->authorize('update', $document);

        $folders = Folder::roots()
            ->with('children')
            ->orderBy('name')
            ->get();

        $communities = Community::orderBy('name')->get();
        $members = Member::orderBy('first_name')->get();
        $categories = DocumentCategory::cases();

        return view('documents.edit', compact(
            'document',
            'folders',
            'communities',
            'members',
            'categories'
        ));
    }

    /**
     * Update the specified document
     */
    public function update(Request $request, Document $document): RedirectResponse
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:'.implode(',', array_column(DocumentCategory::cases(), 'value')),
            'folder_id' => 'nullable|exists:folders,id',
            'community_id' => 'nullable|exists:communities,id',
            'member_id' => 'nullable|exists:members,id',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Update file if new one uploaded
        if ($request->hasFile('file')) {
            // Delete old file
            $document->deleteFile();

            // Upload new file
            $file = $request->file('file');
            $path = $file->store('documents', 'private');

            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['mime_type'] = $file->getMimeType();
            $validated['file_size'] = $file->getSize();
        }

        $document->update($validated);

        return redirect()
            ->route('documents.show', $document)
            ->with('status', 'Document updated successfully.');
    }

    /**
     * Remove the specified document
     */
    public function destroy(Document $document): RedirectResponse
    {
        $this->authorize('delete', $document);

        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('status', 'Document deleted successfully.');
    }

    /**
     * Download the specified document
     */
    public function download(Request $request, Document $document): StreamedResponse
    {
        // Verify signed URL if present
        if ($request->hasValidSignature()) {
            // Signed URL is valid, allow download
        } else {
            // Regular download, check authorization
            $this->authorize('view', $document);
        }

        if (! $document->fileExists()) {
            abort(404, 'File not found.');
        }

        return Storage::disk('private')->download(
            $document->file_path,
            $document->file_name
        );
    }
}
