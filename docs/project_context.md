---
project_name: "Managing the Congregation (at the organizational level)"
user_name: "Wavister"
date: "2025-12-27"
last_updated: "2025-12-27"
sections_completed:
  [
    "technology_stack",
    "language_rules",
    "framework_rules",
    "livewire_patterns",
    "export_rules",
    "testing_rules",
    "quality_rules",
    "workflow_rules",
    "anti_patterns",
  ]
status: "production"
rule_count: 28
optimized_for_llm: true
---

# Project Context for AI Agents

_This file contains critical rules and patterns that AI agents must follow when implementing code in this project. Focus on unobvious details that agents might otherwise miss._

**⚠️ IMPORTANT:** This document reflects the **actual production implementation** as of 2025-12-27. The system has evolved significantly beyond the original MVP specification.

---

## Technology Stack & Versions (ACTUAL)

- **PHP:** 8.3.6 (Strict Typing Required)
- **Laravel:** 11.x
- **MySQL:** 8.0
- **Frontend:** Blade + Tailwind CSS 3.4 + Alpine.js
- **Livewire:** 3.7 (**HEAVY USAGE** - Primary interactive layer, not "sparingly")
- **Testing:** PHPUnit 11.5.3 (NOT Pest)
- **Build Tool:** Vite
- **Container:** Docker (Laravel Sail 1.41)

### Export & Document Generation Libraries

- **PDF:** `barryvdh/laravel-dompdf` v3.1
- **Image Processing:** `intervention/image` v3.11
- **Excel:** `maatwebsite/excel` v3.1
- **DOCX:** `phpoffice/phpword` v1.4
- **Database Backup:** `spatie/db-dumper` v3.8

### Development Tools

- **Static Analysis:** PHPStan 2.1, Larastan 3.8
- **Code Style:** Laravel Pint 1.24
- **Development Server:** Laravel Sail

---

## Critical Implementation Rules

### Language-Specific Rules (PHP 8.3.6)

- **Strict Typing:** ALWAYS use `declare(strict_types=1);` in all new PHP files.
- **Type Hinting:** Explicitly type all method arguments and return values.
- **Modern Features:** Use Constructor Property Promotion, Match expressions, and Enums.
- **Null Safety:** Use null coalescing (`??`) and null-safe operator (`?->`) appropriately.

### Framework-Specific Rules (Laravel 11)

- **Authorization:** NEVER skip authorization. Use Policies in Controllers (`$this->authorize(...)`).
- **Validation:** ALWAYS use `FormRequest` classes. NEVER validate in Controller methods.
- **Business Logic:** Complex logic (Formation/Canon Law, Financial calculations) MUST go in `app/Services`.
- **Scoping:** ALWAYS apply community-based scoping for multi-tenancy (`where('community_id', ...)`).
- **Query Optimization:** ALWAYS eager load relationships. Use `select()` to limit columns.

### Livewire-Specific Rules (3.7) **NEW**

- **Component Architecture:** Use Livewire for dashboards, report builders, and interactive forms.
- **Computed Properties:** Use `#[Computed]` attribute for derived data to enable caching.
- **State Management:** Keep component state minimal. Use computed properties for queries.
- **Performance:** Use `wire:model.blur` or `.change` instead of `.live` unless real-time updates are critical.
- **Loading States:** ALWAYS implement `wire:loading` indicators for user feedback.
- **Validation:** Use `#[Validate]` attributes for real-time validation.
- **Events:** Use `$this->dispatch()` for component communication.
- **Authorization:** Call `$this->authorize()` in `mount()` or action methods.

**Livewire Components in System:**

- `Dashboard` - Main dashboard
- `FinancialDashboard` - Financial overview
- `Reports\ReportBuilder` - Advanced report builder
- `Notifications\NotificationCenter` - Notification management

**Reference:** See `docs/livewire-patterns.md` for detailed patterns.

### Export & Document Generation Rules **NEW**

- **PDF Generation:** Use DomPDF with dedicated Blade views. Enable remote resources for fonts.
- **Excel Exports:** Create dedicated Export classes implementing `FromCollection`, `WithHeadings`, `WithMapping`.
- **DOCX Generation:** Use PHPWord for formatted documents. Store temp files in `storage/app/temp/`.
- **Performance:** For large exports, implement chunking (`WithChunkReading` for Excel).
- **Error Handling:** Wrap all export operations in try-catch blocks with user-friendly error messages.
- **File Cleanup:** Use `deleteFileAfterSend()` for temporary files.

**Reference:** See `docs/export-architecture.md` for detailed patterns.

### Testing Rules (PHPUnit 11.5.3)

- **Framework:** Use PHPUnit (NOT Pest as originally planned).
- **Syntax:** Standard PHPUnit syntax (`public function test_feature_works()`).
- **State:** Use `RefreshDatabase` trait for database tests.
- **Coverage:** Test happy path AND authorization failures AND edge cases.
- **Livewire Testing:** Use `Livewire::test()` facade for component tests.
- **Factories:** Use factories for all test data creation.

### Code Quality & Style Rules

- **Naming:** Controllers (`PascalCase`), DB (`snake_case`), Routes (`kebab-case`).
- **Blade:** Use Components (`<x-card>`) to avoid repetitive Tailwind classes.
- **Formatting:** Follow Laravel Pint (PSR-12) standards.
- **Comments:** Document complex business logic (Canon Law calculations, financial rules).
- **Type Safety:** Use Enums for fixed sets of values (`UserRole`, `PermissionKey`, `MemberStatus`).

### Security Rules

- **Authorization:** Use Policy-based authorization for ALL resource access.
- **Scoping:** ALWAYS scope queries by user's community (Directors see only their community).
- **Super Admin Bypass:** Implement `before()` method in Policies for super admin bypass.
- **File Storage:** Use `storage/app/private` for sensitive documents (health records, formation docs).
- **Temporary Signatures:** Generate temporary URLs for private file downloads.
- **Audit Logging:** Log all critical actions (Create, Update, Delete, Transfer) to `audit_logs` table.

### RBAC Rules **ENHANCED**

- **Dynamic Roles:** System supports custom role creation beyond the 5 default roles.
- **Permission Enums:** Use `PermissionKey` enum for type-safe permission checks.
- **Permission Checking:** Use `$user->hasPermission(PermissionKey::TERRITORIES_VIEW)`.
- **Global Scopes:** Implement Global Scopes on models for automatic community filtering.
- **Policy Pattern:** ALWAYS implement `before()` method for super admin bypass.

**Reference:** See `plans/RBAC_System_Documentation.md` for complete RBAC architecture.

### Critical Don't-Miss Rules

- **Anti-Pattern:** No logic in Controllers ("Fat Controllers"). Delegate to Services or Models.
- **Anti-Pattern:** No `env()` calls outside config files. Use `config('app.name')`.
- **Anti-Pattern:** No raw DB queries. Use Eloquent or Query Builder.
- **Anti-Pattern:** No `wire:model.live` on every input. Use `.blur` or `.change` for performance.
- **Security:** Do not expose internal IDs without authorization checks.
- **Security:** NEVER return full models to Livewire components (use DTOs or select specific fields).
- **Performance:** ALWAYS eager load relationships to avoid N+1 queries.
- **Performance:** Use `select()` to limit columns in queries.

---

## Project Structure (Actual)

### Models (28 Total)

`Member`, `Community`, `Assignment`, `FormationEvent`, `FormationDocument`, `HealthRecord`, `Skill`, `Education`, `EmergencyContact`, `Ordination`, `Expense`, `Project`, `ProjectMember`, `Task`, `Document`, `Folder`, `Reminder`, `PeriodicEvent`, `Notification`, `AuditLog`, `Permission`, `Role`, `User`, `SystemSetting`, `DashboardWidget`, `ReportTemplate`, `FilterPreset`

### Key Services

- `FormationService` - Canon Law date calculations
- `PdfService` - PDF generation (if extracted)
- `FileStorageService` - Secure file handling
- `PermissionService` - RBAC permission management

### Livewire Components

- `Dashboard`
- `FinancialDashboard`
- `Reports\ReportBuilder`
- `Notifications\NotificationCenter`

---

## Feature Scope (Actual vs. Planned)

| Feature              | Original Plan | Current Status   |
| -------------------- | ------------- | ---------------- |
| Member Management    | MVP           | ✅ Enhanced      |
| Formation Tracking   | MVP           | ✅ Complete      |
| Community Management | MVP           | ✅ Enhanced      |
| Financial Management | Post-MVP      | ✅ Complete      |
| Project Management   | Post-MVP      | ✅ Complete + AI |
| Document Management  | Growth Phase  | ✅ Complete      |
| Celebration Cards    | Post-MVP      | ✅ Complete      |
| Advanced Reporting   | Post-MVP      | ✅ Complete      |
| RBAC                 | MVP (3 roles) | ✅ Dynamic Roles |
| Audit Logging        | MVP           | ✅ Complete      |
| Backup Management    | Not Planned   | ✅ Complete      |
| AI Integration       | Not Planned   | ✅ Project Gen   |

**Reference:** See `docs/features-implemented.md` for complete feature inventory.

---

## Common Patterns

### Controller Pattern

```php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;

class MemberController extends Controller
{
    public function store(StoreMemberRequest $request)
    {
        $this->authorize('create', Member::class);

        $member = Member::create($request->validated());

        return to_route('members.show', $member)
            ->with('success', 'Member created successfully');
    }
}
```

### Livewire Component Pattern

```php
declare(strict_types=1);

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Validate;

class FinancialDashboard extends Component
{
    #[Validate('required|integer|min:1|max:12')]
    public int $month;

    #[Computed]
    public function expenses()
    {
        return Expense::query()
            ->whereMonth('date', $this->month)
            ->with('community:id,name')
            ->get();
    }

    public function render()
    {
        return view('livewire.financial-dashboard');
    }
}
```

### Export Pattern

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function exportReport()
{
    $data = $this->prepareReportData();

    $pdf = Pdf::loadView('reports.template', compact('data'))
        ->setPaper('a4', 'portrait');

    return $pdf->download('report.pdf');
}
```

---

## Usage Guidelines

**For AI Agents:**

- Read this file AND `docs/features-implemented.md` before implementing any code
- Follow ALL rules exactly as documented
- Use Livewire for interactive components (dashboards, forms, builders)
- ALWAYS implement authorization checks
- ALWAYS eager load relationships
- When in doubt, prefer the more restrictive option
- Refer to `docs/livewire-patterns.md` for Livewire implementation
- Refer to `docs/export-architecture.md` for export functionality

**For Humans:**

- Keep this file lean and focused on agent needs
- Update when technology stack changes or patterns evolve
- Review when major features are added
- Remove rules that become obvious over time

**Last Updated:** 2025-12-27  
**Next Review:** When major architectural changes occur
