# Story 2.4: Visual Formation Timeline

**Status:** Done

## Story

As a Formation Directress,
I want to see a visual timeline of a sister's formation stages,
so that I can easily track her progress and ensure she meets all Canon Law requirements.

## Acceptance Criteria

1.  **Visual Timeline Display:**

    - Given I am on a member's profile page (`members.show`),
    - Then I see a `<x-feast-timeline>` component displaying the member's formation history.
    - The timeline shows events in chronological order (e.g., Postulancy, Novitiate, First Vows).
    - **UX:** Past events are dimmed, current/today is highlighted (Gold), future events are standard.
    - **UX:** Each event node shows the Stage Name and Date.

2.  **Add Formation Event:**

    - Given I am viewing the timeline,
    - When I click "Add Milestone" (or similar action),
    - Then a modal or form appears to record a new event.
    - I can select the "Stage" (Postulancy, Novitiate, First Vows, Final Vows) from a dropdown.
    - I can enter the "Start Date".
    - I can optionally add "Notes".
    - When I save, the timeline updates immediately (with a celebration animation if possible/simple).

3.  **Automated Date Calculations (Canon Law Logic):**

    - Given I add an event (e.g., "Entrance to Novitiate" on Jan 1, 2024),
    - Then the system automatically calculates the _expected_ date for the next stage (e.g., "First Vows" eligibility = Jan 1, 2025 + 1 year = Jan 1, 2026).
    - This calculated "Future Event" appears on the timeline as a "Projected" node.
    - **Business Logic:**
      - Postulancy -> Novitiate (Variable, usually 6mo-2yr)
      - Novitiate -> First Vows (Min 1 year, strictly 12 months)
      - First Vows -> Final Vows (Min 3 years, usually 5-6 years, Max 9 years)

4.  **Data Integrity:**
    - A member cannot have two overlapping stages (logic handled in Service).
    - Dates must be valid.
    - **Security:** Only authorized users (e.g., Formation Directress, Admin) can add events.

## Tasks / Subtasks

- [ ] **Backend Implementation**

  - [x] **Enums & Constants:**
    - [x] Create `app/Enums/FormationStage.php` (Postulancy, Novitiate, FirstVows, FinalVows).
    - [x] Define Canon Law duration constants (e.g., `NOVITIATE_MIN_MONTHS = 12`) in `FormationService` or a dedicated config/class.
  - [x] **Database:**
    - [x] Create `FormationEvent` model and migration (`formation_events` table).
      - Columns: `id`, `member_id`, `stage` (string/enum), `started_at` (date), `notes` (text), `timestamps`.
  - [x] **Request Validation:**
    - [x] Create `app/Http/Requests/StoreFormationEventRequest.php`.
      - Validate `stage` against Enum.
      - Validate `started_at` is a valid date.
      - Validate `member_id` exists.
  - [x] **Authorization:**
    - [x] Create `app/Policies/FormationPolicy.php`.
      - Define `create` and `view` methods checking user permissions/roles.
    - [x] Register policy in `AppServiceProvider` (if not auto-discovered).
  - [x] **Service Layer:**
    - [x] Create `app/Services/FormationService.php`.
      - Implement `calculateNextStageDate(FormationStage $currentStage, Carbon $startDate): ?Carbon`.
      - Implement `addEvent(Member $member, array $data): FormationEvent`.
      - Ensure logic uses the defined constants.
  - [x] **Controller:**
    - [x] Create `app/Http/Controllers/FormationController.php`.
      - Implement `store(StoreFormationEventRequest $request, Member $member)`.
      - Delegate logic to `FormationService`.
      - Return redirect with flash message.
  - [x] **Routing:**
    - [x] Register route in `routes/web.php`: `POST /members/{member}/formation` (name: `members.formation.store`).

- [x] **Frontend Implementation**

  - [x] Create `<x-feast-timeline>` component (`resources/views/components/feast-timeline.blade.php`).
    - Render timeline nodes based on `formationEvents`.
    - Style using "Sanctuary & Stone" palette (Gold/White).
  - [x] Update `resources/views/members/show.blade.php`.
    - Include `<x-feast-timeline>` above the details grid.
  - [x] Create "Add Milestone" Modal (using Alpine.js).
    - Form with `stage` (select), `started_at` (date), `notes` (textarea).
    - Submit to `members.formation.store`.
    - Ensure CSRF token and Method spoofing (if needed) are present.

- [ ] **Testing**
  - [x] Create `tests/Unit/FormationServiceTest.php`.
    - Test date calculations for Novitiate (1 year) and Vows (3 years) using constants.
  - [x] Create `tests/Feature/FormationTest.php`.
    - Test adding an event as authorized user.
    - Test adding an event as unauthorized user (403 Forbidden).
    - Test validation errors (invalid date, missing stage).
    - Test that timeline renders correctly.

## Dev Notes

- **Coding Standards (CRITICAL):**

  - **Strict Types:** ALL new PHP files (Models, Controllers, Services, Requests, Policies, Tests) MUST start with `declare(strict_types=1);`.
  - **Validation:** ALWAYS use FormRequests. NEVER validate in the Controller.
  - **Authorization:** ALWAYS use Policies. Check `can('create', ...)` in Controller or Request.

- **Architecture Patterns:**

  - **Service Layer:** ALL date calculation logic MUST live in `FormationService`. Do not put Canon Law math in the Controller or Model.
  - **Enums:** Use `FormationStage` Enum for all stage references to prevent "magic strings".
  - **Scoping:** Ensure `FormationController` checks that the user has permission to edit this member (Policy check).

- **UX Standards:**
  - **Timeline:** This is a "Delight" feature. Make it look polished. Use Tailwind colors from the "Sanctuary & Stone" palette (Gold for current/important).
  - **Feedback:** Use `session()->flash` to show success messages.

### Project Structure Notes

- **Model:** `app/Models/FormationEvent.php`
- **Enum:** `app/Enums/FormationStage.php`
- **Request:** `app/Http/Requests/StoreFormationEventRequest.php`
- **Policy:** `app/Policies/FormationPolicy.php`
- **Service:** `app/Services/FormationService.php`
- **Controller:** `app/Http/Controllers/FormationController.php`
- **View Component:** `resources/views/components/feast-timeline.blade.php`

### References

- **Epics:** [Story 2.4 in Epics](docs/epics.md#story-24-visual-formation-timeline)
- **PRD:** FR6, FR7
- **Architecture:** [Service Layer Pattern](docs/architecture.md#service-boundaries), [RBAC](docs/architecture.md#core-architectural-decisions)
- **UX:** [Feast Timeline Component](docs/ux-design-specification.md#2-the-feast-timeline-x-feast-timeline)

## Dev Agent Record

### Context Reference

- `docs/epics.md`
- `docs/architecture.md`
- `docs/ux-design-specification.md`
- `docs/sprint-artifacts/stories/2-3-member-search.md`

### Agent Model Used

- Antigravity (Simulated)

### Debug Log References

### Completion Notes List

**Code Review Fixes Applied (2025-12-03):**

1. **H1 - Created `FormationEventFactory.php`**: Added missing factory with helper methods for each formation stage (postulancy, novitiate, firstVows, finalVows) to enable proper test data generation.

2. **H2 - Added `declare(strict_types=1);` to Migration**: Fixed coding standard violation in `2025_12_03_085022_create_formation_events_table.php` by adding strict types declaration.

3. **H3 - Added Database Index**: Added explicit index on `formation_events.member_id` for improved query performance when loading member timelines.

4. **M2 - Clarified Authorization Logic**: Updated comments in `FormationController::store()` to explain why both role-based (Gate) and community-scoped (Policy) checks are necessary.

5. **M3 - Implemented AC #3 (Projected Future Events)**:

   - Updated `MemberController::show()` to calculate projected next stage dates using `FormationService::calculateNextStageDate()`
   - Enhanced `feast-timeline.blade.php` to render projected events with dashed borders and italic styling
   - Timeline now displays: past events (dimmed), current/today (gold), future events (standard), and projected events (gray, dashed, italic)
   - Projected events show expected dates for next formation stages based on Canon Law minimums

6. **M1 & L1 - Updated File List**: Reorganized File List into "New Files" and "Modified Files" sections, removed duplicate entry, added all 9 modified files that were missing from documentation.

**Implementation Details:**

- Projected events use `FormationService` to calculate next stage dates (Novitiate→FirstVows after 12 months, FirstVows→FinalVows after 36 months)
- Timeline component accepts optional `projectedEvents` prop with array of `['stage' => FormationStage, 'date' => Carbon]`
- Visual distinction: solid border for actual events, dashed border for projections
- UX follows story requirements: past dimmed, current highlighted gold, future standard, projected clearly marked

### File List

**New Files:**

- `app/Enums/FormationStage.php`
- `app/Services/FormationService.php`
- `app/Models/FormationEvent.php`
- `database/migrations/2025_12_03_085022_create_formation_events_table.php`
- `app/Http/Requests/StoreFormationEventRequest.php`
- `app/Policies/FormationPolicy.php`
- `app/Http/Controllers/FormationController.php`
- `resources/views/components/feast-timeline.blade.php`
- `database/factories/FormationEventFactory.php`
- `tests/Unit/Enums/FormationStageTest.php`
- `tests/Unit/FormationServiceTest.php`
- `tests/Unit/FormationEventTest.php`
- `tests/Unit/Http/Requests/StoreFormationEventRequestTest.php`
- `tests/Unit/Policies/FormationPolicyTest.php`
- `tests/Feature/FormationTest.php`

**Modified Files:**

- `docs/sprint-artifacts/sprint-status.yaml`
- `app/Http/Controllers/MemberController.php`
- `app/Models/Member.php`
- `app/Providers/AppServiceProvider.php`
- `database/factories/CommunityFactory.php`
- `database/factories/MemberFactory.php`
- `resources/views/members/index.blade.php`
- `resources/views/members/show.blade.php`
- `routes/web.php`
