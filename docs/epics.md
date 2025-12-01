# Managing the Congregation (at the organizational level) - Epic Breakdown

**Author:** Wavister
**Date:** 2025-12-01
**Project Level:** {{project_level}}
**Target Scale:** {{target_scale}}

---

## Overview

This document provides the complete epic and story breakdown for Managing the Congregation (at the organizational level), decomposing the requirements from the [PRD](./PRD.md) into implementable stories.

**Living Document Notice:** This is the initial version. It will be updated after UX Design and Architecture workflows add interaction and technical details to stories.

### Proposed Epic Structure

Based on the PRD, Architecture, and UX documents, I am structuring the work into four main epics. Each is designed to deliver a distinct piece of user value, building upon the last.

**Epic 1: Foundation & Core Setup**

- **User Value:** Provides the fundamental technical infrastructure, security, and data models for the entire application to run on. This is a technical epic that enables all future user-facing features.
- **PRD Coverage:** FR18, FR19, FR20, FR21 (Core Security & Access), NFR1-NFR4 (Security), NFR8-NFR10 (Reliability).
- **Technical Context:** Implements the "Laravel Breeze" starter, Docker setup, and the critical RBAC policies defined in the Architecture doc. Sets up the initial database schema for `users`, `communities`, and `members`.
- **UX Integration:** Establishes the basic app layout, login/password reset screens, and responsive design foundation from the UX Spec.
- **Dependencies:** None. This is the starting point.

**Epic 2: Member Lifecycle & Profile Management**

- **User Value:** Allows the congregation to create, view, update, and manage member profiles, including their unique spiritual journey from Postulancy to Final Vows. This provides a "single source of truth" for every sister.
- **PRD Coverage:** FR1-FR9 (Member & Formation), FR16, FR17 (Transfers).
- **Technical Context:** Builds out the `MemberController` and `FormationController`. Implements the `FormationService` for date calculations and the `assignments` table for service history. Leverages the `FileStorageService` for secure document uploads (Baptismal Certs).
- **UX Integration:** Implements the "Spiritual Timeline" (`<x-feast-timeline>`) and the core member profile view. Focuses on the "Pastoral Dashboard" design direction for Sr. Teresa.
- **Dependencies:** Epic 1.

**Epic 3: Community Financial Stewardship**

- **User Value:** Dramatically reduces administrative stress by providing Community Directors with an incredibly simple and forgiving system for tracking daily expenses and generating their monthly financial report.
- **PRD Coverage:** FR10-FR14 (Financials), FR15 (Community Creation).
- **Technical Context:** Implements the `FinancialController` and the `PdfService`. Uses Laravel Policies to ensure Sr. Mary can only see her own community's finances. Leverages the `ScopeByHouse` middleware heavily.
- **UX Integration:** Implements "The Digital Ledger" (`<x-ledger-row>`) for a simple, non-intimidating expense entry experience. This directly delivers the "Stress-Free Monthly Close" journey.
- **Dependencies:** Epic 1, Epic 2 (for member assignment to communities).

**Epic 4: Strategic Oversight & Reporting**

- **User Value:** Empowers the Generalate with high-level dashboards, reporting, and alerts for effective and strategic governance of the entire congregation.
- **PRD Coverage:** FR13 (View all reports), FR4 (Search), Dashboard requirements from User Journeys.
- **Technical Context:** Builds out the main administrative dashboard, leveraging model scopes and optimized queries to generate statistics. Implements global search functionality.
- **UX Integration:** Implements the "Exception Reporting" and "Pastoral Status Card" flows for Sr. Anne, using high-level `Pastoral Status Cards` to summarize data.
- **Dependencies:** Epics 1, 2, and 3.

---

## Functional Requirements Inventory

- **FR1:** Admin/Director can create a new member profile with mandatory fields (Name, DOB, Entry Date).
- **FR2:** Admin/Director can upload and crop a profile photo for a member.
- **FR3:** Admin/Director can update member status (Active, Deceased, Exited, Transferred).
- **FR4:** Admin/Director can search for members by name, religious name, or civil name.
- **FR5:** System must prevent duplicate member entries based on Civil Name + DOB.
- **FR6:** Admin/Director can view a visual timeline of a member's formation stages (Postulancy, Novitiate, Vows).
- **FR7:** System must automatically calculate eligibility dates for the next stage based on Canon Law rules (e.g., 1 year for Novitiate).
- **FR8:** Admin/Director can upload documents (Baptismal Cert, Health Report) to a specific formation stage.
- **FR9:** System must display a "Critical Alert" on the dashboard for vows expiring within 30 days.
- **FR10:** Community Director can enter a daily expense with Category, Amount, Date, and Description.
- **FR11:** Community Director can upload a receipt image/PDF for any expense > threshold (configurable).
- **FR12:** Community Director can generate a "Monthly Financial Report" PDF that aggregates expenses by category.
- **FR13:** General Treasurer can view read-only financial reports from all communities.
- **FR14:** System must lock financial records after the monthly report is submitted.
- **FR15:** Super Admin can create new Houses/Communities and assign a Community Director.
- **FR16:** Admin can transfer a member from one House to another (updating their current location).
- **FR17:** System must maintain a history log of all member transfers (Service History).
- **FR18:** Super Admin can create user accounts and assign roles (General, Director, Member).
- **FR19:** System must restrict Community Directors to view/edit ONLY members and finances of their assigned House.
- **FR20:** System must log all critical actions (Create, Delete, Transfer) in an audit trail visible to Super Admin.
- **FR21:** Users can reset their own passwords via email link.

---

## FR Coverage Map

### Member Management

- **FR1:** Create Member → **Story 2.1** (Create and View Member Profiles)
- **FR2:** Upload Photo → **Story 2.7** (Member Profile Photo Management)
- **FR3:** Update Status → **Story 2.2** (Edit Member Profile and Status)
- **FR4:** Search Members → **Story 2.3** (Member Search)
- **FR5:** Prevent Duplicates → **Story 2.1** (Create and View Member Profiles)

### Formation Tracking

- **FR6:** Visual Timeline → **Story 2.4** (Visual Formation Timeline)
- **FR7:** Auto-Calculate Dates → **Story 2.4** (Visual Formation Timeline)
- **FR8:** Upload Documents → **Story 2.5** (Secure Document Upload for Formation)
- **FR9:** Critical Vow Alerts → **Story 4.2** (Critical Vow Expiry Alerts)

### Financial Management

- **FR10:** Daily Expense Entry → **Story 3.1** (Daily Expense Entry)
- **FR11:** Receipt Upload → **Story 3.2** (Receipt Upload)
- **FR12:** Monthly PDF Report → **Story 3.3** (One-Click Monthly PDF Report)
- **FR13:** View All Reports → **Story 4.3** (View All Financial Reports)
- **FR14:** Lock Records → **Story 3.4** (Locking Financial Records)

### Community & Housing

- **FR15:** Create Communities → **Story 3.5** (Community & House Creation)
- **FR16:** Transfer Member → **Story 2.6** (Member Transfer History)
- **FR17:** Transfer History → **Story 2.6** (Member Transfer History)

### Security & Access Control

- **FR18:** Create Users/Roles → **Story 1.4** (Role-Based Access Control Foundation)
- **FR19:** House Scoping → **Story 1.5** (House-Scoped Data Access for Directors)
- **FR20:** Audit Trail → **Story 4.4** (Audit Trail Log)
- **FR21:** Password Reset → **Story 1.3** (User Authentication)

---

## Epic 1: Foundation & Core Setup

**Epic Goal:** To establish the complete technical foundation of the application, including project setup, authentication, authorization, and core data models, so that future development of user-facing features can proceed securely and efficiently.

### Story 1.1: Project Initialization & Environment Setup

- **User Story:** As a Developer, I want to initialize the Laravel project and set up a local Docker environment, so that I can begin building the application consistently.
- **Acceptance Criteria:**
  - Given the project directory is empty,
  - When I run the initialization commands from the Architecture doc (`composer create-project`, `breeze:install`),
  - Then a new Laravel project is created with Blade, Tailwind CSS, and Alpine.js installed.
  - And when I run `./vendor/bin/sail up`,
  - Then a local development environment with PHP, MySQL, and Mailpit is running.
- **Technical Implementation:** Follows "Selected Starter: Laravel Breeze" section of `architecture.md`.

### Story 1.2: Core Data Model & Migrations

- **User Story:** As a System Administrator, I want the core database tables for users, communities, and members to be defined, so that the application has a foundational structure for storing data.
- **Acceptance Criteria:**
  - Given the project is initialized,
  - When I run `php artisan migrate`,
  - Then the `users`, `communities`, and `members` tables are created in the database.
  - And the `members` table includes fields for name, DOB, status, etc., as defined in the PRD.
  - And the `communities` table includes fields for name and location.
  - And a foreign key relationship exists between `members` and `communities`.
- **Technical Implementation:** Creates migration files based on the "Data Architecture" section of `architecture.md`. Uses snake_case plural table names.

### Story 1.3: User Authentication

- **User Story:** As a User, I want to be able to register, log in, log out, and reset my password, so that I can securely access the application.
- **Acceptance Criteria:**
  - Given I am on the login page, I can enter my credentials to log in.
  - When I am logged in, I can access the main dashboard.
  - And I can use a "Logout" button to end my session.
  - Given I have forgotten my password, I can use the "Forgot Password" link to receive a reset email via Mailpit.
- **Technical Implementation:** Utilizes the default routes and controllers provided by Laravel Breeze. No custom logic needed for this story.

### Story 1.4: Role-Based Access Control (RBAC) Foundation

- **User Story:** As a System Administrator, I want to assign roles (Super Admin, General, Director) to users, so that I can control their access levels.
- **Acceptance Criteria:**
  - Given the `users` table exists,
  - Then it is modified to include a `role` column (e.g., string or enum).
  - And a `UserPolicy` is created.
  - When a user has the 'Super Admin' role, they can access a future "Admin" section (gate passes).
  - When a user has the 'Director' role, they are restricted from admin functions.
- **Technical Implementation:** Creates a `role` column in the `users` migration. Defines basic gates or policies in `AuthServiceProvider`.

### Story 1.5: House-Scoped Data Access for Directors

- **User Story:** As a Community Director, I want to see ONLY the members and data related to my assigned house, so that data privacy is maintained.
- **Acceptance Criteria:**
  - Given a user with the 'Director' role is assigned to "House of Bethany",
  - When they view the (future) member list page,
  - Then they ONLY see members whose `community_id` is "House of Bethany".
  - And they cannot view members from any other house.
- **Technical Implementation:** Implements a global scope (`ScopeByHouse`) or uses a trait on the `Member` model to automatically apply `where('community_id', $user->community_id)` to all queries. This is a critical pattern from `architecture.md`.

---

## Epic 2: Member Lifecycle & Profile Management

**Epic Goal:** To provide a complete and centralized system for managing a sister's entire lifecycle, from her personal information to her spiritual journey, ensuring data is accurate, historical, and easily accessible to authorized users.

### Story 2.1: Create and View Member Profiles

- **User Story:** As a Director, I want to create a new member profile and view a list of all members in my community, so that I have a basic roster.
- **Acceptance Criteria:**
  - Given I am a logged-in Director,
  - When I navigate to the "Members" section, I see a list of members from my community only.
  - And I can click a "Create Member" button which opens a form with fields for Name, DOB, Entry Date.
  - When I submit the form with valid data, a new member is created and appears in the list.
  - And the system prevents me from creating a new member if another member with the same Civil Name and Date of Birth already exists.
- **PRD Coverage:** FR1, FR5
- **Technical Implementation:** Creates `MemberController` with `index` and `create`/`store` methods. The `index` method MUST use the `ScopeByHouse` logic from Epic 1. Adds validation for duplicate entries in the `store` method.

### Story 2.2: Edit Member Profile and Status

- **User Story:** As a Director, I want to edit a member's profile information and update their status, so that I can keep records current.
- **Acceptance Criteria:**
  - Given I am viewing the member list,
  - When I click on a member, I am taken to their profile page.
  - And I can click an "Edit" button to change their information.
  - And I can update their status (Active, Deceased, Exited) via a dropdown, which saves immediately.
- **PRD Coverage:** FR1, FR3
- **Technical Implementation:** Adds `show` and `edit`/`update` methods to `MemberController`. Implements soft deletes on the `Member` model.

### Story 2.3: Member Search

- **User Story:** As a General Secretary, I want to search for any member in the congregation by name, so that I can find their records quickly.
- **Acceptance Criteria:**
  - Given I am a logged-in General Secretary,
  - When I use the global search bar and type a name,
  - Then a list of matching members is displayed.
  - And clicking a result takes me to that member's profile.
- **PRD Coverage:** FR4
- **Technical Implementation:** Creates a search endpoint that queries the `members` table. The query should not be scoped for the 'General' or 'Super Admin' roles.

### Story 2.4: Visual Formation Timeline

- **User Story:** As a Formation Directress, I want to see a visual timeline of a sister's formation stages, so that I can easily track her progress.
- **Acceptance Criteria:**
  - Given I am on a member's profile page,
  - Then I see a timeline displaying her formation events (Postulancy, Novitiate, etc.) with dates.
  - And I can add a new formation event (e.g., "First Vows") with a date.
  - And the system automatically calculates and displays the expected date for the next stage based on Canon Law rules.
- **PRD Coverage:** FR6, FR7
- **UX Integration:** Implements the `<x-feast-timeline>` custom component.
- **Technical Implementation:** Creates `FormationController` and `FormationService`. The service contains the business logic for date calculations.

### Story 2.5: Secure Document Upload for Formation

- **User Story:** As a Director, I want to upload sensitive documents like a Baptismal Certificate to a specific formation stage, so that all required paperwork is stored securely in one place.
- **Acceptance Criteria:**
  - Given I am viewing a formation event on the timeline,
  - When I click "Upload Document", I can select a file.
  - Then the file is uploaded and associated with that specific event.
  - And the file is NOT accessible via a public URL.
- **PRD Coverage:** FR8
- **Technical Implementation:** Uses Laravel's `Storage` facade to save files to the `private` disk (as per `architecture.md`). Creates a new route/controller method to serve files that checks the user's policy first.

### Story 2.6: Member Transfer History

- **User Story:** As an Admin, I want to transfer a member from one house to another and see a complete history of their assignments, so that we have a full record of their service.
- **Acceptance Criteria:**
  - Given I am on a member's profile,
  - When I click "Transfer Member", I can select a new community and date of transfer.
  - Then the member's current `community_id` is updated.
  - And a new entry is created in the `assignments` table to log the historical record.
  - And the member's profile page displays a "Service History" section listing all past and present assignments.
- **PRD Coverage:** FR16, FR17
- **Technical Implementation:** Implements the `assignments` table and model. The transfer action is a DB transaction that updates the member and creates an assignment record.

### Story 2.7: Member Profile Photo Management

- **User Story:** As a Director, I want to upload and crop a profile photo for a member, so that their profile feels personal and complete.
- **Acceptance Criteria:**
  - Given I am on the member's edit page,
  - When I upload an image,
  - Then the system presents a cropping tool to create a square headshot.
  - And the final image is displayed on the member's profile.
- **PRD Coverage:** FR2
- **Technical Implementation:** Uses a client-side library for cropping and the `FileStorageService` for secure storage.

---

## Epic 3: Community Financial Stewardship

**Epic Goal:** To deliver the "Stress-Free Monthly Close" by providing Community Directors with an incredibly simple and forgiving system for tracking daily expenses and generating their monthly financial report.

### Story 3.1: Daily Expense Entry

- **User Story:** As a Community Director, I want to quickly enter a daily expense with a category and amount, so that I don't have to save receipts in a shoebox.
- **Acceptance Criteria:**
  - Given I am on my community's finance page,
  - When I fill out the "Add Expense" form (Category, Amount, Date, Description),
  - Then the new expense appears in a list for the current month.
  - And the list is presented as a simple, clear "Digital Ledger".
- **PRD Coverage:** FR10
- **UX Integration:** Implements `<x-ledger-row>` for displaying expenses. The form is optimized for mobile and forgiving.

### Story 3.2: Receipt Upload

- **User Story:** As a Community Director, I want to upload a photo of a receipt for large expenses, so that I have a digital record for the General Treasurer.
- **Acceptance Criteria:**
  - Given I am entering an expense,
  - When the amount is over a configurable threshold (e.g., $100),
  - Then an "Upload Receipt" button appears.
  - And I can attach an image which is linked to that expense record.
- **PRD Coverage:** FR11
- **Technical Implementation:** Uses the `FileStorageService` to save receipts to the private disk.

### Story 3.3: One-Click Monthly PDF Report

- **User Story:** As a Community Director, I want to generate my complete monthly financial report as a PDF with a single click, so that my accounting duties are finished in minutes, not hours.
- **Acceptance Criteria:**
  - Given it is the end of the month,
  - When I click the "Generate Monthly Report" button on the finance dashboard,
  - Then a PDF is generated that neatly summarizes all expenses for the month, grouped by category.
  - And the report is automatically sent to the General Treasurer (or made available for them to view).
- **PRD Coverage:** FR12
- **Technical Implementation:** Implements the `PdfService`. The controller gathers all expenses for the month (scoped to the house), passes them to a Blade view styled for the PDF, and the service renders it.

### Story 3.4: Locking Financial Records

- **User Story:** As a General Treasurer, I want financial records for a month to be locked after a report is submitted, so that the data remains consistent for annual auditing.
- **Acceptance Criteria:**
  - Given a Director has submitted their June report,
  - When they view the expense list for June,
  - Then they can no longer add, edit, or delete expenses for that month.
  - And a "Submitted" badge is clearly visible.
- **PRD Coverage:** FR14
- **Technical Implementation:** Adds a `status` column to a `financial_reports` table. The `FinancialPolicy` prevents modification if the report's status is `submitted`.

### Story 3.5: Community & House Creation

- **User Story:** As a Super Admin, I want to create new communities (houses), so that the system can be rolled out to new locations.
- **Acceptance Criteria:**
  - Given I am a Super Admin,
  - When I go to the "Admin" section, I can access a "Communities" management page.
  - And I can create a new community by providing a name.
  - And I can assign an existing user to be the Community Director for that new house.
- **PRD Coverage:** FR15

---

## Epic 4: Strategic Oversight & Reporting

**Epic Goal:** To empower the Generalate with high-level dashboards, reporting, and alerts for effective and strategic governance of the entire congregation.

### Story 4.1: Generalate Dashboard View

- **User Story:** As a General Secretary, I want a main dashboard that shows a high-level overview of the congregation's health, including member counts and critical alerts, so I can spot trends and issues quickly.
- **Acceptance Criteria:**
  - Given I am a logged-in General Secretary,
  - When I visit the dashboard, I see summary cards for "Total Members", "Communities", and "Members in Formation".
  - And I see a "Critical Alerts" widget.
- **UX Integration:** Implements `<x-status-card>` to display key metrics. Follows the "Pastoral Dashboard" design direction.

### Story 4.2: Critical Vow Expiry Alerts

- **User Story:** As a General Secretary, I want to be alerted on my dashboard when a sister's temporary vows are expiring soon, so that we never miss a critical formation milestone.
- **Acceptance Criteria:**
  - Given a member's vows are set to expire within 30 days,
  - When I view the Generalate Dashboard,
  - Then the "Critical Alerts" widget shows a notification with the member's name and vow expiration date.
  - And clicking the alert takes me directly to that member's profile.
- **PRD Coverage:** FR9
- **Technical Implementation:** Creates a scheduled command (`php artisan app:check-expiring-vows`) that runs daily to check for upcoming dates and creates a notification record. The dashboard queries this table.

### Story 4.3: View All Financial Reports

- **User Story:** As a General Treasurer, I want to be able to view the submitted monthly financial reports from all communities in one place, so that I can conduct audits and oversee the congregation's finances.
- **Acceptance Criteria:**
  - Given I am a logged-in General Treasurer,
  - When I navigate to the "Congregation Finances" section, I see a list of all communities.
  - And for each community, I can see the status of their latest monthly report (Submitted, Pending).
  - And I can click to view the read-only PDF report for any submitted month.
- **PRD Coverage:** FR13
- **Technical Implementation:** The controller for this view is NOT scoped by house. It queries all reports and groups them by community.

### Story 4.4: Audit Trail Log

- **User Story:** As a Super Admin, I want to view a log of all critical system actions, so that I can ensure accountability and trace any data inconsistencies.
- **Acceptance Criteria:**
  - Given a Director transfers a member,
  - When I, as a Super Admin, view the Audit Trail,
  - Then I see a log entry: "User [Director's Name] transferred Member [Member's Name] from [Old House] to [New House]".
  - And the log captures who, what, and when for all Create, Delete, and Transfer actions.
- **PRD Coverage:** FR20
- **Technical Implementation:** Create an `AuditObserver` that listens for Eloquent model events (`created`, `updated`, `deleted`) on key models and writes to an `audit_events` table.

---

## Summary

**✅ EPIC AND STORY CREATION COMPLETE**

**Full Context Incorporated:** PRD, Architecture, and UX Design documents.
**FR Coverage:** 21/21 functional requirements mapped to 21 stories across 4 epics.

The `epics.md` file is now a complete and actionable plan for development. This workflow is complete.
