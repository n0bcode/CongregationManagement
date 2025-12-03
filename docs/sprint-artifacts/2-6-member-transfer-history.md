# Story 2.6: Member Transfer History

Status: done

## Story

As an **Admin**,
I want **to transfer a member from one house to another and see a complete history of their assignments**,
so that **we have a full record of their service**.

## Acceptance Criteria

1.  Given I am on a member's profile page, when I activate the "Transfer Member" action, I can select a new community and a date of transfer.
2.  Upon confirmation, the member's current `community_id` in the `members` table is updated to the newly selected community.
3.  Upon confirmation, a new historical record is created in the `assignments` table, logging the old community, the new community, and the transfer date.
4.  The member's profile page must display a "Service History" section that lists all past and present assignments in a clear, chronological order.
5.  The transfer action must be recorded in the `audit_events` table, logging who made the transfer, which member was transferred, and the source/destination communities. [Source: `docs/prd.md` - FR20]
6.  The user interface for the transfer should be simple, pre-filling information where possible and requiring confirmation before executing the change. [Source: `docs/ux-design-specification.md` - "Review, Don't Type" & "Forgiving by Design"]

## Tasks / Subtasks

- [ ] **Backend: Create `assignments` table** (AC: #3)
  - [ ] Create migration for the `assignments` table. It should include `member_id`, `community_id`, `role`, `start_date`, and `end_date`. The `role` column (string) snapshots the member's `UserRole` at the time of assignment.
  - [ ] Create the `Assignment.php` model.
  - [ ] Define the relationship between `Member` and `Assignment` models (`hasMany`).
- [ ] **Backend: Implement Authorization**
  - [ ] Create or update `MemberPolicy.php` to include a `transfer(User $user, Member $member)` method.
  - [ ] The policy should allow 'Super Admin' and 'General' roles to transfer any member.
  - [ ] It should prevent a 'Director' from transferring a member who is not in their own community.
- [ ] **Backend: Implement Transfer Logic** (AC: #1, #2, #3, #5)
  - [ ] Create a `MemberTransferController` with a `store(StoreMemberTransferRequest $request, Member $member)` method.
  - [ ] Before executing logic, authorize the action: `$this->authorize('transfer', $member);`.
  - [ ] Create a `StoreMemberTransferRequest` FormRequest for validation (rules: `new_community_id` is required and exists, `transfer_date` is a valid date).
  - [ ] Implement the `store` method logic within a database transaction.
  - [ ] The transaction should:
    - Update the `end_date` of the member's _current_ assignment record (if one exists).
    - Update the `community_id` on the `members` table.
    - Create a new record in the `assignments` table with the `start_date` set to the transfer date.
  - [ ] Add the corresponding route in `routes/web.php`.
  - [ ] Ensure the action is logged by the `AuditObserver`.
- [ ] **Frontend: Build Transfer UI** (AC: #1, #6)
  - [ ] On the member profile page (`resources/views/members/show.blade.php`), add a "Transfer Member" button, visible only to authorized users (`@can('transfer', $member)`).
  - [ ] The button should trigger a modal form.
  - [ ] The form should contain a dropdown to select the new community and a date picker for the transfer date.
- [ ] **Frontend: Display Service History** (AC: #4)
  - [ ] On the member profile page, query and pass the member's assignments history from the controller.
  - [ ] Create a new Blade component (`<x-service-history-list>`) to display the assignments in a clear, timeline-like format.
- [ ] **Testing**
  - [ ] Write a Feature test to simulate the entire transfer process, including authorization checks for different roles.
  - [ ] Test that a Director _cannot_ transfer a member from another community.
  - [ ] Test that an Admin _can_ transfer any member.
  - [ ] Verify all database changes (members, assignments, audit_events) and the UI update.

## Dev Notes

- **CRITICAL: Global Scope Awareness:** This application uses a `ScopeByHouse` global scope on the `Member` model. When an Admin or General Secretary performs a transfer, queries for members outside their own community (or for a member's previous assignments in other communities) might fail. The transfer logic **must** use `Member::withoutGlobalScope(ScopeByHouse::class)->...` where appropriate to prevent bugs.
- **Architecture:** The core pattern is creating an immutable historical record (`assignments`) while updating the member's current state (`members.community_id`). This aligns with the "Strict Relational Model" and "Service History" logic.
- **Transaction is Key:** The entire transfer operation **must** be wrapped in a `DB::transaction()` to ensure data integrity. If any part fails, the whole operation must be rolled back.
- **Audit Trail:** This is a critical action. Ensure the `AuditObserver` is correctly triggered. This is a cross-cutting concern that must be verified. [Source: `docs/architecture.md`#Cross-Cutting-Concerns-Identified]
- **Soft Deletes:** Remember that `Member` and `Community` models use soft deletes. Queries should respect this.

### Project Structure Notes

- **Controller:** `app/Http/Controllers/MemberTransferController.php`
- **Policy:** `app/Policies/MemberPolicy.php`
- **Model:** `app/Models/Assignment.php`
- **Migration:** `database/migrations/xxxx_xx_xx_create_assignments_table.php`
- **Request:** `app/Http/Requests/StoreMemberTransferRequest.php`
- **View (for history):** `resources/views/members/show.blade.php`
- **Component:** `resources/views/components/service-history-list.blade.php`

### References

- [Source: `docs/epics.md`#Story-2.6-Member--Transfer-History]
- [Source: `docs/prd.md`#Community-&-Housing]
- [Source: `docs/architecture.md`#Data-Architecture]
- [Source: `docs/ux-design-specification.md`#User-Journey-Flows]

## Dev Agent Record

### Context Reference

<!-- Path(s) to story context XML will be added here by context workflow -->

### Agent Model Used

{{agent_model_name_version}}

### Debug Log References

### Completion Notes List

### File List
