---
stepsCompleted: [1, 2, 3, 4, 5, 6, 7, 8]
inputDocuments:
  - docs/prd.md
  - docs/ux-design-specification.md
  - docs/analysis/product-brief-Managing_the_Congregation-2025-12-01.md
workflowType: "architecture"
lastStep: 8
project_name: "Managing the Congregation (at the organizational level)"
user_name: "Wavister"
date: "2025-12-01"
status: "complete"
completedAt: "2025-12-01"
---

# Architecture Decision Document

_This document builds collaboratively through step-by-step discovery. Sections are appended as we work through each architectural decision together._

## Project Context Analysis

### Requirements Overview

**Functional Requirements:**
The system requires a robust Member Management core with specialized lifecycle tracking for religious formation. Key modules include:

- **Member Management:** Comprehensive CRUD with deep historical tracking (Service History, Formation Stages).
- **Formation Tracking:** Logic-heavy module for calculating dates and managing stage transitions based on Canon Law.
- **Financial Management:** Distributed expense entry (Community level) with centralized reporting (Generalate level). Requires PDF generation and strict period locking.
- **Community Management:** Hierarchical structure (Houses) with member assignment and transfer logic.
- **Access Control:** Role-Based Access Control (RBAC) with strict context boundaries (Director sees only their House).

**Non-Functional Requirements:**

- **Performance:** < 2s page loads on 4G networks is a hard constraint, requiring optimized assets and efficient queries.
- **Offline Tolerance:** Critical for African communities. The system must handle intermittent connectivity, likely requiring PWA features or "optimistic UI" updates.
- **Security:** Strict data privacy (health records) and audit logging for all critical actions.
- **Usability:** High accessibility standards (WCAG AA, large fonts) for elderly users.

**Scale & Complexity:**

- Primary domain: Web Application (Laravel Monolith)
- Complexity level: Medium
- Estimated architectural components: ~10-15 (Auth, Members, Formation, Finance, Communities, Documents, Reporting, Notifications, Audit, etc.)

### Technical Constraints & Dependencies

- **Stack:** PHP 8.x (Laravel), MySQL 8.0, Tailwind CSS, Alpine.js/Livewire.
- **Infrastructure:** Dockerized deployment.
- **Browser Support:** Mobile-first (iOS Safari, Android Chrome) + Desktop. IE11 excluded.
- **Maintainability:** Strict adherence to Laravel standards; no custom frameworks.

### Cross-Cutting Concerns Identified

- **Role-Based Access Control (RBAC):** Must be pervasive, filtering data at the query level based on user scope (House vs. General).
- **Audit Logging:** Every state change (transfer, delete, financial approval) needs an immutable log.
- **File Management:** Secure storage and serving of private documents (receipts, health records).
- **Notification Engine:** Time-based alerts (vow expiry) and event-based notifications.
- **PDF Generation:** Centralized service for consistent report formatting.
- **Offline/Sync Strategy:** Handling data entry without connection and syncing when online.

## Starter Template Evaluation

### Primary Technology Domain

**Web Application (Laravel Monolith)** based on project requirements analysis.

### Starter Options Considered

- **Laravel Breeze:** Minimal, simple implementation of authentication. Uses Blade templates + Tailwind CSS + Alpine.js. Perfect for "Radical Simplicity" and custom UI development.
- **Laravel Jetstream:** Robust, feature-rich starter with Livewire or Inertia. Includes 2FA, Teams, API support. Powerful but higher complexity ("kitchen sink").
- **Plain Laravel:** Maximum control but requires manual setup of Auth, Tailwind, and frontend tooling.

### Selected Starter: Laravel Breeze (Blade Stack)

**Rationale for Selection:**

- **Alignment:** Perfectly matches the PRD's "Blade Templates + Tailwind CSS + Alpine.js" requirement.
- **Simplicity:** Fits the "Radical Simplicity" and "Experience MVP" philosophy by providing a clean, lightweight foundation without unused features.
- **Maintainability:** Standard Laravel package, easy for any Laravel developer to pick up (mitigating "Developer Burnout" risk).
- **Flexibility:** Allows us to implement the custom "Congregation" specific logic (Formation, Communities) without fighting opinionated pre-built structures.

**Initialization Command:**

```bash
composer create-project laravel/laravel managing-congregation
cd managing-congregation
composer require laravel/breeze --dev
php artisan breeze:install blade
```

**Architectural Decisions Provided by Starter:**

**Language & Runtime:**

- PHP 8.x
- Laravel 11 Framework

**Styling Solution:**

- Tailwind CSS (configured with PostCSS/Vite)
- Blade Components for UI reuse

**Build Tooling:**

- Vite (fast HMR and build optimization)

**Testing Framework:**

- Pest or PHPUnit (Standard Laravel)

**Code Organization:**

- Standard MVC (Model-View-Controller)
- Domain logic will need to be organized (likely Service/Action pattern)

**Development Experience:**

- Hot Module Replacement (HMR) via Vite
- Simple Auth scaffolding (Login, Register, Password Reset)
- Alpine.js pre-configured for interactivity

**Note:** Project initialization using this command should be the first implementation story.

## Core Architectural Decisions

### Decision Priority Analysis

**Critical Decisions (Block Implementation):**

- **Database Schema:** Must support complex "Service History" and "Formation" logic.
- **RBAC Implementation:** Strict "House-level" scoping is non-negotiable.
- **PDF Engine:** "One-click" reporting depends on a reliable PDF generator.

**Important Decisions (Shape Architecture):**

- **Offline Strategy:** How to handle data entry in rural Africa without internet.
- **File Storage:** Secure handling of private documents (Health/Vows).

**Deferred Decisions (Post-MVP):**

- **API Strategy:** External integrations (Vatican/Provinces) are Phase 3.
- **Mobile App:** Native app is Phase 3; PWA is sufficient for MVP.

### Data Architecture

- **Database:** MySQL 8.0 (Standard, Reliable, Relational).
- **Modeling Approach:** Strict Relational Model.
  - `members` table is the core entity.
  - `assignments` table tracks history (Member <-> Community <-> Role <-> DateRange).
  - `formation_events` table tracks lifecycle (Member <-> Stage <-> Date).
- **Validation:** Server-side validation (Laravel FormRequests) is the source of truth. Client-side (Alpine) for UX only.
- **Soft Deletes:** Enabled for all core entities (`members`, `communities`) to prevent accidental data loss.

### Authentication & Security

- **Auth Method:** Session-based Authentication (Standard Laravel). Simple and secure for a Monolith.
- **Authorization:** Policy-based Authorization (Laravel Policies).
  - _Critical Pattern:_ Every query must be scoped. `Member::where('house_id', $user->house_id)`.
- **Data Privacy:** Health records and sensitive documents must be stored in a private S3/MinIO bucket, not public storage. URLs generated with temporary signatures.

### API & Communication Patterns

- **Internal API:** Use Laravel Controllers returning JSON for dynamic parts (Livewire/Alpine), but primarily Server-Side Rendering (Blade).
- **HTMX/Livewire:** Use Livewire for dynamic interactions (e.g., "Dependent Dropdowns" for Location selection) to keep logic on the server.

### Frontend Architecture

- **Pattern:** Multi-Page Application (MPA) with "Sprinkles" of interactivity.
- **Stack:** Blade Templates (Layouts) + Tailwind CSS (Utility classes) + Alpine.js (Dropdowns, Modals).
- **State Management:** URL-driven state (Filters, Search) + Server Session. No complex client-side store (Redux/Pinia) needed.
- **Offline Strategy (MVP):** "Optimistic UI" is too complex for MVP.
  - _Decision:_ Use Service Worker for caching static assets (App Shell).
  - _Form Entry:_ If offline, browser "Local Storage" can temporarily hold an expense entry, warning the user "Not Saved to Server".

### Infrastructure & Deployment

- **Containerization:** Docker (Laravel Sail for Dev, Custom Dockerfile for Prod).
- **CI/CD:** GitHub Actions.
  - _Pipeline:_ Lint (Pint) -> Test (Pest) -> Build Assets (Vite) -> Deploy.
- **Hosting:** VPS (DigitalOcean/Linode) or PaaS (Railway/Render).
  - _Preference:_ VPS for cost control and data sovereignty.

### Decision Impact Analysis

**Implementation Sequence:**

1.  **Project Init:** Laravel Breeze + Docker Setup.
2.  **Core Domain:** Database Migrations (Members, Communities).
3.  **Auth & RBAC:** Scoping Logic & Policies.
4.  **Feature:** Member Management (CRUD).
5.  **Feature:** Financials (Expense Entry + PDF).

**Cross-Component Dependencies:**

- **RBAC** affects _every_ controller and query. It must be built first.
- **Formation Logic** depends on the **Member** entity being stable.

## Implementation Patterns & Consistency Rules

### Pattern Categories Defined

**Critical Conflict Points Identified:**
5 areas where AI agents could make different choices (Naming, Structure, Format, Communication, Process).

### Naming Patterns

**Database Naming Conventions:**

- **Tables:** Snake_case, Plural (e.g., `formation_events`, `members`).
- **Columns:** Snake_case (e.g., `is_active`, `joined_at`).
- **Foreign Keys:** Singular_model_id (e.g., `community_id`, `member_id`).
- **Indexes:** `table_column_index` (e.g., `members_email_index`).

**API/Route Naming Conventions:**

- **URLs:** Kebab-case, Plural Resources (e.g., `/formation-events`, `/members/{member}/edit`).
- **Route Names:** Kebab-case with dot notation (e.g., `members.create`, `formation.update-stage`).
- **Query Params:** Snake_case (e.g., `?sort_by=created_at`).

**Code Naming Conventions:**

- **Controllers:** PascalCase + Controller (e.g., `MemberController`).
- **Services:** PascalCase + Service (e.g., `FormationService`).
- **Variables:** camelCase (e.g., `$activeMembers`).
- **Blade Components:** Kebab-case (e.g., `<x-status-badge />`).

### Structure Patterns

**Project Organization:**

- **Logic Location:**
  - _Simple CRUD:_ Keep in Controller.
  - _Complex Logic:_ Move to `app/Services` (e.g., `FormationService` for date calculations).
  - _Queries:_ Use Scopes on Models (e.g., `Member::active()`).
- **Tests:** Co-located in `tests/Feature` and `tests/Unit`. Mirror the `app` structure.

**File Structure Patterns:**

- **Views:** Resource-based folders (e.g., `resources/views/members/index.blade.php`).
- **Components:** `resources/views/components` for UI atoms (buttons, cards).
- **Livewire:** `app/Livewire` for components, `resources/views/livewire` for templates.

### Format Patterns

**Data Exchange Formats:**

- **Dates:**
  - _Database:_ `Y-m-d` or `Y-m-d H:i:s`.
  - _UI Display:_ Human readable (e.g., "Jan 1, 2024").
  - _Input:_ HTML5 date picker format (`Y-m-d`).
- **Money:** Store as Integers (cents), display as formatted currency.
- **Booleans:** `1`/`0` in DB, `true`/`false` in JSON/JS.

### Communication Patterns

**State Management Patterns:**

- **Server-First:** State lives in the Database/Session.
- **Livewire:** Use for "temporary" UI state (e.g., current tab, modal open).
- **Alpine:** Use for "micro" interactions (e.g., dropdown toggle).
- **Flash Messages:** Use `session()->flash('status', 'Message')` for success/error feedback.

### Process Patterns

**Error Handling Patterns:**

- **User Facing:** Catch exceptions in Controllers, return `back()->withErrors(...)`.
- **Logging:** Log all 500s to storage/daily.log.
- **Validation:** Use FormRequests for all POST/PUT actions. Never validate in the Controller.

**Loading State Patterns:**

- **Livewire:** Use `wire:loading` to show spinners during server roundtrips.
- **Buttons:** Disable submit buttons on click to prevent double-submission.

### Enforcement Guidelines

**All AI Agents MUST:**

1.  **Follow Laravel Naming:** Never use CamelCase for DB columns.
2.  **Scope Queries:** Always apply `->where('house_id', ...)` or use a Global Scope for multi-tenancy.
3.  **Use Blade Components:** Do not write raw Tailwind classes for buttons/inputs repeatedly.

**Pattern Examples:**

**Good Example (Controller):**

```php
public function store(StoreMemberRequest $request) {
    $member = Member::create($request->validated());
    return to_route('members.show', $member)->with('status', 'Member created.');
}
```

**Anti-Pattern (Avoid):**

```php
// Avoid: Validation in controller, raw DB queries, no route helper
public function save() {
    request()->validate([...]);
    DB::table('Members')->insert([...]); // Wrong casing, raw query
    return redirect('/members'); // Hardcoded URL
}
```

## Project Structure & Boundaries

### Complete Project Directory Structure

```
managing-congregation/
├── README.md
├── composer.json
├── package.json
├── vite.config.js
├── tailwind.config.js
├── phpunit.xml
├── .env.example
├── .gitignore
├── docker-compose.yml
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   ├── MemberController.php
│   │   │   ├── FormationController.php
│   │   │   ├── FinancialController.php
│   │   │   └── CommunityController.php
│   │   ├── Middleware/
│   │   │   └── ScopeByHouse.php
│   │   ├── Requests/
│   │   │   ├── StoreMemberRequest.php
│   │   │   └── StoreExpenseRequest.php
│   │   └── Resources/ (API Resources if needed)
│   ├── Models/
│   │   ├── User.php
│   │   ├── Member.php
│   │   ├── Community.php
│   │   ├── Assignment.php
│   │   ├── FormationEvent.php
│   │   └── Expense.php
│   ├── Policies/
│   │   ├── MemberPolicy.php
│   │   └── FinancialPolicy.php
│   ├── Providers/
│   ├── Services/
│   │   ├── FormationService.php (Date Logic)
│   │   ├── PdfService.php
│   │   └── FileStorageService.php
│   └── View/
│       └── Components/
│           ├── StatusBadge.php
│           └── LedgerRow.php
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── xxxx_xx_xx_create_communities_table.php
│   │   ├── xxxx_xx_xx_create_members_table.php
│   │   ├── xxxx_xx_xx_create_assignments_table.php
│   │   └── xxxx_xx_xx_create_expenses_table.php
│   └── seeders/
│       └── DatabaseSeeder.php
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   ├── app.js
│   │   └── controllers/ (Alpine)
│   └── views/
│       ├── components/
│       │   ├── ledger-row.blade.php
│       │   └── status-badge.blade.php
│       ├── layouts/
│       │   ├── app.blade.php
│       │   └── guest.blade.php
│       ├── members/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       ├── finance/
│       │   └── report.blade.php
│       └── dashboard.blade.php
├── routes/
│   ├── web.php
│   ├── auth.php
│   └── console.php
├── storage/
│   └── app/
│       └── private/ (Secure Docs)
└── tests/
    ├── Feature/
    │   ├── MemberTest.php
    │   └── FinancialReportTest.php
    └── Unit/
        └── FormationLogicTest.php
```

### Architectural Boundaries

**API Boundaries:**

- **Internal:** `routes/web.php` defines all application routes. Controllers return Views (HTML) or JSON (for specific dynamic components).
- **External:** None for MVP. Future API routes will live in `routes/api.php`.

**Component Boundaries:**

- **Blade Components (`resources/views/components`):** Pure UI presentation. No business logic.
- **Livewire Components (`app/Livewire`):** UI Logic + State (e.g., "Dependent Dropdowns").
- **Controllers:** Request handling, Authorization check, Service delegation, Response.

**Service Boundaries:**

- **Services (`app/Services`):** Pure business logic (e.g., "Calculate next vow date"). Independent of HTTP Request/Response.
- **Models:** Data persistence and Relationships.

**Data Boundaries:**

- **Database:** MySQL is the single source of truth.
- **Storage:** `storage/app/private` is the boundary for sensitive files. Direct access is forbidden; must go through a Controller that checks Policy.

### Requirements to Structure Mapping

**Feature/Epic Mapping:**

- **Member Management:**
  - Controller: `MemberController.php`
  - Model: `Member.php`
  - Views: `resources/views/members/`
  - Policy: `MemberPolicy.php`
- **Formation Tracking:**
  - Logic: `FormationService.php`
  - Model: `FormationEvent.php`
  - Controller: `FormationController.php`
- **Financial Reporting:**
  - Controller: `FinancialController.php`
  - Model: `Expense.php`
  - PDF Generation: `PdfService.php`

**Cross-Cutting Concerns:**

- **RBAC:** `app/Policies/*` and `app/Http/Middleware/ScopeByHouse.php`.
- **Audit Logging:** `app/Observers/AuditObserver.php` (to be created).

### Integration Points

**Internal Communication:**

- **Controller -> Service:** Direct method calls (`$service->calculate()`).
- **View -> Controller:** HTML Forms (POST/PUT).
- **Alpine -> Backend:** `fetch()` to internal API endpoints or Livewire actions.

**External Integrations:**

- **S3/MinIO:** Via Laravel's `Storage` facade (abstracted).

### File Organization Patterns

**Source Organization:**

- **Domain-Driven-ish:** While using standard Laravel folders, we group related logic. E.g., `FormationService` handles all formation logic, rather than spreading it across Models and Controllers.

**Test Organization:**

- **Feature Tests:** Test full HTTP requests (Controller -> DB -> View).
- **Unit Tests:** Test isolated logic (Service methods).

### Development Workflow Integration

**Development Server:**

- `./vendor/bin/sail up` starts the Docker environment (App, MySQL, Redis, Mailpit).
- `npm run dev` starts Vite for asset compilation.

**Deployment Structure:**

- `Dockerfile` defines the production image.
- `docker-compose.prod.yml` defines the production stack.

## Architecture Validation Results

### Coherence Validation ✅

**Decision Compatibility:**
The selected stack (Laravel Breeze + Blade + Alpine) is internally consistent. Avoiding a full SPA (React/Vue) reduces complexity significantly, aligning with the "Radical Simplicity" goal. The choice of MySQL matches Laravel's defaults perfectly.

**Pattern Consistency:**
Naming conventions (Snake for DB, Kebab for URLs) are standard Laravel best practices. The "Service Layer" pattern addresses the complexity of Canon Law logic without cluttering Controllers.

**Structure Alignment:**
The directory structure explicitly allocates space for "Services" (`app/Services`) and "Policies" (`app/Policies`), ensuring that Business Logic and Authorization have dedicated homes, preventing "Fat Controllers."

### Requirements Coverage Validation ✅

**Epic/Feature Coverage:**

- **Member Management:** Fully covered by `MemberController`, `Member` model, and `MemberPolicy`.
- **Formation Tracking:** Covered by `FormationService` (logic) and `FormationEvent` (data).
- **Financials:** Covered by `Expense` model and `PdfService` for reporting.

**Functional Requirements Coverage:**

- **RBAC:** Addressed via `ScopeByHouse` middleware and Policies. This is a robust, pervasive solution.
- **Offline Tolerance:** Addressed via Service Worker (Assets) and Local Storage fallback. _Note: This is a minimal MVP approach, not a full Offline-First sync engine._

**Non-Functional Requirements Coverage:**

- **Performance:** Blade SSR + Vite ensures fast load times (<2s).
- **Security:** Private storage for health records and strict Policies cover data privacy.

### Implementation Readiness Validation ✅

**Decision Completeness:**
All critical technology choices (Stack, DB, Auth) are made. Versions (PHP 8.x, Laravel 11) are specified.

**Structure Completeness:**
The file tree is complete, including specific locations for "Logic-heavy" components.

**Pattern Completeness:**
Naming and Coding standards are defined to prevent "Spaghetti Code."

### Gap Analysis Results

**Priority 3 (Nice-to-Have):**

- **Detailed Offline Sync:** The current "Warning" strategy is safe but low-tech. A robust background sync (using IndexedDB + Background Sync API) could be added in Phase 2.
- **API Documentation:** Not needed for MVP (Internal use only), but `scribe` or `swagger` should be considered if the API opens up.

### Architecture Completeness Checklist

**✅ Requirements Analysis**

- [x] Project context thoroughly analyzed
- [x] Scale and complexity assessed
- [x] Technical constraints identified
- [x] Cross-cutting concerns mapped

**✅ Architectural Decisions**

- [x] Critical decisions documented with versions
- [x] Technology stack fully specified
- [x] Integration patterns defined
- [x] Performance considerations addressed

**✅ Implementation Patterns**

- [x] Naming conventions established
- [x] Structure patterns defined
- [x] Communication patterns specified
- [x] Process patterns documented

**✅ Project Structure**

- [x] Complete directory structure defined
- [x] Component boundaries established
- [x] Integration points mapped
- [x] Requirements to structure mapping complete

### Architecture Readiness Assessment

**Overall Status:** READY FOR IMPLEMENTATION

**Confidence Level:** HIGH

**Key Strengths:**

1.  **Simplicity:** Sticking to standard Laravel patterns reduces onboarding time and maintenance cost.
2.  **Clarity:** Explicit boundaries for "Formation Logic" prevent it from leaking into UI code.
3.  **Security:** RBAC is baked into the foundation, not bolted on.

**Areas for Future Enhancement:**

- **PWA Features:** Enhancing the offline experience.
- **Mobile App:** Building a native interface for remote areas.

### Implementation Handoff

**AI Agent Guidelines:**

- Follow all architectural decisions exactly as documented.
- Use implementation patterns consistently across all components.
- Respect project structure and boundaries.
- Refer to this document for all architectural questions.

**First Implementation Priority:**
Initialize the project with Laravel Breeze and set up the Docker environment.
