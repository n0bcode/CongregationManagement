# Story 2.5: Secure Document Upload for Formation

**Status:** Ready for Review

## Story

As a Director,
I want to upload sensitive documents like a Baptismal Certificate to a specific formation stage,
so that all required paperwork is stored securely in one place and is easily accessible when needed.

## Acceptance Criteria

1. **Upload Document to Formation Event:**

   - Given I am viewing a member's profile page with the formation timeline,
   - When I click on a formation event node (e.g., "Postulancy", "Novitiate"),
   - Then I see an "Upload Document" button or link.
   - When I click "Upload Document", a modal or form appears.
   - I can select a file (PDF, JPG, PNG) from my device.
   - I can optionally add a "Document Type" label (e.g., "Baptismal Certificate", "Health Report", "Vow Application").
   - When I submit, the file is uploaded and associated with that specific `FormationEvent`.
   - The timeline node shows a visual indicator (e.g., paperclip icon, document count badge) that documents are attached.

2. **View Uploaded Documents:**

   - Given documents have been uploaded to a formation event,
   - When I view that event's details (click to expand or view in modal),
   - Then I see a list of all uploaded documents with their names and types.
   - Each document has a "Download" or "View" link.

3. **Secure File Storage (CRITICAL):**

   - Given a document is uploaded,
   - Then the file is stored in Laravel's `private` disk (NOT `public`).
   - The file is NOT accessible via a direct public URL.
   - When I click "Download" or "View", the system checks my authorization (Policy) before serving the file.
   - Only authorized users (Formation Directress, Admin, Super Admin) can access formation documents.
   - Directors can ONLY access documents for members in their assigned community.

4. **File Validation:**

   - The system validates file types (allowed: PDF, JPG, JPEG, PNG).
   - The system validates file size (max: 5MB per file, configurable).
   - If validation fails, a clear error message is displayed.

5. **Delete Document (Optional for MVP):**
   - Given I am viewing the document list for a formation event,
   - When I click "Delete" on a document,
   - Then the file is removed from storage and the database record is deleted.
   - Only authorized users can delete documents.

## Tasks / Subtasks

- [x] **Backend Implementation**

  - [x] **Database:**
    - [x] Create `FormationDocument` model and migration (`formation_documents` table).
      - Columns: `id`, `formation_event_id` (FK), `file_name`, `file_path`, `document_type` (nullable string), `file_size` (bytes), `mime_type`, `uploaded_by` (FK to users), `deleted_at` (nullable, for soft deletes), `timestamps`.
      - Index on `formation_event_id` for query performance.
      - Enable soft deletes (`use SoftDeletes` trait in model) for audit trail preservation.
    - [x] Create `database/factories/FormationDocumentFactory.php`.
      - Helper methods for different document types (baptismal certificate, health report, vow application).
      - Generate realistic test file metadata.
  - [x] **Request Validation:**
    - [x] Create `app/Http/Requests/StoreFormationDocumentRequest.php`.
      - Validate `file` (required, file, mimes:pdf,jpg,jpeg,png, max:5120 KB).
      - Validate `document_type` (nullable, string, max:100).
      - Validate `formation_event_id` (required, exists in formation_events table).
  - [x] **Authorization:**
    - [x] Add formation permissions to RBAC system:
      - Add `FORMATION_VIEW = 'formation.view'` to `app/Enums/PermissionKey.php`.
      - Add `FORMATION_MANAGE = 'formation.manage'` to `app/Enums/PermissionKey.php`.
      - Update `database/seeders/PermissionSeeder.php` to create these permissions.
      - Assign permissions: Director (both), General (both), Member (none).
    - [x] Update `app/Policies/FormationPolicy.php` or create `FormationDocumentPolicy.php`.
      - Define `upload` method: Check user has permission to manage formation for this member.
      - Define `download` method: Check user can view this member's formation data.
      - Define `delete` method: Check user has permission to manage formation.
      - Implement community scoping: Directors can only access documents for members in their community.
  - [x] **Service Layer:**
    - [x] Create `app/Services/FileStorageService.php`.
      - **NOTE:** This is the FIRST story requiring file storage. Service must be created from scratch.
      - Implement `storeFormationDocument(UploadedFile $file, FormationEvent $event, ?string $documentType): FormationDocument`.
        - Generate unique filename: `{timestamp}_{sanitized_original_name}` to prevent overwrites.
        - Sanitize filename: remove special characters and spaces.
        - Store file to `storage/app/formation-documents/{member_id}/{event_id}/`.
        - Store original filename in database `file_name` column for display.
        - Create database record with file metadata.
      - Implement `deleteFormationDocument(FormationDocument $document): bool`.
        - Soft delete database record (preserves audit trail).
        - Optionally delete physical file (or keep for recovery).
      - Implement `getDocumentPath(FormationDocument $document): string`.
        - Return full storage path for serving.
  - [x] **Controller:**
    - [x] Create `app/Http/Controllers/FormationDocumentController.php`.
      - Implement `store(StoreFormationDocumentRequest $request, FormationEvent $event)`.
        - Authorize using Policy.
        - Delegate to `FileStorageService::storeFormationDocument()`.
        - Return redirect with success message.
      - Implement `download(FormationDocument $document)`.
        - Eager load relationships to prevent N+1: `$document->load('formationEvent.member')`.
        - Authorize using Policy.
        - Return `Storage::download()` response with proper headers.
      - Implement `destroy(FormationDocument $document)` (Optional for MVP).
        - Authorize using Policy.
        - Delegate to `FileStorageService::deleteFormationDocument()`.
        - Return redirect with success message.
  - [x] **Routing:**
    - [x] Register routes in `routes/web.php`:
      - `POST /formation-events/{event}/documents` (name: `formation.documents.store`)
      - `GET /formation-documents/{document}/download` (name: `formation.documents.download`)
      - `DELETE /formation-documents/{document}` (name: `formation.documents.destroy`) - Optional

- [x] **Frontend Implementation**

  - [x] Update `resources/views/components/feast-timeline.blade.php`.
    - Add document indicator to event nodes that have documents:
      - Use paperclip icon from Heroicons (or project's icon library).
      - Use Muted Gold color from "Sanctuary & Stone" palette for indicator.
      - Show document count badge if multiple documents attached.
    - Add "Upload Document" button/link to each event node.
    - Follow "Pastoral Dashboard" design direction for warmth and clarity.
  - [x] Create "Upload Document" Modal.
    - Use Alpine.js for modal state management.
    - Form with `file` input (accept: .pdf,.jpg,.jpeg,.png).
    - Optional `document_type` text input.
    - Submit to `formation.documents.store` route.
    - Include CSRF token and proper form encoding (`enctype="multipart/form-data"`).
  - [x] Create Document List View.
    - Display uploaded documents for each formation event.
    - Show file name, document type, upload date, uploaded by.
    - Provide "Download" link for each document.
    - Provide "Delete" button (Optional for MVP).
  - [x] Update `resources/views/members/show.blade.php`.
    - Ensure formation timeline passes document data to component.
    - Load documents relationship when fetching formation events.

- [x] **Testing**
  - [x] Create `tests/Unit/FileStorageServiceTest.php`.
    - Test document storage creates file and database record.
    - Test document deletion removes file and database record.
    - Test file path generation.
  - [x] Create `tests/Feature/FormationDocumentTest.php`.
    - Test uploading document as authorized user (Formation Directress, Admin).
    - Test uploading document as unauthorized user (403 Forbidden).
    - Test uploading document for member in different community as Director (403 Forbidden).
    - Test file validation (invalid type, oversized file).
    - Test downloading document as authorized user.
    - Test downloading document as unauthorized user (403 Forbidden).
    - Test deleting document as authorized user (Optional).
    - Test that files are stored in private disk (not publicly accessible).

## Dev Notes

### Quick Reference

| Component          | Action | File Path                                                       |
| ------------------ | ------ | --------------------------------------------------------------- |
| Model              | CREATE | `app/Models/FormationDocument.php`                              |
| Migration          | CREATE | `database/migrations/xxxx_create_formation_documents_table.php` |
| Factory            | CREATE | `database/factories/FormationDocumentFactory.php`               |
| Request            | CREATE | `app/Http/Requests/StoreFormationDocumentRequest.php`           |
| Policy             | UPDATE | `app/Policies/FormationPolicy.php`                              |
| Service            | CREATE | `app/Services/FileStorageService.php`                           |
| Controller         | CREATE | `app/Http/Controllers/FormationDocumentController.php`          |
| Timeline Component | UPDATE | `resources/views/components/feast-timeline.blade.php`           |
| Member Show View   | UPDATE | `resources/views/members/show.blade.php`                        |
| Routes             | UPDATE | `routes/web.php`                                                |

- **Security (CRITICAL):**

  - **NEVER store sensitive documents in `storage/app/public`**. Always use `Storage::disk('private')` or the default disk configured to `local` (which maps to `storage/app`).
  - **Authorization is MANDATORY**. Every document download MUST check the Policy before serving the file.
  - **Community Scoping:** Directors can ONLY upload/download documents for members in their assigned community. Use the same scoping pattern from Story 1.5 and previous member stories.

- **Coding Standards (CRITICAL):**

  - **Strict Types:** ALL new PHP files MUST start with `declare(strict_types=1);`.
  - **Validation:** ALWAYS use FormRequests. NEVER validate in the Controller.
  - **Authorization:** ALWAYS use Policies. Check `can('upload', ...)` or `authorize()` in Controller.
  - **Service Layer:** File storage logic MUST live in `FileStorageService`. Do not put file handling logic in the Controller or Model.

- **Architecture Patterns:**

  - **File Storage:** Use Laravel's `Storage` facade with the `private` disk.
    - Store files in organized subdirectories: `formation-documents/{member_id}/{event_id}/`.
    - Store original filename and metadata in the database for display purposes.
  - **File Serving:** Use `Storage::download($path, $name)` to serve files with proper headers and force download.
  - **Relationships:**
    - `FormationEvent hasMany FormationDocument`
    - `FormationDocument belongsTo FormationEvent`
    - `FormationDocument belongsTo User` (uploaded_by)

- **UX Standards:**

  - **Feedback:** Use `session()->flash` to show success/error messages after upload/delete.
  - **Visual Indicators:** Show a clear visual indicator (icon, badge) on timeline nodes that have documents attached.
  - **File Size Display:** Show file size in human-readable format (KB, MB) in the document list.
  - **Progress Indicator:** Consider showing upload progress for large files (optional, can use Livewire `wire:loading` or Alpine).

- **File Storage Configuration:**
  - Ensure `config/filesystems.php` has `'default' => env('FILESYSTEM_DISK', 'local')`.
  - The `local` disk points to `storage/app`, which is NOT publicly accessible.
  - Files in `storage/app/private` are served only through authenticated controller routes.

### Project Structure Notes

- **Model:** `app/Models/FormationDocument.php`
- **Request:** `app/Http/Requests/StoreFormationDocumentRequest.php`
- **Policy:** `app/Policies/FormationDocumentPolicy.php` (or extend `FormationPolicy.php`)
- **Service:** `app/Services/FileStorageService.php`
- **Controller:** `app/Http/Controllers/FormationDocumentController.php`
- **Migration:** `database/migrations/xxxx_xx_xx_create_formation_documents_table.php`
- **View Component:** Update `resources/views/components/feast-timeline.blade.php`
- **Views:** Update `resources/views/members/show.blade.php`

### References

- **Epics:** [Story 2.5 in Epics](docs/epics.md#story-25-secure-document-upload-for-formation)
- **PRD:** FR8 (Upload Documents to Formation Stage)
- **Architecture:**
  - [File Management](docs/architecture.md#cross-cutting-concerns-identified) - Secure storage and serving of private documents
  - [Service Layer Pattern](docs/architecture.md#service-boundaries)
  - [RBAC](docs/architecture.md#authentication--security) - Policy-based authorization
  - [Data Privacy](docs/architecture.md#authentication--security) - Private storage for sensitive documents
- **UX:** [Feast Timeline Component](docs/ux-design-specification.md#2-the-feast-timeline-x-feast-timeline)

### Previous Story Learnings (from Story 2.4)

- **FormationEvent Model:** Already exists with relationship to `Member`.
- **FormationPolicy:** Already exists with `create` and `view` methods. Extend this for document operations.
- **Timeline Component:** `feast-timeline.blade.php` already renders formation events. Enhance it to show document indicators.
- **Authorization Pattern:** Use the same pattern from Story 2.4:
  - Gate check for role-based permissions (e.g., `Gate::allows('manage-formation')`).
  - Policy check for community-scoped access (e.g., `$this->authorize('upload', $formationEvent)`).
- **Testing Pattern:** Follow the comprehensive testing approach from Story 2.4 (Unit tests for Service, Feature tests for Controller).

### Git Intelligence (Recent Commits)

Recent commits show the pattern for implementing formation features:

- **Story 2.4 (Visual Formation Timeline):** Created `FormationEvent` model, `FormationService`, `FormationController`, and comprehensive tests.
- **Story 2.3 (Member Search):** Implemented search functionality with proper scoping and validation.
- **Story 2.2 (Edit Member):** Created `MemberStatus` enum and refactored forms.
- **Story 2.1 (Create Member):** Established `MemberController` patterns and community scoping.
- **Story 1.5 (House-Scoped Access):** Implemented global scopes for automatic community filtering.

**Key Patterns to Follow:**

- Use Enums for type-safe constants (consider `DocumentType` enum if document types are fixed).
- Use Factories for test data generation.
- Use FormRequests for all validation.
- Use Policies for all authorization.
- Use Services for complex business logic (file storage).
- Use `declare(strict_types=1);` in all PHP files.
- Write comprehensive tests (Unit + Feature).

### Technical Implementation Details

**File Storage Structure:**

```
storage/app/
  └── formation-documents/
      └── {member_id}/
          └── {formation_event_id}/
              ├── 1701612345_baptismal-certificate.pdf
              ├── 1701612456_health-report.pdf
              └── 1701612567_vow-application.pdf
```

**Filename Pattern:** `{timestamp}_{sanitized_original_name}` prevents overwrites and maintains uniqueness.

---

**Database Schema:**

```sql
CREATE TABLE formation_documents (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    formation_event_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    document_type VARCHAR(100) NULL,
    file_size INT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (formation_event_id) REFERENCES formation_events(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    INDEX idx_formation_event_id (formation_event_id)
);
```

---

**Authorization Logic:**

```php
// FormationDocumentPolicy.php
public function upload(User $user, FormationEvent $event): bool {
    // Super admin bypass
    if ($user->role === UserRole::SUPER_ADMIN) {
        return true;
    }

    // Check permission
    if (!$user->hasPermission(PermissionKey::FORMATION_MANAGE)) {
        return false;
    }

    // Community scoping for Directors
    if ($user->role === UserRole::DIRECTOR) {
        return $event->member->community_id === $user->community_id;
    }

    // General role can access all
    return true;
}

public function download(User $user, FormationDocument $document): bool {
    // Super admin bypass
    if ($user->role === UserRole::SUPER_ADMIN) {
        return true;
    }

    // Check permission
    if (!$user->hasPermission(PermissionKey::FORMATION_VIEW)) {
        return false;
    }

    // Community scoping for Directors
    if ($user->role === UserRole::DIRECTOR) {
        return $document->formationEvent->member->community_id === $user->community_id;
    }

    // General role can access all
    return true;
}
```

---

**File Upload Flow:**

1. User clicks "Upload Document" on a formation event node.
2. Modal opens with file input and document type field.
3. User selects file and submits form.
4. `StoreFormationDocumentRequest` validates file (type, size).
5. `FormationDocumentController::store()` authorizes the request.
6. `FileStorageService::storeFormationDocument()` stores file and creates database record.
7. Controller redirects back with success message.
8. Timeline updates to show document indicator.

---

**File Download Flow:**

1. User clicks "Download" link on a document.
2. Request hits `FormationDocumentController::download()`.
3. Controller loads `FormationDocument` with relationships (`formationEvent.member`).
4. Controller authorizes the request using Policy.
5. Controller uses `Storage::download()` to serve the file.
6. Browser downloads the file with original filename.

### Critical Pitfalls

- ❌ **Public storage** - Use `Storage::disk('private')` ONLY, never `public`
- ❌ **Unauthenticated downloads** - ALWAYS check Policy before serving files
- ❌ **Path exposure** - Route by document ID, never expose storage paths in URLs
- ❌ **Missing validation** - Validate file type/size before storage to prevent malicious uploads
- ❌ **Orphaned files** - Use soft deletes or model events to maintain audit trail
- ❌ **Broken scoping** - Directors see ONLY their community's documents (check `community_id`)
- ❌ **N+1 queries** - Eager load `formationEvent.member` relationships before authorization checks

## Dev Agent Record

### Context Reference

- `docs/epics.md` - Story 2.5 requirements and business context
- `docs/prd.md` - FR8 (Upload Documents to Formation Stage)
- `docs/architecture.md` - File storage patterns, RBAC, Service Layer
- `docs/ux-design-specification.md` - Timeline component design
- `docs/sprint-artifacts/stories/2-4-visual-formation-timeline.md` - Previous story for formation context
- `docs/sprint-artifacts/stories/2-3-member-search.md` - Scoping and validation patterns
- `docs/sprint-artifacts/stories/1-5-house-scoped-data-access-for-directors.md` - Community scoping patterns

### Agent Model Used

Google Gemini 2.0 Flash (Thinking - Experimental)

### Debug Log References

_To be filled by implementing agent_

### Completion Notes List

- ✅ Created `FormationDocument` model with soft deletes, relationships to `FormationEvent` and `User`
- ✅ Created migration for `formation_documents` table with all required columns and indexes
- ✅ Created `FormationDocumentFactory` with helper methods for different document types (baptismal certificate, health report, vow application, image)
- ✅ Updated `FormationEvent` model to include `hasMany` relationship to documents
- ✅ Created `StoreFormationDocumentRequest` with comprehensive file validation (type, size) and custom error messages
- ✅ Added `FORMATION_VIEW` and `FORMATION_MANAGE` permissions to `PermissionKey` enum
- ✅ Updated `PermissionSeeder` to create formation permissions and assign to Director and General roles
- ✅ Extended `FormationPolicy` with `uploadDocument`, `downloadDocument`, and `deleteDocument` methods implementing community scoping
- ✅ Created `FileStorageService` (first file storage service in project) with methods for storing, deleting, and retrieving documents
  - Implements unique filename generation with timestamp and sanitization
  - Organizes files by member_id and event_id in directory structure
  - Soft deletes database records while preserving audit trail
- ✅ Created `FormationDocumentController` with store, download, and destroy methods
  - Proper authorization checks using Policy
  - Eager loading to prevent N+1 queries
  - Delegates file operations to FileStorageService
- ✅ Added routes for document upload, download, and delete operations
- ✅ Updated `feast-timeline.blade.php` component with:
  - Document count badges on timeline nodes
  - Paperclip icon indicators for nodes with documents
  - Upload button for each event (with authorization check)
  - View documents button showing count
- ✅ Created upload document modal for each formation event with:
  - File input with proper accept types and styling
  - Optional document type field
  - Proper form encoding for multipart/form-data
- ✅ Created view documents modal for each formation event with:
  - Document list showing file name, type, size, upload date, and uploader
  - Download and delete buttons with authorization checks
  - Confirmation dialog for delete action
- ✅ Updated `members/show.blade.php` to eager load documents relationship
- ✅ Created comprehensive unit tests for `FileStorageService` (5 tests)
- ✅ Created comprehensive feature tests for `FormationDocumentTest` (11 tests covering authorization, validation, community scoping, and private storage)

### File List

**Created:**

- `database/migrations/2025_12_03_103000_create_formation_documents_table.php`
- `app/Models/FormationDocument.php`
- `database/factories/FormationDocumentFactory.php`
- `app/Http/Requests/StoreFormationDocumentRequest.php`
- `app/Services/FileStorageService.php`
- `app/Http/Controllers/FormationDocumentController.php`
- `tests/Unit/FileStorageServiceTest.php`
- `tests/Feature/FormationDocumentTest.php`

**Modified:**

- `app/Enums/PermissionKey.php` - Added FORMATION_VIEW and FORMATION_MANAGE permissions
- `database/seeders/PermissionSeeder.php` - Added formation permissions and role assignments
- `app/Policies/FormationPolicy.php` - Added uploadDocument, downloadDocument, deleteDocument methods
- `app/Models/FormationEvent.php` - Added documents() hasMany relationship
- `routes/web.php` - Added formation document routes
- `resources/views/components/feast-timeline.blade.php` - Added document indicators and upload/view buttons
- `resources/views/members/show.blade.php` - Added upload and view document modals, eager load documents
- `docs/sprint-artifacts/sprint-status.yaml` - Updated story status to in-progress then ready for review
