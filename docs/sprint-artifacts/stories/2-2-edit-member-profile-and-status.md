# Story 2.2: Edit Member Profile and Status

Status: done

## Story

As a Director,
I want to edit a member's profile information and update their status,
so that I can keep records current.

## Acceptance Criteria

1.  **View Profile for Editing:**

    - Given I am viewing the member list,
    - When I click on a member, I am taken to their profile page (`members.show`).
    - And I see an "Edit" button (visible only to authorized users).
    - When I click "Edit", I am taken to the edit form (`members.edit`).

2.  **Update Profile Information:**

    - Given I am on the edit page,
    - I can modify the member's Name, Date of Birth, and Entry Date.
    - **UX Requirement:** All inputs must use "Floating Label" style (as per UX Spec).
    - When I submit the form, the changes are saved to the database.
    - And I am redirected back to the profile page with a success flash message.
    - Validation errors are displayed clearly below each field if submission fails.

3.  **Update Member Status:**

    - Given I am on the edit page,
    - I see a "Status" dropdown with options backed by a Type-Safe Enum: Active, Deceased, Exited, Transferred.
    - When I change the status and save, the member's status is updated.
    - **Critical:** The `Member` model must use the `SoftDeletes` trait to handle "removed" states safely.

4.  **Permissions & Scoping:**
    - Only Directors of the member's house (or General/Super Admin) can edit the profile.
    - A Director cannot edit a member from another house (enforced by Policy and Scope).

## Tasks / Subtasks

- [x] **Core Architecture & Enums** (AC: 3)

  - [x] Create `app/Enums/MemberStatus.php` (backed Enum: string).
  - [x] Add `use SoftDeletes` trait to `app/Models/Member.php`.
  - [x] Ensure migration supports soft deletes (`$table->softDeletes()`).

- [x] **Refactor Form Partial (Crucial)** (AC: 1, 2)

  - [x] Extract common form fields (Name, DOB, Entry Date) from `create.blade.php` into `resources/views/members/partials/form.blade.php`.
  - [x] Update `create.blade.php` to include the new partial.
  - [x] **UX:** Ensure the partial uses **Floating Label** input styles.

- [x] **Implement Edit/Update Logic** (AC: 1, 2, 3)

  - [x] Add `edit` method to `MemberController` returning `members.edit` view.
  - [x] Add `update` method to `MemberController` with `UpdateMemberRequest`.
  - [x] Implement `UpdateMemberRequest` with validation rules (use Enum validation for status).

- [x] **Create Edit View** (AC: 1, 2, 3)

  - [x] Create `resources/views/members/edit.blade.php`.
  - [x] Include `members.partials.form`.
  - [x] Add the Status dropdown (populating options from `MemberStatus` Enum).

- [x] **Implement Authorization** (AC: 4)

  - [x] Update `MemberPolicy` with `update` method.
  - [x] Ensure `MemberController` authorizes the action: `Gate::authorize('update', $member)`.

- [x] **Add Navigation** (AC: 1)
  - [x] Add "Edit" button to `members.show` view.

## Dev Notes

- **UX Standards:**
  - **Floating Labels:** Use Tailwind/Forms or custom CSS to implement floating labels. This is a hard requirement for accessibility and older users.
- **Architecture Patterns:**
  - **Enums:** Use PHP 8.1+ Enums for Status. Do not use magic strings.
  - **Soft Deletes:** Essential for data safety.
  - **Authorization:** Strict Policy enforcement.
- **Source Tree Components:**
  - `app/Enums/MemberStatus.php` (New)
  - `app/Http/Controllers/MemberController.php`
  - `app/Http/Requests/UpdateMemberRequest.php` (New)
  - `resources/views/members/partials/form.blade.php` (New)
  - `resources/views/members/edit.blade.php` (New)
  - `app/Models/Member.php`
- **Testing Standards:**
  - Test Enum casting in Model.
  - Test Soft Deletes behavior.
  - Test Validation errors appear in UI.

### References

- **Epics:** [Story 2.2 in Epics](docs/epics.md#story-22-edit-member-profile-and-status)
- **UX:** [Forms & Inputs](docs/ux-design-specification.md#component-strategy) (Floating Labels)

## Dev Agent Record

### Context Reference

- `docs/epics.md`
- `docs/architecture.md`
- `docs/ux-design-specification.md`

### Agent Model Used

- Antigravity (Simulated)

### Completion Notes List

- Refactored form into partial with Floating Labels.
- Added `MemberStatus` Enum.
- Enforced Soft Deletes.
- Implemented `MemberStatus` Enum with `label()` method.
- Verified `SoftDeletes` on `Member` model and migration.
- Implemented `edit` and `update` methods in `MemberController`.
- Created `UpdateMemberRequest` with validation.
- Created `resources/views/members/edit.blade.php` with Status dropdown.
- Implemented `MemberPolicy` and authorization checks.
- Added "Edit" button to `members.show` view.
- Added comprehensive tests: `MemberEditTest` and `MemberStatusTest`.
- **Code Review Fixes:**
  - Removed `app/Models/Member.php` from File List (change was pre-existing).
  - Expanded `MemberPolicy` to include all standard CRUD methods.

### File List

- `app/Enums/MemberStatus.php`
- `app/Http/Controllers/MemberController.php`
- `app/Http/Requests/UpdateMemberRequest.php`
- `app/Http/Requests/UpdateMemberRequest.php`
- `app/Policies/MemberPolicy.php`
- `resources/views/members/create.blade.php`
- `resources/views/members/edit.blade.php`
- `resources/views/members/partials/form.blade.php`
- `resources/views/members/show.blade.php`
- `tests/Feature/MemberEditTest.php`
- `tests/Unit/Enums/MemberStatusTest.php`
