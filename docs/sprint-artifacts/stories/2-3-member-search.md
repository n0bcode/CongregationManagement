# Story 2.3: Member Search

Status: done

## Story

As a General Secretary (and Director),
I want to search for any member in the congregation by name,
so that I can find their records quickly.

## Acceptance Criteria

1.  **Global Search Functionality:**

    - Given I am a logged-in user with permission to view members,
    - When I enter a search term in the search bar (Name/Religious Name or Civil Name),
    - Then the member list is filtered to show only matching records.
    - The search is case-insensitive and partial match (e.g., "mar" matches "Mary", "Martha").
    - **Schema Note:** Search covers `religious_name`, `first_name`, and `last_name` columns.

2.  **Scope-Aware Results:**

    - **General Secretary / Super Admin:** Search results include members from ALL communities.
    - **Community Director:** Search results include ONLY members from their assigned community.
    - **System Constraint:** The search query MUST respect the global/house scope defined in Story 1.5.

3.  **Search Interface:**

    - The search bar is prominent on the Member List page (`members.index`).
    - **UX:** Real-time filtering (via Livewire/Alpine) or "Press Enter to Search" (Server-side) is acceptable. For MVP, Server-side `?search=` param is preferred for simplicity.
    - **UX:** Include a "Clear" (X) button or link when a search is active to easily reset the list.
    - If no results are found, a friendly "No members found matching 'xyz'" message is displayed.

4.  **Navigation:**
    - Clicking a result row takes me to that member's profile (`members.show`).

## Tasks / Subtasks

- [x] **Backend Implementation** (AC: 1, 2)

  - [x] Update `app/Models/Member.php` with a `scopeSearch($query, $term)` method.
    - Should search `religious_name`, `first_name`, and `last_name`.
    - **Critical:** Schema was updated to use `religious_name`, `first_name`, `last_name` (not `name`, `civil_name`).
  - [x] Update `MemberController@index` to handle `request('search')`.
    - Apply `Member::search($term)` before pagination.
    - **Critical:** Ensure pagination links append the search term: `$members->appends(['search' => $search])`.
    - Ensure `ScopeByHouse` (or global scope) is still applied _before_ or _with_ the search scope.

- [x] **Frontend Implementation** (AC: 3, 4)

  - [x] Update `resources/views/members/index.blade.php`.
  - [x] Add a Search Bar component above the table.
    - Input field with "Search members..." placeholder.
    - Search icon/button.
  - [x] Ensure search term persists in the input after search (value="{{ request('search') }}").
  - [x] Add "No results" state to the table.

- [x] **Testing**
  - [x] Create `tests/Feature/MemberSearchTest.php`.
  - [x] Test: General Secretary finds members from any house.
  - [x] Test: Director finds members ONLY from their house (even if name matches a member in another house).
  - [x] Test: Partial matches work.

## Dev Notes

- **Architecture Patterns:**

  - **Scoping:** The beauty of the `ScopeByHouse` global scope (from Story 1.5) is that `Member::search('Mary')->get()` should AUTOMATICALLY be scoped to the Director's house if the global scope is working correctly. **Verify this.** Do not manually add `where('community_id'...)` in the controller if the global scope handles it.
  - **Search Logic:** Use `where(function($q) use ($term) { $q->where('name', 'like', "%{$term}%")->orWhere('civil_name', 'like', "%{$term}%"); })` to group OR clauses correctly so they don't override the House scope AND clause.
  - **Simplicity:** Do NOT use Laravel Scout or external search engines (Algolia/Meilisearch). Standard Eloquent `LIKE` queries are sufficient for this MVP.

- **UX Standards:**
  - **Input:** Use standard Tailwind input styles (rounded, accessible focus ring).
  - **Feedback:** If using server-side search, ensure the page reload is fast.

### Project Structure Notes

- **Model:** `app/Models/Member.php`
- **Controller:** `app/Http/Controllers/MemberController.php`
- **View:** `resources/views/members/index.blade.php`

### References

- **Epics:** [Story 2.3 in Epics](docs/epics.md#story-23-member-search)
- **PRD:** FR4 (Search)
- **Architecture:** [Query Scoping](docs/architecture.md#critical-patterns)

## Dev Agent Record

### Context Reference

- `docs/epics.md`
- `docs/architecture.md`
- `docs/ux-design-specification.md`
- `docs/sprint-artifacts/stories/2-2-edit-member-profile-and-status.md`

### Agent Model Used

- Antigravity (Simulated)

### Debug Log References

### Completion Notes List

- Implemented `scopeSearch` in `Member` model to handle searching by religious name, first name, and last name.
- Updated `MemberController` to apply the search scope when a search term is present.
- Added a search bar to the `members.index` view with persistence and clear button.
- Created `MemberSearchTest` to verify functionality.
- **Code Review Fixes Applied:**
  - Added `declare(strict_types=1)` to test file
  - Updated empty state message to show search term when active
  - Created migration to add indexes on search columns for performance
  - Updated story documentation to match actual schema implementation

### File List

- `managing-congregation/app/Models/Member.php`
- `managing-congregation/app/Http/Controllers/MemberController.php`
- `managing-congregation/resources/views/members/index.blade.php`
- `managing-congregation/tests/Feature/MemberSearchTest.php`
- `managing-congregation/database/migrations/2025_12_03_150000_add_indexes_to_member_search_columns.php`
- `docs/sprint-artifacts/sprint-status.yaml`
- `docs/sprint-artifacts/stories/2-3-member-search.md`
