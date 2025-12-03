# Story 2.1: Create and View Member Profiles

Status: Done

## Story

As a **Community Director**,
I want to **create a new member profile and view a list of all members in my community**,
so that **I have a basic roster and can manage my community's membership**.

## Context

This story is the **core entry point** for the Member Management module. It establishes the `MemberController` and the basic CRUD operations for members. Crucially, it must integrate with the **House-Scoped Data Access** pattern established in Story 1.5 to ensure Directors only see their own members.

**Key Architectural Dependencies:**

- **RBAC & Scoping:** Must use `ScopedByCommunity` trait on `Member` model (implemented in Story 1.5).
- **Validation:** Must prevent duplicate entries (Civil Name + DOB) to maintain data integrity.
- **UX Pattern:** "Pastoral Dashboard" list view (from UX Spec) - simple, clear, card-based or list-based.

## Acceptance Criteria

### AC1: Member List View (Scoped)

- Given I am a logged-in Director assigned to "House of Bethany",
- When I navigate to the `/members` route,
- Then I see a list of members belonging ONLY to "House of Bethany".
- And I do NOT see members from other houses.
- And the list displays: Name, Religious Name (if any), and Status.
- **Technical Note:** The controller `index` method should rely on the Global Scope from Story 1.5, NOT manual `where()` clauses.

### AC2: Create Member Form

- Given I am on the member list page,
- When I click the "Create Member" button,
- Then I am taken to a form with the following mandatory fields:
  - **First Name** (Civil)
  - **Last Name** (Civil)
  - **Religious Name** (Optional)
  - **Date of Birth**
  - **Entry Date** (Postulancy Date)
- And the "Community" field is automatically set to my assigned house (hidden or read-only).

### AC3: Member Creation & Validation

- Given I have filled out the form,
- When I submit it with valid data,
- Then a new `Member` record is created in the database.
- And I am redirected to the member's profile or the list with a success message "Member created successfully."
- And the new member is automatically assigned to my `community_id`.

### AC4: Duplicate Prevention (FR5)

- Given a member "Mary Smith" born "1980-01-01" already exists,
- When I try to create another member with the exact same Civil Name and DOB,
- Then the system prevents the creation.
- And shows a user-friendly error: "A member with this name and date of birth already exists."

### AC5: Super Admin / General Access

- Given I am a Super Admin or General Secretary,
- When I view the member list,
- Then I see ALL members from ALL communities.
- And the list includes a "Community" column/badge to identify where they belong.

### AC6: Member Detail View

- Given I am a logged-in Director,
- When I click on a member's name in the list,
- Then I am taken to the member's profile page (`/members/{id}`).
- And I see their full details (Civil Name, Religious Name, DOB, Entry Date, Status).
- And I can only see members from my own community (404 or 403 if trying to access others).

## Tasks / Subtasks

### 1. Member Controller & Routes

- [x] Create `MemberController` (if not exists)
  - [x] Define `index` method (return view with paginated members)
  - [x] Define `create` method (return form view)
  - [x] Define `store` method (handle submission)
  - [x] Define `show` method (return profile view)
- [x] Register resource routes in `routes/web.php`
  - [x] Ensure routes are protected by `auth` middleware

### 2. Create Member Request Validation

### 2. Create Member Request Validation

- [x] Create `app/Http/Requests/StoreMemberRequest.php`
  - [x] Add rules for:
    - `first_name`: required, string, max:255
    - `last_name`: required, string, max:255
    - `religious_name`: nullable, string, max:255
    - `dob`: required, date, before:today
    - `entry_date`: required, date
  - [x] Implement "Unique Civil Name + DOB" validation rule
    - Use `Rule::unique('members')->where(...)` logic

### 3. Model Updates

- [x] Update `app/Models/Member.php`
  - [x] Add `$fillable` attributes: `first_name`, `last_name`, `religious_name`, `dob`, `entry_date`, `community_id`.
  - [x] Add `$casts`: `dob` => `date`, `entry_date` => `date`.

### 3. Views Implementation (Blade + Tailwind)

- [x] Create `resources/views/members/index.blade.php`
  - [x] Use `x-app-layout`
  - [x] Display list of members (use a simple table or card layout)
  - [x] Add "Create Member" button (visible to authorized roles)
- [x] Create `resources/views/members/create.blade.php`
  - [x] Form with CSRF token
  - [x] Input fields with error message display
  - [x] Input fields with error message display
  - [x] "Cancel" and "Save" buttons
- [x] Create `resources/views/members/show.blade.php`
  - [x] Display member details
  - [x] "Back to List" button

### 5. Controller Logic Implementation

### 5. Controller Logic Implementation

- [x] Implement `index` method
  - [x] `Member::paginate(20)` (Scope handles filtering automatically)
- [x] Implement `store` method
  - [x] Use `StoreMemberRequest` for validation
  - [x] Handle `community_id`:
    - If User is Director/Member: Use `Auth::user()->community_id`.
    - If User is Super Admin: Require `community_id` from input (add to form if Super Admin) OR default to a specific logic. _Decision: For MVP, Super Admin can select community in form._
  - [x] Create member: `Member::create($data)`
- [x] Implement `show` method
  - [x] Implicit binding `show(Member $member)` should work with Global Scope (automatically 404 if not in scope).
  - [x] Use `StoreMemberRequest` for validation
  - [x] Create member: `Member::create($request->validated() + ['community_id' => Auth::user()->community_id])`
    - **Note:** For Super Admin creating members, allow selecting community (future story? assume Director context for now or handle null) -> _Refinement: If Super Admin, require community_id input._

### 6. Testing (Feature Tests)

### 6. Testing (Feature Tests)

- [x] Create `tests/Feature/MemberManagementTest.php`
  - [x] Test: Director can view own members
  - [x] Test: Director can create member
  - [x] Test: Member is assigned to Director's community
  - [x] Test: Duplicate validation fails
  - [x] Test: Required fields validation
  - [x] Test: Required fields validation
  - [x] Test: Super Admin sees all members
  - [x] Test: Director can view single member profile
  - [x] Test: Director cannot view member from another community (404)

## Dev Notes

### Architecture Patterns & Constraints

- **Global Scope:** Rely on `ScopedByCommunity` trait (Story 1.5). Do NOT add `where('community_id', ...)` in `index` method.
- **Validation:** Use FormRequests (`StoreMemberRequest`), not controller validation.
- **Naming:** `MemberController`, `Member` model, `members` table.
- **Strict Types:** `declare(strict_types=1);` in all files.

### UX Guidelines

- **List View:** Use a clean, readable table or list. Show "Status" with a badge (Active/Deceased).
- **Form:** Use floating labels or clear labels. Date inputs should be browser-native date pickers.
- **Feedback:** Use `session()->flash('status', ...)` for success messages.

### References

- [Epics: Story 2.1](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/epics.md#story-21-create-and-view-member-profiles)
- [PRD: FR1, FR5](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/prd.md#member-management)
- [Architecture: Naming Conventions](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/architecture.md#naming-patterns)
- [Previous Story: 1.5 Scoping](file:///media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/sprint-artifacts/stories/1-5-house-scoped-data-access-for-directors.md)

## Dev Agent Record

### Context Reference

- `docs/epics.md` - Story 2.1
- `docs/sprint-artifacts/stories/1-5-house-scoped-data-access-for-directors.md` - Scoping pattern

### Agent Model Used

- Gemini 2.0 Flash

### Completion Notes List

- [ ] Confirmed `ScopedByCommunity` trait usage
- [ ] Implemented duplicate check logic
- [ ] Verified Super Admin access patterns
