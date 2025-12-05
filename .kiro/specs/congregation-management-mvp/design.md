# Design Document

## Overview

The Managing the Congregation system is a comprehensive Laravel-based web application designed to modernize religious order administration. The system follows a Multi-Page Application (MPA) architecture using Laravel Breeze, Blade templates, Tailwind CSS, and Alpine.js for interactivity.

The design prioritizes:

- **Pastoral Efficiency**: Removing administrative burden from religious life
- **Accessibility**: WCAG 2.1 Level AA compliance for elderly users
- **Security**: Role-Based Access Control with community-level data scoping
- **Performance**: Sub-2-second page loads on 4G networks
- **Maintainability**: Standard Laravel patterns and comprehensive testing

## Architecture

### Technology Stack

- **Backend**: PHP 8.2+ with Laravel 11
- **Database**: MySQL 8.0 with strict relational modeling
- **Frontend**: Blade templates + Tailwind CSS + Alpine.js
- **Build Tools**: Vite for asset compilation
- **Containerization**: Docker with Laravel Sail
- **Testing**: Pest for unit and feature tests

### System Components

1. **Authentication & Authorization**

   - Laravel Breeze for authentication scaffolding
   - Custom RBAC with type-safe enums (UserRole, PermissionKey)
   - Policy-based authorization with super admin bypass
   - Community-scoped data access via Global Scopes
   - Production-ready caching layer for permissions
   - Route-based permission auto-discovery

2. **Member Management**

   - Core entity with comprehensive profile data
   - Formation lifecycle tracking
   - Health records with secure document storage
   - Skills taxonomy (pastoral, practical, special)
   - Service history with timeline visualization

3. **Financial Management**

   - Expense tracking with receipt uploads
   - Monthly report generation (PDF)
   - Period locking for data immutability
   - Community-level budgeting
   - General Treasurer oversight

4. **Document Management**

   - Hierarchical folder structure
   - Full-text search capabilities
   - Role-based access control
   - Temporary signed URLs for downloads
   - Document categorization (appointment, transfer, vows, etc.)

5. **Notification System**

   - Time-based reminders (vow expiration, birthdays)
   - Event-based notifications (formation milestones)
   - Configurable reminder periods
   - Dashboard integration

6. **Reporting & Analytics**

   - Visual dashboards with charts
   - Demographic analysis
   - Export to PDF/Excel
   - Filtering by community, date range, formation stage

7. **Audit Logging**

   - Immutable audit trail
   - User, timestamp, and change tracking
   - Tamper-evident exports
   - Comprehensive filtering

8. **Celebration System**
   - Template-based card generation
   - Photo integration
   - Multiple delivery options (download, email)
   - Upcoming celebrations widget

## Components and Interfaces

### Models

#### User

```php
class User extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'community_id'];
    protected $casts = ['role' => UserRole::class];

    public function community(): BelongsTo;
    public function hasPermission(PermissionKey|string $permission): bool;
    public function isSuperAdmin(): bool;
}
```

#### Member

```php
class Member extends Model
{
    protected $fillable = [
        'civil_name', 'religious_name', 'date_of_birth', 'gender',
        'entry_date', 'current_stage', 'community_id', 'status',
        'profile_photo_path', 'email', 'phone', 'address',
        'emergency_contact_name', 'emergency_contact_phone'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'entry_date' => 'date',
        'current_stage' => FormationStage::class,
        'status' => MemberStatus::class
    ];

    public function community(): BelongsTo;
    public function formationEvents(): HasMany;
    public function healthRecords(): HasMany;
    public function skills(): HasMany;
    public function assignments(): HasMany;
    public function timeline(): Collection; // Computed property
}
```

#### Expense

```php
class Expense extends Model
{
    protected $fillable = [
        'community_id', 'category', 'amount', 'date',
        'description', 'receipt_path', 'created_by', 'is_locked'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer', // Store in cents
        'is_locked' => 'boolean'
    ];

    public function community(): BelongsTo;
    public function creator(): BelongsTo;
}
```

#### Document

```php
class Document extends Model
{
    protected $fillable = [
        'title', 'category', 'file_path', 'folder_id',
        'member_id', 'community_id', 'uploaded_by'
    ];

    protected $casts = [
        'category' => DocumentCategory::class
    ];

    public function folder(): BelongsTo;
    public function member(): BelongsTo;
    public function community(): BelongsTo;
    public function uploader(): BelongsTo;
    public function getTemporaryUrl(int $minutes = 60): string;
}
```

#### AuditLog

```php
class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'auditable_type', 'auditable_id',
        'old_values', 'new_values', 'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array'
    ];

    public function user(): BelongsTo;
    public function auditable(): MorphTo;
}
```

### Services

#### FormationService

```php
class FormationService
{
    public function calculateNextStageDate(Member $member): ?Carbon;
    public function canAdvanceToStage(Member $member, FormationStage $stage): bool;
    public function advanceStage(Member $member, FormationStage $newStage, array $data): FormationEvent;
    public function getUpcomingVowExpirations(int $days = 30): Collection;
}
```

#### FinancialService

```php
class FinancialService
{
    public function generateMonthlyReport(Community $community, Carbon $month): string; // Returns PDF path
    public function lockPeriod(Community $community, Carbon $month): void;
    public function aggregateExpensesByCategory(Community $community, Carbon $start, Carbon $end): Collection;
}
```

#### PdfService

```php
class PdfService
{
    public function generateMemberProfile(Member $member): string;
    public function generateFinancialReport(array $data): string;
    public function generateCelebrationCard(Member $member, string $template): string;
}
```

#### NotificationService

```php
class NotificationService
{
    public function scheduleVowReminders(FormationEvent $event): void;
    public function sendBirthdayNotifications(): void;
    public function notifyFormationDirectress(Member $member, string $message): void;
}
```

#### AuditService

````php
class AuditService
{
    public function log(string $action, Model $model, ?array $oldValues = null): AuditLog;
    public function generateTamperEvidentReport(Collection $logs): string;
}

#### PermissionService

```php
interface PermissionServiceInterface
{
    public function assignPermissionsToRole(UserRole $role, array $permissionKeys): void;
    public function getRolePermissions(UserRole $role): Collection;
    public function syncPermissionsFromRoutes(): array;
    public function invalidateRoleCache(UserRole $role): void;
    public function invalidateUserCache(int $userId): void;
}
````

#### CacheManager

```php
interface CacheManagerInterface
{
    public function getUserPermissions(int $userId): ?array;
    public function cacheUserPermissions(int $userId, array $permissions): void;
    public function invalidateUserCache(int $userId): void;
    public function invalidateRoleCache(UserRole $role): void;
}
```

#### RouteScanner

```php
interface RouteScannerInterface
{
    public function scanRoutes(): Collection;
    public function extractPermissionFromMiddleware(array $middleware): ?string;
}
```

````

### Blade Components

#### Status Card

```blade
<x-status-card
    variant="peace|attention|pending"
    icon="🕊️"
    title="Members in Formation"
    :value="$count"
/>
````

#### Ledger Row

```blade
<x-ledger-row
    :date="$expense->date"
    :description="$expense->description"
    :category="$expense->category"
    :amount="money($expense->amount)"
/>
```

#### Timeline Node

```blade
<x-timeline-node
    :event="$event"
    :isPast="$event->isPast()"
    :isToday="$event->isToday()"
/>
```

#### Button

```blade
<x-button
    variant="primary|secondary|danger"
    size="sm|md|lg"
    type="button|submit"
>
    {{ $slot }}
</x-button>
```

#### Navigation Dropdown

```blade
<x-nav-dropdown
    label="Management"
    :active="request()->routeIs('members.*', 'documents.*')"
>
    <x-dropdown-link :href="route('members.index')">
        {{ __('Members') }}
    </x-dropdown-link>
</x-nav-dropdown>
```

#### Responsive Navigation Accordion

```blade
<div x-data="{ expanded: false }">
    <button @click="expanded = !expanded">
        {{ __('Management') }}
    </button>
    <div x-show="expanded">
        <!-- Links -->
    </div>
</div>
```

## Data Models

### Database Schema

#### users

- id: bigint (PK)
- name: string
- email: string (unique)
- password: string
- role: enum (super_admin, general, director, secretary, member)
- community_id: bigint (FK, nullable)
- email_verified_at: timestamp (nullable)
- remember_token: string (nullable)
- timestamps

#### permissions

- id: bigint (PK)
- key: string (unique)
- name: string
- module: string
- is_active: boolean
- timestamps

#### role_permissions

- id: bigint (PK)
- role: string
- permission_id: bigint (FK)
- timestamps

#### communities

- id: bigint (PK)
- name: string
- address: text (nullable)
- director_id: bigint (FK to users, nullable)
- phone: string (nullable)
- email: string (nullable)
- timestamps
- soft_deletes

#### members

- id: bigint (PK)
- civil_name: string
- religious_name: string (nullable)
- date_of_birth: date
- gender: enum (male, female, other)
- entry_date: date
- current_stage: enum (aspirant, postulant, novice, temporary_vows, perpetual_vows)
- community_id: bigint (FK)
- status: enum (active, deceased, exited, transferred)
- profile_photo_path: string (nullable)
- email: string (nullable)
- phone: string (nullable)
- address: text (nullable)
- emergency_contact_name: string (nullable)
- emergency_contact_phone: string (nullable)
- emergency_contact_relationship: string (nullable)
- timestamps
- soft_deletes
- indexes: (civil_name, religious_name, email, community_id, current_stage, status)

#### formation_events

- id: bigint (PK)
- member_id: bigint (FK)
- stage: enum (same as member.current_stage)
- event_date: date
- expiry_date: date (nullable, for temporary vows)
- notes: text (nullable)
- created_by: bigint (FK to users)
- timestamps

#### health_records

- id: bigint (PK)
- member_id: bigint (FK)
- condition: string
- medications: text (nullable)
- notes: text (nullable)
- document_path: string (nullable)
- recorded_at: date
- recorded_by: bigint (FK to users)
- timestamps

#### skills

- id: bigint (PK)
- member_id: bigint (FK)
- category: enum (pastoral, practical, special)
- name: string
- proficiency: enum (beginner, intermediate, advanced, expert)
- notes: text (nullable)
- timestamps

#### assignments

- id: bigint (PK)
- member_id: bigint (FK)
- community_id: bigint (FK)
- role: string
- start_date: date
- end_date: date (nullable)
- notes: text (nullable)
- timestamps

#### expenses

- id: bigint (PK)
- community_id: bigint (FK)
- category: string
- amount: integer (cents)
- date: date
- description: text
- receipt_path: string (nullable)
- created_by: bigint (FK to users)
- is_locked: boolean (default false)
- locked_at: timestamp (nullable)
- locked_by: bigint (FK to users, nullable)
- timestamps

#### documents

- id: bigint (PK)
- title: string
- category: enum (appointment, transfer, vows, introduction_letter, internal)
- file_path: string
- folder_id: bigint (FK to folders, nullable)
- member_id: bigint (FK, nullable)
- community_id: bigint (FK, nullable)
- uploaded_by: bigint (FK to users)
- timestamps
- soft_deletes

#### folders

- id: bigint (PK)
- name: string
- parent_id: bigint (FK to folders, nullable)
- community_id: bigint (FK, nullable)
- timestamps

#### audit_logs

- id: bigint (PK)
- user_id: bigint (FK)
- action: string
- auditable_type: string
- auditable_id: bigint
- old_values: json (nullable)
- new_values: json (nullable)
- ip_address: string (nullable)
- user_agent: text (nullable)
- timestamps (created_at only, no updates)

#### reminders

- id: bigint (PK)
- member_id: bigint (FK)
- type: enum (vow_expiration, birthday, health_check, formation_milestone)
- remind_at: datetime
- message: text
- is_sent: boolean (default false)
- sent_at: timestamp (nullable)
- timestamps

#### system_settings

- id: bigint (PK)
- key: string (unique)
- value: text
- type: enum (string, integer, boolean, json)
- timestamps

## Correctness Properties

_A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees._

### Property 1: Model Relationships Have Correct Return Types

_For any_ model with relationships, all relationship methods should have proper return type declarations (BelongsTo, HasMany, etc.)
**Validates: Requirements 1.3**

### Property 2: Routes Have Authorization Middleware

_For any_ route that accesses protected resources, the route should have authentication and authorization middleware applied
**Validates: Requirements 1.4**

### Property 3: Health Data Saves Correctly

_For any_ valid health record data, saving it should result in a database record with all fields preserved
**Validates: Requirements 2.2**

### Property 4: Skills Are Categorized Correctly

_For any_ skill entry, the category should be one of (pastoral, practical, special) and proficiency should be a valid level
**Validates: Requirements 2.3**

### Property 5: Member Search Works Across Fields

_For any_ search term, the search should return members matching name, religious name, community, formation stage, or skills
**Validates: Requirements 2.4**

### Property 6: Status Changes Create Audit Logs

_For any_ member status change, an audit log entry should be created with old and new values
**Validates: Requirements 2.5**

### Property 7: Expense Validation Enforces Required Fields

_For any_ expense submission, the system should reject entries missing category, amount, date, or description
**Validates: Requirements 3.1**

### Property 8: Monthly Report Generation Performance

_For any_ community with expenses, generating a monthly report should complete in under 5 seconds
**Validates: Requirements 3.3**

### Property 9: Locked Periods Are Immutable

_For any_ expense in a locked period, attempts to modify it should be rejected
**Validates: Requirements 3.4**

### Property 10: General Treasurer Has Read-Only Access

_For any_ user with General role, they should see all community finances but cannot edit expenses
**Validates: Requirements 3.5**

### Property 11: Documents Require Valid Categories

_For any_ document upload, the category should be one of the defined types (appointment, transfer, vows, introduction_letter, internal)
**Validates: Requirements 5.1**

### Property 12: Folder Hierarchy Supports Nesting

_For any_ folder, it can have a parent folder, creating a hierarchical structure
**Validates: Requirements 5.2**

### Property 13: Document Search Supports Multiple Filters

_For any_ document search, filtering by category, date, and member should return correct results
**Validates: Requirements 5.3**

### Property 14: Directors See Only Their Community Documents

_For any_ user with Director role, document queries should be automatically scoped to their community
**Validates: Requirements 5.4**

### Property 15: Document URLs Are Temporary

_For any_ document download, the generated signed URL should expire after the specified time period
**Validates: Requirements 5.5**

### Property 16: Vow Reminders Are Scheduled Correctly

_For any_ formation event with expiry date, reminders should be created at 60, 30, and 7 days before
**Validates: Requirements 6.1**

### Property 17: Formation Notifications Target Correct Role

_For any_ formation milestone, notifications should be sent to users with Formation Directress role
**Validates: Requirements 6.3**

### Property 18: Reminder Periods Are Customizable

_For any_ system setting change to reminder periods, new reminders should use the updated values
**Validates: Requirements 6.5**

### Property 19: Demographic Reports Include All Sections

_For any_ demographic report generation, the output should contain age distribution, formation stage breakdown, and community assignments
**Validates: Requirements 7.2**

### Property 20: Report Filters Work Correctly

_For any_ report with filters applied, the results should match the filter criteria (community, date range, formation stage)
**Validates: Requirements 7.4**

### Property 21: Error Messages Are User-Friendly

_For any_ validation error, the message should be in plain language without technical error codes
**Validates: Requirements 8.2**

### Property 22: Blade Components Are Reused

_For any_ view file, UI elements should use x-component syntax rather than raw HTML/Tailwind classes
**Validates: Requirements 8.3**

### Property 23: CRUD Operations Create Audit Logs

_For any_ member create, update, or delete operation, an audit log should be created with user, timestamp, and changes
**Validates: Requirements 9.1**

### Property 24: Transfer Logs Include Both Communities

_For any_ member transfer, the audit log should contain both old_community_id and new_community_id
**Validates: Requirements 9.2**

### Property 25: Audit Logs Are Immutable

_For any_ audit log entry, attempts to update or delete it should be prevented
**Validates: Requirements 9.3**

### Property 26: Audit Logs Support Filtering

_For any_ audit log query, filtering by user, action type, and date range should return correct results
**Validates: Requirements 9.4**

### Property 27: Audit Exports Are Tamper-Evident

_For any_ audit log export, the report should include checksums or signatures to detect tampering
**Validates: Requirements 9.5**

### Property 28: Timeline Events Are Chronological

_For any_ member timeline, events should be sorted by date in ascending order
**Validates: Requirements 10.1**

### Property 29: Service Records Require Mandatory Fields

_For any_ service record submission, location, role, and start_date should be required
**Validates: Requirements 10.2**

### Property 30: Community Changes Create Timeline Entries

_For any_ member community change, a timeline entry should be automatically created
**Validates: Requirements 10.3**

### Property 31: Service Duration Is Calculated

_For any_ service record with start and end dates, the duration should be computed correctly
**Validates: Requirements 10.4**

### Property 32: Member Profile Export Includes Timeline

_For any_ member profile PDF export, all timeline events should be included
**Validates: Requirements 10.5**

### Property 33: Celebration Card Generation Performance

_For any_ celebration card request, image generation should complete in under 3 seconds
**Validates: Requirements 11.3**

### Property 34: Upcoming Celebrations Filter By Date

_For any_ upcoming celebrations query, only events within the next 30 days should be returned
**Validates: Requirements 11.5**

### Property 35: Service Year Defaults New Assignments

_For any_ new assignment creation, the year should default to the configured service year setting
**Validates: Requirements 12.1**

### Property 36: Reminder Period Changes Apply Globally

_For any_ reminder period configuration change, all future reminders should use the new values
**Validates: Requirements 12.2**

### Property 37: Email Settings Are Validated

_For any_ SMTP configuration change, the system should test the connection before saving
**Validates: Requirements 12.4**

### Property 38: Backups Run Daily

_For any_ backup configuration enabled, database backups should be scheduled to run daily
**Validates: Requirements 12.5**

### Property 39: Dashboard Loads Under 2 Seconds

_For any_ dashboard page load on 4G connection, initial render should complete in under 2 seconds
**Validates: Requirements 13.1**

### Property 40: Member Search Performance

_For any_ member search query on database with 500+ members, results should return in under 500ms
**Validates: Requirements 13.2**

### Property 41: Photos Are Optimized Automatically

_For any_ photo upload, the system should convert to WebP format at 85% quality
**Validates: Requirements 13.3**

### Property 42: RBAC Uses Cached Permissions

_For any_ permission check, the system should use cached values to prevent N+1 queries
**Validates: Requirements 13.4**

### Property 43: Date of Birth Cannot Be Future

_For any_ member date of birth entry, future dates should be rejected
**Validates: Requirements 14.1**

### Property 44: Formation Dates Are Chronological

_For any_ member with multiple formation events, entry_date < first_vows_date < perpetual_vows_date should be enforced
**Validates: Requirements 14.2**

### Property 45: Assignments Cannot Overlap

_For any_ member, creating an assignment with date range overlapping an existing assignment should be rejected
**Validates: Requirements 14.3**

### Property 46: Expense Amounts Are Positive

_For any_ expense entry, negative amounts should be rejected
**Validates: Requirements 14.4**

### Property 47: File Uploads Are Validated

_For any_ file upload, invalid file types and files exceeding size limits (10MB documents, 5MB photos) should be rejected
**Validates: Requirements 14.5**

### Property 48: Images Have Alt Text

_For any_ image in the system, the alt attribute should be non-empty and descriptive
**Validates: Requirements 15.2**

### Property 49: Permission Cache Consistency

_For any_ user, when their permissions are checked twice within the cache TTL period, the second check should use cached data without querying the database
**Validates: Requirements 13.4**

### Property 50: Cache Invalidation on Role Change

_For any_ user, when their role is changed, subsequent permission checks should reflect the new role's permissions immediately
**Validates: Requirements 13.4**

### Property 51: Navigation Dropdown Visibility

_For any_ navigation dropdown, clicking the trigger should toggle the dropdown between open and closed states
**Validates: Requirements 16.1**

### Property 52: Navigation Active State

_For any_ navigation dropdown, when a child route is active, the parent dropdown trigger should display active styling
**Validates: Requirements 16.4**

## Error Handling

### Validation Errors

- Use Laravel FormRequests for all input validation
- Return user-friendly error messages without technical codes
- Example: "Please check the balance. The cash on hand doesn't match the system calculation." instead of "Error 422: Validation failed"

### Authorization Errors

- Return 403 Forbidden with clear message: "You don't have permission to access this resource"
- Log authorization failures for security auditing
- Redirect to appropriate page based on user role

### File Upload Errors

- Validate file type and size before processing
- Return specific errors: "Photo must be under 5MB" or "Only PDF, JPG, and PNG files are allowed"
- Clean up temporary files on error

### Database Errors

- Catch constraint violations and return user-friendly messages
- Example: "This member already has an assignment for this period" instead of "SQLSTATE[23000]: Integrity constraint violation"
- Log full error details for debugging

### Performance Errors

- Set timeouts for long-running operations (PDF generation, report exports)
- Return partial results with warning if query takes too long
- Queue heavy operations (bulk exports, email sending)

## Testing Strategy

### Unit Testing

- Test individual service methods in isolation
- Test model relationships and scopes
- Test validation rules
- Test helper functions
- Use factories for test data generation

### Feature Testing

- Test complete HTTP request/response cycles
- Test authentication and authorization
- Test CRUD operations for all resources
- Test file uploads and downloads
- Test PDF generation
- Test email sending (using Mail::fake())

### Property-Based Testing

- Use Pest for property-based tests
- Configure each test to run minimum 100 iterations
- Tag each test with the property number from this design document
- Example tag format: `// Feature: congregation-management-mvp, Property 6: Status Changes Create Audit Logs`

### Browser Testing

- Use Laravel Dusk for critical user journeys
- Test responsive design at different breakpoints
- Test keyboard navigation
- Test screen reader compatibility
- Capture screenshots for visual regression testing

### Performance Testing

- Measure page load times
- Measure query execution times
- Test with realistic data volumes (500+ members)
- Profile N+1 query issues
- Test concurrent user access

### Accessibility Testing

- Validate WCAG 2.1 Level AA compliance
- Test color contrast ratios
- Test keyboard navigation
- Test with screen readers (NVDA, JAWS)
- Validate ARIA attributes

### Testing Tools

- **Pest**: Primary testing framework
- **Laravel Dusk**: Browser testing
- **Faker**: Test data generation
- **Telescope**: Debugging and profiling
- **Laravel Pint**: Code style enforcement
- **PHPStan**: Static analysis

### Test Coverage Goals

- Minimum 80% code coverage
- 100% coverage for critical paths (authentication, authorization, financial operations)
- All correctness properties must have corresponding tests
