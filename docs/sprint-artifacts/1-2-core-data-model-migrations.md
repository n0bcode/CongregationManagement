# Story 1.2: Core Data Model & Migrations

Status: done

## Story

As a **System Administrator**,
I want **the core database tables for users, communities, and members to be defined**,
so that **the application has a foundational structure for storing data.**

## Acceptance Criteria

1.  **Migrations Run:** `php artisan migrate` executes successfully without errors.
2.  **Users Table:** The `users` table exists (default Laravel + modifications if needed for RBAC later, but standard for now).
3.  **Communities Table:** The `communities` table is created with fields:
    - `id` (Primary Key)
    - `name` (String)
    - `location` (String/Text - address or city)
    - `timestamps` (created_at, updated_at)
    - `softDeletes` (deleted_at)
4.  **Members Table:** The `members` table is created with fields:
    - `id` (Primary Key)
    - `community_id` (Foreign Key -> communities)
    - `name` (String - Religious Name)
    - `civil_name` (String)
    - `dob` (Date)
    - `entry_date` (Date)
    - `status` (String/Enum: Active, Deceased, Exited, Transferred)
    - `timestamps` (created_at, updated_at)
    - `softDeletes` (deleted_at)
5.  **Relationships:**
    - Foreign key constraint: `members.community_id` references `communities.id`.
6.  **Models Created:** Eloquent models created in `app/Models/`:
    - `User` (Existing)
    - `Community`
    - `Member`
7.  **Soft Deletes:** Enabled for `Community` and `Member` models and tables.
8.  **Factories & Seeders:** `CommunityFactory` and `MemberFactory` created. `DatabaseSeeder` updated to seed initial test data.
9.  **Performance:** Indexes added to `members` table for `name`, `civil_name`, and `community_id`.

## Tasks / Subtasks

- [x] Create `Community` model and migration (`php artisan make:model Community -m`)
  - [x] Define schema in migration (name, location, softDeletes)
  - [x] Add `SoftDeletes` trait to Model
  - [x] Define `members()` relationship in Model
- [x] Create `Member` model and migration (`php artisan make:model Member -m`)
  - [x] Define schema in migration (community_id, name, civil_name, dob, entry_date, status, softDeletes)
  - [x] Add `SoftDeletes` trait to Model
  - [x] Define `community()` relationship in Model
  - [x] Define `casts` for dates (dob, entry_date)
- [x] Create Factories (`php artisan make:factory CommunityFactory`, `MemberFactory`)
- [x] Update `DatabaseSeeder` to call factories
- [x] Run migrations (`php artisan migrate`)
- [x] Verify database structure (using `php artisan model:show` or database inspection)

## Dev Notes

### Architecture Compliance

- **Naming:** Use **Snake_case** for DB columns (`civil_name`, `entry_date`). Use **Plural** for table names (`communities`, `members`).
- **Soft Deletes:** Critical requirement from Architecture doc for core entities.
- **Strictness:** Use standard Laravel migrations. Do not use raw SQL.
- **Location:** Models must be in `app/Models/`.

### Technical Implementation Guide

**1. Community Migration:**

```php
Schema::create('communities', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('location')->nullable(); // Address/City
    $table->timestamps();
    $table->softDeletes();
});
```

**2. Member Migration:**

```php
Schema::create('members', function (Blueprint $table) {
    $table->id();
    $table->foreignId('community_id')->constrained()->onDelete('restrict'); // Protect data integrity
    $table->string('name')->index(); // Religious Name
    $table->string('civil_name')->index();
    $table->date('dob');
    $table->date('entry_date');
    $table->string('status')->default('Active'); // Consider Enum later, string fine for MVP
    $table->timestamps();
    $table->softDeletes();

    // Composite index for common lookups if needed, but individual indexes fine for now
});
```

**3. Models:**

**Community.php**

```php
protected $fillable = ['name', 'location'];
```

**Member.php**

```php
protected $fillable = ['community_id', 'name', 'civil_name', 'dob', 'entry_date', 'status'];

protected $casts = [
    'dob' => 'date',
    'entry_date' => 'date',
];
```

### Previous Story Learnings (from 1.1)

- **Project Root:** Work inside `managing-congregation/` directory.
- **Laravel Version:** 11.x is the target.
- **Docker:** Use `./vendor/bin/sail artisan` for running commands if local php is not available or version mismatched.

## References

- [Architecture: Data Architecture](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/architecture.md#data-architecture)
- [Epics: Story 1.2](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/epics.md#story-12-core-data-model-migrations)

## Dev Agent Record

### Context Reference

- **Architecture:** `docs/architecture.md`
- **Epics:** `docs/epics.md`

### Agent Model Used

Antigravity (Google Deepmind)

### Completion Notes List

- [x] Models created
- [x] Migrations files created
- [x] Factories & Seeders created
- [x] Migrations executed successfully
- [x] Verified database structure via `php artisan model:show` and unit tests.

### File List

- managing-congregation/app/Models/Community.php
- managing-congregation/app/Models/Member.php
- managing-congregation/database/migrations/2025_12_02_035011_create_communities_table.php
- managing-congregation/database/migrations/2025_12_02_035215_create_members_table.php
- managing-congregation/database/factories/CommunityFactory.php
- managing-congregation/database/factories/MemberFactory.php
- managing-congregation/database/seeders/DatabaseSeeder.php
- managing-congregation/tests/Unit/CommunityTest.php
- managing-congregation/tests/Unit/MemberTest.php
- managing-congregation/tests/Unit/SeederTest.php

### Change Log

- 2025-12-02: Implemented core data models (Community, Member) with migrations, factories, and tests. Updated DatabaseSeeder.
- 2025-12-02: [Code Review] Added `declare(strict_types=1);` to all new PHP files.

## Senior Developer Review (AI)

- **Review Outcome:** Approved (with fixes applied)
- **Review Date:** 2025-12-02
- **Reviewer:** Code Review Agent

### Action Items

- [x] [High] Add `declare(strict_types=1);` to all new PHP files (Fixed automatically)
