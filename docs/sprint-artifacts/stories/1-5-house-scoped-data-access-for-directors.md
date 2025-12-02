# Story 1.5: House-Scoped Data Access for Directors

Status: Done

## Story

As a **Community Director**,
I want to **see ONLY the members and data related to my assigned house**,
so that **data privacy is maintained and I cannot accidentally access other communities' information**.

## Context

This story implements the **critical multi-tenancy pattern** for the entire application. It ensures that Community Directors are automatically restricted to viewing and managing only the data (members, finances, etc.) for their assigned community/house. This is a **foundational security requirement** that affects every future feature.

**Key Design Decisions:**

- ✅ Global Scope approach for automatic query filtering
- ✅ Trait-based implementation for reusability across models
- ✅ Super Admin and General roles bypass scoping (full access)
- ✅ Director role is strictly scoped to their `community_id`
- ✅ Scope applies to ALL queries (index, show, update, delete)
- ❌ NO manual `where()` clauses in controllers (anti-pattern)
- ❌ NO middleware approach (less reliable than model scopes)

**Architecture Reference:**
From `architecture.md` line 159:

> _Critical Pattern:_ Every query must be scoped. `Member::where('house_id', $user->house_id)`.

We're implementing this using Laravel Global Scopes to avoid repetitive manual filtering in controllers.

**Role Behavior Matrix:**

| Role          | Scoping Behavior                | Rationale                                     |
| ------------- | ------------------------------- | --------------------------------------------- |
| `SUPER_ADMIN` | ✅ Bypass (see all communities) | Full system access required                   |
| `GENERAL`     | ✅ Bypass (see all communities) | Generalate oversight requires full visibility |
| `DIRECTOR`    | ❌ Scoped (own community only)  | Data privacy - restricted to assigned house   |
| `MEMBER`      | ❌ Scoped (own community only)  | Data privacy - restricted to assigned house   |

## Acceptance Criteria

### AC1: Global Scope Trait Created

- Given the need for reusable scoping logic,
- When a `ScopedByCommunity` trait is created in `app/Models/Concerns/`,
- Then it defines a `bootScopedByCommunity()` method that adds a global scope
- And the scope automatically filters queries by `community_id` for Director users
- And the scope is bypassed for Super Admin and General roles

### AC2: Member Model Uses Scoping

- Given the `Member` model exists,
- When the `ScopedByCommunity` trait is applied,
- Then all `Member::all()`, `Member::find()`, and query builder calls automatically filter by the authenticated user's `community_id`
- And Directors can ONLY see members from their assigned community
- And Super Admins and Generals see all members (no filtering)

### AC3: Scoping Respects User Roles

- Given users with different roles (see Role Behavior Matrix above),
- When they query `Member::all()`,
- Then scoping is applied according to their role:
  - **DIRECTOR/MEMBER:** SQL includes `WHERE community_id = {user.community_id}` automatically
  - **SUPER_ADMIN/GENERAL:** No WHERE clause added (full access to all communities)
- And the behavior matches the Role Behavior Matrix exactly

### AC4: Scoping Works with Relationships

- Given a Director queries members via a relationship (e.g., `$community->members`),
- When the relationship is loaded,
- Then the scoping still applies correctly (even though querying through parent model)
- And no members from other communities leak through
- **Note:** This is expected behavior - global scopes apply to ALL queries including relationships

### AC5: Scoping Can Be Bypassed When Needed

- Given a Super Admin needs to perform a cross-community operation,
- When they use `Member::withoutGlobalScopes()` (removes all global scopes),
- Then the scope is temporarily disabled for that query
- And all members are returned regardless of community
- **Note:** Since we use anonymous scopes, use `withoutGlobalScopes()` (plural) to remove all scopes, or implement a named scope if selective removal is needed

### AC6: Comprehensive Test Coverage

- Given the house-scoped access implementation,
- When the test suite runs,
- Then all 15+ tests pass (see Tasks 3-5 for detailed breakdown by category)
- And code coverage for scoping logic is ≥95%
- And no N+1 query issues introduced (verified with query logging tests)

## Tasks / Subtasks

### 1. Create Scoping Trait

- [x] Create `app/Models/Concerns/ScopedByCommunity.php`
  - [x] Add `declare(strict_types=1);`
  - [x] Define `bootScopedByCommunity()` method
  - [x] Add global scope using `static::addGlobalScope()`
  - [x] Check `Auth::user()` role and `community_id`
  - [x] Apply `where('community_id', $user->community_id)` for Directors only
  - [x] Skip scope for `SUPER_ADMIN` and `GENERAL` roles
  - [x] Handle unauthenticated users gracefully (no scope applied)

### 2. Apply Trait to Member Model

- [x] Update `app/Models/Member.php`
  - [x] Add `use App\Models\Concerns\ScopedByCommunity;`
  - [x] Add trait to class: `use ScopedByCommunity;`
  - [x] Verify existing `community()` relationship is defined

### 3. Testing - Trait Functionality

- [x] Create `tests/Unit/Concerns/ScopedByCommunityTest.php`
  - [x] Test: scope is applied for Director role
  - [x] Test: scope is applied for Member role
  - [x] Test: scope is bypassed for Super Admin
  - [x] Test: scope is bypassed for General
  - [x] Test: withoutGlobalScopes() removes the scope (plural method)

### 4. Testing - Member Model Integration

- [x] Create `tests/Feature/MemberScopingTest.php`
  - [x] Test: Director sees only their community's members
  - [x] Test: Member sees only their community's members
  - [x] Test: Director cannot access members from other communities
  - [x] Test: Super Admin sees all members
  - [x] Test: General sees all members
  - [x] Test: Relationship queries respect scoping ($community->members)
  - [x] Test: Query builder methods respect scoping

### 5. Testing - Edge Cases

- [x] Add edge case tests to `tests/Feature/MemberScopingTest.php`

  - [x] Test: Director with `null` community_id sees no members
  - [x] Test: Unauthenticated requests don't crash (no scope applied)
  - [x] Test: Scoping works with pagination
  - [x] Test: Scoping works with soft deletes
  - [x] Test: N+1 query prevention with scoped eager loading

    ```php
    public function test_scoping_does_not_introduce_n_plus_1_queries(): void
    {
        $community = Community::factory()->create();
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        Member::factory()->count(10)->create(['community_id' => $community->id]);

        $this->actingAs($director);

        DB::enableQueryLog();
        $members = Member::with('community')->get();
        $queryCount = count(DB::getQueryLog());

        // Should be 2 queries: 1 for members (with scope), 1 for communities
        $this->assertEquals(2, $queryCount);
    }
    ```

### 6. Verify Database Index

- [x] Verify `community_id` index exists on `members` table
  - [x] Check migration `2025_12_02_035215_create_members_table.php`
  - [x] Confirm `foreignId('community_id')` creates index automatically
  - [x] Run `SHOW INDEX FROM members` to verify

### 7. Update Factories for Testing

- [x] Update `database/factories/UserFactory.php`

  - [x] Add `community_id` support for creating scoped test users
  - [x] Add state methods: `director()`, `member()` with community assignment
  - [x] Implementation pattern:

    ```php
    public function director(?Community $community = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::DIRECTOR,
            'community_id' => $community?->id ?? Community::factory(),
        ]);
    }

    public function member(?Community $community = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::MEMBER,
            'community_id' => $community?->id ?? Community::factory(),
        ]);
    }
    ```

- [x] Update `database/factories/MemberFactory.php`
  - [x] Ensure `community_id` is properly assigned in factory
  - [x] Add state method for creating members in specific communities
  - [x] Implementation pattern:
    ```php
    public function forCommunity(Community $community): static
    {
        return $this->state(fn (array $attributes) => [
            'community_id' => $community->id,
        ]);
    }
    ```

### 8. Documentation

- [x] Add inline comments to `ScopedByCommunity` trait explaining the pattern
  - [x] Document which roles bypass the scope (see Role Behavior Matrix)
  - [x] Document how to temporarily disable the scope (use class reference)
  - [x] Add PHPDoc blocks for all methods
  - [x] Add console command example for using withoutGlobalScope()

## Dev Notes

### Architecture Patterns & Constraints

**Laravel 11 Global Scopes:**

- Use `static::addGlobalScope()` in the `boot` method of the trait
- Scope receives a `Builder` instance and the `Model` class
- Access authenticated user via `Auth::user()`

**Strict Types:**

- `declare(strict_types=1);` in ALL new files

**Scope Removal Best Practice:**

- ✅ **Use plural method:** `Member::withoutGlobalScopes()` - removes ALL global scopes
- ✅ **For selective removal:** Implement named scope if needed: `Member::withoutGlobalScope('scopeName')`
- ❌ **Don't mix patterns:** Our implementation uses anonymous scopes (no name) for simplicity
- **Rationale:** Anonymous scopes are cleaner and prevent naming conflicts. Use `withoutGlobalScopes()` in seeders/commands

**Critical Pattern from Architecture:**
From `architecture.md` line 159:

> Every query must be scoped. `Member::where('house_id', $user->house_id)`.

We're implementing this via Global Scopes to avoid manual filtering in every controller.

**Role-Based Bypass:**
From Story 1.4, we know the roles are:

- `UserRole::SUPER_ADMIN` - Full access, bypass all scopes
- `UserRole::GENERAL` - Generalate staff, full access
- `UserRole::DIRECTOR` - Community Director, scoped to their house
- `UserRole::MEMBER` - Standard user, scoped to their house

### Implementation Pattern

**Global Scope Trait Structure:**

```php
<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait ScopedByCommunity
{
    /**
     * Boot the scoped by community trait for a model.
     *
     * PATTERN: Anonymous Global Scope
     * APPLIES TO: Director and Member roles
     * BYPASSES: Super Admin and General roles
     */
    protected static function bootScopedByCommunity(): void
    {
        // Use anonymous scope (no name) for simplicity
        static::addGlobalScope(function (Builder $builder) {
            // STEP 1: Get authenticated user
            $user = Auth::user();

            // STEP 2: Handle unauthenticated requests (console commands, API)
            if (!$user) {
                return;
            }

            // STEP 3: Bypass scoping for privileged roles
            // Use helper methods from User model for consistency
            if ($user->isSuperAdmin() || $user->hasRole(UserRole::GENERAL)) {
                return;
            }

            // STEP 4: Apply community scoping for Director and Member roles
            // Both roles are restricted to their assigned community
            if ($user->community_id) {
                $builder->where('community_id', $user->community_id);
            }
        });
    }
}
```

**Applying the Trait:**

```php
// In app/Models/Member.php
use App\Models\Concerns\ScopedByCommunity;

class Member extends Model
{
    use HasFactory, SoftDeletes, ScopedByCommunity;

    // ... rest of model
}
```

**Temporarily Disabling the Scope:**

```php
// Use plural method to remove all global scopes (recommended for anonymous scopes)
$allMembers = Member::withoutGlobalScopes()->get();

// Alternative: If you need to preserve other scopes, implement named scope
// static::addGlobalScope('community', function (Builder $builder) { ... });
// Then use: Member::withoutGlobalScope('community')->get();
```

**Console Command Usage:**

```php
// In Artisan commands, seeders, or jobs without authenticated user
// Bypass scoping for data operations that need cross-community access

// Remove all global scopes (recommended)
$allMembers = Member::withoutGlobalScopes()->get();

// Process all members across all communities
Member::withoutGlobalScopes()->each(function ($member) {
    // Process member regardless of community
});

// Direct member creation in seeders (requires scope bypass)
Member::withoutGlobalScopes()->factory()->count(50)->create();
```

**Seeder Best Practice:**

```php
// ✅ PREFERRED: Use factory relationships (scope bypass automatic)
Community::factory(3)
    ->has(Member::factory()->count(10))
    ->create();

// ⚠️ ONLY IF NEEDED: Direct member creation (requires explicit bypass)
Member::withoutGlobalScopes()->factory()->count(50)->create([
    'community_id' => $communityId,
]);
```

**Note:** The existing `DatabaseSeeder` (lines 54-57) uses factory relationships, which naturally bypasses the scope. Only use `withoutGlobalScopes()` when creating members directly without community context.

### Previous Story Intelligence

From Story 1.4 (RBAC Foundation):

- ✅ User model has `role` enum cast to `UserRole`
- ✅ User model has `community()` relationship defined
- ✅ User model has `hasRole()`, `isSuperAdmin()` helper methods
- ✅ `community_id` column exists on `users` table (nullable foreign key)
- ✅ All code uses `declare(strict_types=1);`
- ✅ Tests use factories and follow Feature/Unit organization

**Key Files from Story 1.4:**

- `app/Models/User.php` - Has role enum and community relationship
- `app/Enums/UserRole.php` - Defines all role constants
- `database/seeders/DatabaseSeeder.php` - Creates test users with communities

**Testing Patterns from Story 1.4:**

- Use `actingAs($user)` to authenticate test users
- Create users with specific roles using factories
- Test both positive and negative cases (can access / cannot access)
- Use descriptive test method names: `test_director_sees_only_their_community_members()`

### Git Intelligence

Recent commits show:

1.  **ed426e1**: Implemented comprehensive RBAC with permissions, roles, policies

    - Created `UserRole` and `PermissionKey` enums
    - Added `role` and `community_id` to users table
    - Established pattern of using enums for type safety

2.  **d8b96b2**: Implemented Community and Member models
    - Created migrations for `communities` and `members` tables
    - Both models use `SoftDeletes` trait
    - Established relationship: `Member` belongs to `Community`

**Code Patterns Established:**

- All new files use `declare(strict_types=1);`
- Models use `HasFactory` and `SoftDeletes` traits
- Relationships use return type hints
- Tests are comprehensive (20+ tests per story)

### Future Enhancements (Not in This Story)

- ❌ Scoping for other models (Expenses, Assignments) - will be added in their respective stories
- ❌ Audit logging of scope violations - future security enhancement
- ❌ UI indicator showing which community a Director is viewing - UX enhancement
- ❌ Ability for Super Admin to "impersonate" a Director's scoped view - admin tool

### Project Structure

```
app/
├── Models/
│   ├── Concerns/
│   │   └── ScopedByCommunity.php (NEW)
│   ├── Member.php (UPDATED)
│   └── User.php (EXISTING)
├── Enums/
│   └── UserRole.php (EXISTING)

tests/
├── Feature/
│   └── MemberScopingTest.php (NEW)
└── Unit/
    └── Concerns/
        └── ScopedByCommunityTest.php (NEW)
```

### References

- [Epics: Story 1.5](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/epics.md#story-15-house-scoped-data-access-for-directors)
- [Architecture: RBAC Pattern](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/architecture.md#authentication--security)
- [PRD: FR19](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/prd.md#security--access-control)
- [Laravel Global Scopes Documentation](https://laravel.com/docs/11.x/eloquent#global-scopes)
- [Previous Story: 1.4 RBAC Foundation](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/sprint-artifacts/stories/1-4-role-based-access-control-rbac-foundation.md)

## Definition of Done

- ✅ All 8 task groups completed
- ✅ All 15+ tests pass
- ✅ Code coverage ≥95% for scoping logic
- ✅ PHPStan level 8 passes
- ✅ No N+1 query issues introduced
- ✅ Trait is reusable for future models
- ✅ Documentation is complete with examples
- ✅ Directors can ONLY see their community's data
- ✅ Members can ONLY see their community's data
- ✅ Super Admins and Generals have full access
- ✅ Database index verified on community_id column

## File List

### New Files

- `managing-congregation/app/Models/Concerns/ScopedByCommunity.php`
- `managing-congregation/tests/Unit/Concerns/ScopedByCommunityTest.php`
- `managing-congregation/tests/Feature/MemberScopingTest.php`

### Modified Files

- `managing-congregation/app/Models/Member.php`
- `managing-congregation/database/factories/UserFactory.php`
- `managing-congregation/database/factories/MemberFactory.php`
- `docs/architecture.md`
- `docs/epics.md`

## Dev Agent Record

### Completion Notes

- Implemented `ScopedByCommunity` trait with anonymous global scope.
- Applied trait to `Member` model.
- Updated `UserFactory` and `MemberFactory` with helper methods.
- Verified database index on `members.community_id`.
- Added comprehensive tests covering all roles, edge cases (null community, unauthenticated), and N+1 prevention.
- Fixed a security flaw where Directors with null community_id could see all members (now see none).
- **Code Review Fix:** Hardened `ScopedByCommunity` to block unauthenticated access by default (secure-by-default).
- **Regression Fix:** Updated `SeederTest` to use `withoutGlobalScopes()` for verification.
- Updated `docs/architecture.md` and `docs/epics.md` with RBAC details.
- All 68 tests passed.

### ⚠️ SECURITY CRITICAL

This story implements the **foundational security pattern** for the entire application. If implemented incorrectly, Directors could access data from other communities, violating data privacy requirements.

**Common Mistakes to Avoid:**

1.  **❌ DON'T** manually add `where('community_id', ...)` in controllers

    - This is error-prone and easy to forget
    - Use the global scope instead

2.  **❌ DON'T** forget to bypass the scope for Super Admin and General

    - They need full access to all communities
    - Check role in the scope logic

3.  **❌ DON'T** apply the scope to the `Community` model itself

    - Only apply to models that belong to a community (Member, Expense, etc.)
    - Community model should be accessible to all roles

4.  **❌ DON'T** forget to handle unauthenticated users

    - API requests or console commands may not have an authenticated user
    - Check `Auth::user()` before accessing properties

5.  **❌ DON'T** use middleware for scoping
    - Middleware can be bypassed or forgotten on routes
    - Model scopes are more reliable and automatic

### ✅ TESTING CRITICAL

**Must Test All 4 Roles:**

- Director with community_id = 1 can ONLY see members with community_id = 1
- Member with community_id = 1 can ONLY see members with community_id = 1
- Director/Member with community_id = 1 CANNOT see members with community_id = 2
- Super Admin sees ALL members regardless of community_id
- General sees ALL members regardless of community_id
- Scope works with `Member::all()`, `Member::find()`, `Member::where()`, etc.
- Scope can be disabled with `withoutGlobalScopes()` (plural) when needed in seeders/commands
- Relationship queries (`$community->members`) correctly apply scoping

### 🎯 PERFORMANCE CRITICAL

**Query Efficiency:**

- The scope adds a `WHERE community_id = ?` clause to every query
- ✅ `community_id` column is indexed via `foreignId()` in migration `2025_12_02_035215_create_members_table.php`
- No N+1 queries should be introduced
- Test with `DB::enableQueryLog()` to verify query count
- Verify index exists: `SHOW INDEX FROM members WHERE Column_name = 'community_id'`

## Dev Agent Record

### Context Reference

- `docs/epics.md` - Epic 1, Story 1.5
- `docs/architecture.md` - RBAC and scoping patterns
- `docs/prd.md` - FR19 (House-scoped access requirement)
- `docs/sprint-artifacts/stories/1-4-role-based-access-control-rbac-foundation.md` - Previous story context

### Implementation Sequence

1. Create the `ScopedByCommunity` trait with comprehensive role checking
2. Apply trait to `Member` model
3. Write comprehensive tests (Unit + Feature)
4. Verify no regressions in existing tests
5. Document the pattern for future use

### Success Criteria

Story is complete when:

- A Director logged into the application can ONLY see members from their assigned community
- Super Admins and Generals can see all members from all communities
- All tests pass with ≥95% coverage
- No manual `where('community_id')` clauses exist in controllers
- The trait is documented and ready to be applied to other models (Expenses, Assignments) in future stories
