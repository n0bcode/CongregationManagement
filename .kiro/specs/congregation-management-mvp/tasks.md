# Implementation Plan

## Phase 1: System Audit and Foundation

- [ ] 1. System Audit and Code Quality
- [x] 1.1 Run Laravel Pint and fix code style issues

  - Execute `./vendor/bin/pint` and resolve all violations
  - Ensure PSR-12 compliance
  - _Requirements: 1.1_

- [x] 1.2 Audit database migrations for foreign key constraints

  - Review all migration files
  - Add missing ON DELETE and ON UPDATE cascades
  - Ensure proper indexing
  - _Requirements: 1.2_

- [x] 1.3 Audit model relationships and add return types

  - Review all models in app/Models
  - Add proper return type declarations (BelongsTo, HasMany, etc.)
  - _Requirements: 1.3_

- [x]\* 1.4 Write property test for model relationships

  - **Property 1: Model Relationships Have Correct Return Types**
  - **Validates: Requirements 1.3**

- [x] 1.5 Audit routes for middleware and authorization

  - Review routes/web.php
  - Ensure all protected routes have auth middleware
  - Add authorization checks where needed
  - _Requirements: 1.4_

- [x]\* 1.6 Write property test for route authorization

  - **Property 2: Routes Have Authorization Middleware**
  - **Validates: Requirements 1.4**

- [x] 1.7 Test Docker configuration
  - Run `docker compose up -d`
  - Verify all services start correctly
  - Test database connectivity
  - _Requirements: 1.5_

## Phase 2: UI/UX Design System Implementation

- [ ] 2. Create Blade Component Library
- [x] 2.1 Implement Tailwind config with Sanctuary & Stone palette

  - Update tailwind.config.js with color system
  - Add custom font families (Merriweather, Inter)
  - Configure responsive breakpoints
  - _Requirements: 8.1_

- [x] 2.2 Create base button component

  - Create resources/views/components/button.blade.php
  - Support variants: primary, secondary, danger
  - Support sizes: sm, md, lg
  - Ensure 48px minimum touch target
  - _Requirements: 8.1, 8.3_

- [x] 2.3 Create status-card component

  - Create resources/views/components/status-card.blade.php
  - Support variants: peace, attention, pending
  - Use Sanctuary & Stone colors
  - _Requirements: 8.3_

- [x] 2.4 Create ledger-row component

  - Create resources/views/components/ledger-row.blade.php
  - Implement date badge, description, amount layout
  - Add hover effects
  - _Requirements: 8.3_

- [x] 2.5 Create timeline-node component

  - Create resources/views/components/timeline-node.blade.php
  - Support past, today, future states
  - Use sanctuary-gold for current events
  - _Requirements: 8.3_

- [x]\* 2.6 Write property test for component reuse

  - **Property 22: Blade Components Are Reused**
  - **Validates: Requirements 8.3**

- [x] 2.7 Update app layout with accessibility features
  - Add focus ring styles
  - Ensure 18px base font size
  - Add prefers-reduced-motion support
  - _Requirements: 8.1, 8.5, 15.5_

## Phase 3: Member Management Enhancement

- [ ] 3. Enhance Member Profile System
- [x] 3.1 Create health_records migration and model

  - Create migration for health_records table
  - Create HealthRecord model with relationships
  - Add fillable fields and casts
  - _Requirements: 2.2_

- [x] 3.2 Create skills migration and model

  - Create migration for skills table
  - Create Skill model with category enum
  - Add proficiency levels
  - _Requirements: 2.3_

- [x]\* 3.3 Write property test for health data persistence

  - **Property 3: Health Data Saves Correctly**
  - **Validates: Requirements 2.2**

- [ ]\* 3.4 Write property test for skill categorization

  - **Property 4: Skills Are Categorized Correctly**
  - **Validates: Requirements 2.3**

- [x] 3.5 Enhance member search functionality

  - Update MemberController search method
  - Add search by skills and health status
  - Optimize with database indexes
  - _Requirements: 2.4_

- [ ]\* 3.6 Write property test for member search

  - **Property 5: Member Search Works Across Fields**
  - **Validates: Requirements 2.4**

- [x] 3.7 Create member profile view with tabs

  - Update resources/views/members/show.blade.php
  - Add tabs for: Personal, Formation, Health, Skills, Service History
  - Use Alpine.js for tab switching
  - _Requirements: 2.1_

- [x] 3.8 Create health records management UI

  - Create form for adding health records
  - Support document uploads
  - Display health history
  - _Requirements: 2.2_

- [x] 3.9 Create skills management UI
  - Create form for adding skills
  - Support skill categories and proficiency
  - Display skills grouped by category
  - _Requirements: 2.3_

## Phase 4: Audit Logging System

- [ ] 4. Implement Comprehensive Audit Trail
- [x] 4.1 Create audit_logs migration and model

  - Create migration with all required fields
  - Create AuditLog model (no updates allowed)
  - Add morphTo relationship
  - _Requirements: 9.1_

- [x] 4.2 Create AuditService

  - Create app/Services/AuditService.php
  - Implement log() method
  - Implement generateTamperEvidentReport() method
  - _Requirements: 9.1, 9.5_

- [x] 4.3 Create AuditObserver for Member model

  - Create app/Observers/MemberAuditObserver.php
  - Log created, updated, deleted events
  - Capture old and new values
  - _Requirements: 9.1_

- [ ]\* 4.4 Write property test for CRUD audit logging

  - **Property 23: CRUD Operations Create Audit Logs**
  - **Validates: Requirements 9.1**

- [x] 4.5 Add audit logging for member transfers

  - Update MemberTransferController
  - Log old and new community IDs
  - _Requirements: 9.2_

- [ ]\* 4.6 Write property test for transfer audit logs
  - **Property 24: Transfer Logs Include Both Communities**
  - **Validates: Requirements 9.2**
- [ ]\* 4.7 Write property test for audit immutability

  - **Property 25: Audit Logs Are Immutable**
  - **Validates: Requirements 9.3**

- [x] 4.8 Create audit log viewer UI

  - Create AuditLogController
  - Create resources/views/audit-logs/index.blade.php
  - Support filtering by user, action, date range
  - _Requirements: 9.4_

- [ ]\* 4.9 Write property test for audit log filtering

  - **Property 26: Audit Logs Support Filtering**
  - **Validates: Requirements 9.4**

- [x] 4.10 Implement tamper-evident export

  - Add export functionality to AuditService
  - Generate checksums for audit data
  - Create PDF with verification hash
  - _Requirements: 9.5_

- [ ]\* 4.11 Write property test for tamper-evident exports

  - **Property 27: Audit Exports Are Tamper-Evident**
  - **Validates: Requirements 9.5**

- [ ] 4.12 Implement Permission Audit Logging

  - Log permission assignment and revocation
  - Log role changes
  - Update AuditService to handle permission events
  - _Requirements: 9.4_

## Phase 5: Financial Management Module

- [ ] 5. Build Financial Management System
- [x] 5.1 Create expenses migration (if not exists)

  - Verify expenses table structure
  - Add is_locked and locked_at fields
  - Add indexes for performance
  - _Requirements: 3.1_

- [x] 5.2 Create Expense model with validation

  - Update Expense model
  - Add validation rules
  - Add community relationship
  - _Requirements: 3.1_

- [ ]\* 5.3 Write property test for expense validation

  - **Property 7: Expense Validation Enforces Required Fields**
  - **Validates: Requirements 3.1**

- [ ]\* 5.4 Write property test for positive amounts

  - **Property 46: Expense Amounts Are Positive**
  - **Validates: Requirements 14.4**

- [x] 5.5 Create FinancialController

  - Create app/Http/Controllers/FinancialController.php
  - Implement index, create, store methods
  - Add RBAC authorization
  - _Requirements: 3.1, 3.5_

- [x] 5.6 Create expense entry form

  - Create resources/views/financials/create.blade.php
  - Support receipt upload
  - Use ledger-row component for display
  - _Requirements: 3.1, 3.2_

- [x] 5.7 Create FinancialService

  - Create app/Services/FinancialService.php
  - Implement generateMonthlyReport() method
  - Implement lockPeriod() method
  - Implement aggregateExpensesByCategory() method
  - _Requirements: 3.3, 3.4_

- [ ]\* 5.8 Write property test for report generation performance

  - **Property 8: Monthly Report Generation Performance**
  - **Validates: Requirements 3.3**

- [x] 5.9 Implement period locking

  - Add lockPeriod() functionality
  - Prevent edits to locked expenses
  - _Requirements: 3.4_

- [ ]\* 5.10 Write property test for locked period immutability
  - **Property 9: Locked Periods Are Immutable**
  - **Validates: Requirements 3.4**
- [x] 5.11 Implement General Treasurer read-only access

  - Update FinancialPolicy
  - Allow General role to view all communities
  - Prevent General role from editing
  - _Requirements: 3.5_

- [ ]\* 5.12 Write property test for General Treasurer access

  - **Property 10: General Treasurer Has Read-Only Access**
  - **Validates: Requirements 3.5**

- [x] 5.13 Create PdfService for reports

  - Create app/Services/PdfService.php
  - Install and configure PDF library (dompdf or snappy)
  - Implement generateFinancialReport() method
  - _Requirements: 3.3_

- [x] 5.14 Create monthly report view template
  - Create resources/views/financials/report.blade.php
  - Use Sanctuary & Stone design
  - Include congregation logo
  - _Requirements: 3.3_

## Phase 6: Document Management System

- [ ] 6. Build Document Management
- [x] 6.1 Create documents and folders migrations

  - Create documents table with categories
  - Create folders table with parent_id for hierarchy
  - Add proper indexes
  - _Requirements: 5.1, 5.2_

- [x] 6.2 Create Document and Folder models

  - Create Document model with relationships
  - Create Folder model with self-referencing relationship
  - Add category enum
  - _Requirements: 5.1, 5.2_

- [ ]\* 6.3 Write property test for document categorization

  - **Property 11: Documents Require Valid Categories**
  - **Validates: Requirements 5.1**

- [ ]\* 6.4 Write property test for folder hierarchy

  - **Property 12: Folder Hierarchy Supports Nesting**
  - **Validates: Requirements 5.2**

- [x] 6.5 Create DocumentController

  - Create app/Http/Controllers/DocumentController.php
  - Implement CRUD operations
  - Add search functionality
  - _Requirements: 5.3_

- [ ]\* 6.6 Write property test for document search

  - **Property 13: Document Search Supports Multiple Filters**
  - **Validates: Requirements 5.3**

- [x] 6.7 Implement RBAC for documents

  - Create DocumentPolicy
  - Add community scoping for Directors
  - _Requirements: 5.4_

- [ ]\* 6.8 Write property test for Director document scoping

  - **Property 14: Directors See Only Their Community Documents**
  - **Validates: Requirements 5.4**

- [x] 6.9 Implement temporary signed URLs

  - Add getTemporaryUrl() method to Document model
  - Configure storage for private files
  - _Requirements: 5.5_

- [ ]\* 6.10 Write property test for URL expiration

  - **Property 15: Document URLs Are Temporary**
  - **Validates: Requirements 5.5**

- [x] 6.11 Create document management UI
  - Create resources/views/documents/index.blade.php
  - Add folder navigation
  - Add search interface
  - Support file uploads
  - _Requirements: 5.1, 5.2, 5.3_

## Phase 7: Reminders and Notifications

- [ ] 7. Implement Notification System
- [x] 7.1 Create reminders migration and model

  - Create reminders table
  - Create Reminder model
  - Add type enum (vow_expiration, birthday, health_check, formation_milestone)
  - _Requirements: 6.1, 6.2, 6.3_

- [x] 7.2 Create NotificationService

  - Create app/Services/NotificationService.php
  - Implement scheduleVowReminders() method
  - Implement sendBirthdayNotifications() method
  - Implement notifyFormationDirectress() method
  - _Requirements: 6.1, 6.2, 6.3_

- [ ]\* 7.3 Write property test for vow reminder scheduling

  - **Property 16: Vow Reminders Are Scheduled Correctly**
  - **Validates: Requirements 6.1**

- [ ]\* 7.4 Write property test for formation notifications

  - **Property 17: Formation Notifications Target Correct Role**
  - **Validates: Requirements 6.3**

- [x] 7.5 Create scheduled command for reminders

  - Create app/Console/Commands/SendReminders.php
  - Schedule in app/Console/Kernel.php
  - _Requirements: 6.1, 6.2_

- [x] 7.6 Create system_settings migration and model

  - Create system_settings table
  - Create SystemSetting model
  - Add key-value storage with type casting
  - _Requirements: 6.5, 12.1, 12.2_

- [ ]\* 7.7 Write property test for customizable reminder periods

  - **Property 18: Reminder Periods Are Customizable**
  - **Validates: Requirements 6.5**

- [x] 7.8 Create dashboard notifications widget
  - Update resources/views/dashboard.blade.php
  - Display upcoming events in timeline format
  - Use timeline-node component
  - _Requirements: 6.4_

## Phase 8: Service History and Timeline

- [ ] 8. Enhance Service History System
- [x] 8.1 Verify assignments table structure

  - Check assignments migration
  - Ensure proper date fields and indexes
  - _Requirements: 10.1, 10.2_

- [x] 8.2 Add timeline() method to Member model

  - Aggregate formation events, assignments, transfers
  - Sort chronologically
  - _Requirements: 10.1_

- [ ]\* 8.3 Write property test for timeline chronology

  - **Property 28: Timeline Events Are Chronological**
  - **Validates: Requirements 10.1**

- [x] 8.3 Create ServiceHistoryController

  - Create app/Http/Controllers/ServiceHistoryController.php
  - Implement store method with validation
  - _Requirements: 10.2_

- [ ]\* 8.4 Write property test for service record validation

  - **Property 29: Service Records Require Mandatory Fields**
  - **Validates: Requirements 10.2**

- [x] 8.5 Add automatic timeline entry for transfers

  - Update MemberTransferController
  - Create timeline entry on community change
  - _Requirements: 10.3_

- [ ]\* 8.6 Write property test for automatic timeline entries
  - **Property 30: Community Changes Create Timeline Entries**
  - **Validates: Requirements 10.3**
- [x] 8.7 Add duration calculation to Assignment model

  - Add getDurationAttribute() method
  - Calculate from start_date to end_date or now
  - _Requirements: 10.4_

- [ ]\* 8.8 Write property test for duration calculation

  - **Property 31: Service Duration Is Calculated**
  - **Validates: Requirements 10.4**

- [x] 8.9 Enhance member profile PDF export

  - Update PdfService->generateMemberProfile()
  - Include complete timeline
  - _Requirements: 10.5_

- [ ]\* 8.10 Write property test for timeline in PDF export

  - **Property 32: Member Profile Export Includes Timeline**
  - **Validates: Requirements 10.5**

- [x] 8.11 Create timeline visualization UI
  - Create resources/views/members/timeline.blade.php
  - Use timeline-node component
  - Display all event types
  - _Requirements: 10.1_

## Phase 9: Dashboard and Reporting

- [ ] 9. Build Dashboard and Reports
- [x] 9.1 Create DashboardController

  - Create app/Http/Controllers/DashboardController.php
  - Aggregate member counts by formation stage
  - Get upcoming birthdays
  - Get active projects count
  - _Requirements: 7.1_

- [x] 9.2 Create dashboard view

  - Update resources/views/dashboard.blade.php
  - Use status-card components
  - Display member statistics
  - Show upcoming events
  - _Requirements: 7.1_

- [x] 9.3 Create ReportController

  - Create app/Http/Controllers/ReportController.php
  - Implement demographic reports
  - Support filtering
  - _Requirements: 7.2, 7.4_

- [ ]\* 9.4 Write property test for demographic reports

  - **Property 19: Demographic Reports Include All Sections**
  - **Validates: Requirements 7.2**

- [ ]\* 9.5 Write property test for report filtering

  - **Property 20: Report Filters Work Correctly**
  - **Validates: Requirements 7.4**

- [x] 9.6 Implement report export functionality

  - Add PDF export to ReportController
  - Add Excel export using Laravel Excel
  - _Requirements: 7.3_

- [x] 9.7 Create report templates

  - Create resources/views/reports/demographic.blade.php
  - Create resources/views/reports/financial.blade.php
  - Use Sanctuary & Stone colors for charts
  - _Requirements: 7.2, 7.5_

- [x] 9.8 Implement chart generation
  - Install and configure chart library (Chart.js or similar)
  - Create age distribution chart
  - Create formation stage breakdown chart
  - Use accessible colors
  - _Requirements: 7.2, 7.5_

## Phase 10: Celebration Card Generator

- [ ] 10. Build Celebration System
- [x] 10.1 Create CelebrationController
  - Create app/Http/Controllers/CelebrationController.php
  - Implement card generation
  - Support multiple templates
  - _Requirements: 11.1, 11.2_
- [x] 10.2 Install image manipulation library

  - Install Intervention Image or similar
  - Configure for image generation
  - _Requirements: 11.3_

- [x] 10.3 Create celebration card templates

  - Create birthday template
  - Create vow anniversary template
  - Create ordination template
  - Use Sanctuary & Stone design
  - _Requirements: 11.2_

- [x]\* 10.4 Write property test for card generation performance

  - **Property 33: Celebration Card Generation Performance**
  - **Validates: Requirements 11.3**

- [x] 10.5 Implement card download and email

  - Add download functionality
  - Add email sending functionality
  - _Requirements: 11.4_

- [x] 10.6 Create upcoming celebrations widget

  - Add to dashboard
  - Filter events within 30 days
  - _Requirements: 11.5_

- [x]\* 10.7 Write property test for upcoming celebrations filter
  - **Property 34: Upcoming Celebrations Filter By Date**
  - **Validates: Requirements 11.5**

## Phase 11: System Settings and Configuration

- [ ] 11. Build System Configuration
- [x] 11.1 Create SettingsController

  - Create app/Http/Controllers/SettingsController.php
  - Implement CRUD for system settings
  - Restrict to Super Admin
  - _Requirements: 12.1, 12.2, 12.3, 12.4_

- [x] 11.2 Create settings management UI

  - Create resources/views/settings/index.blade.php
  - Add service year configuration
  - Add reminder period configuration
  - Add logo upload
  - Add email settings
  - _Requirements: 12.1, 12.2, 12.3, 12.4_

- [x]\* 11.3 Write property test for service year default

  - **Property 35: Service Year Defaults New Assignments**
  - **Validates: Requirements 12.1**

- [x]\* 11.4 Write property test for reminder period changes

  - **Property 36: Reminder Period Changes Apply Globally**
  - **Validates: Requirements 12.2**

- [x] 11.5 Implement email settings validation

  - Add SMTP connection test
  - Validate before saving
  - _Requirements: 12.4_

- [x]\* 11.6 Write property test for email validation

  - **Property 37: Email Settings Are Validated**
  - **Validates: Requirements 12.4**

- [x] 11.7 Implement backup system

  - Create backup command
  - Schedule daily backups
  - Store encrypted backups
  - _Requirements: 12.5_

- [x]\* 11.8 Write property test for daily backups
  - **Property 38: Backups Run Daily**
  - **Validates: Requirements 12.5**

## Phase 12: Performance Optimization

- [ ] 12. Optimize System Performance
- [x] 12.1 Add database indexes
  - Review query patterns
  - Add indexes to frequently queried columns
  - _Requirements: 13.5_
- [x]\* 12.2 Write property test for dashboard performance

  - **Property 39: Dashboard Loads Under 2 Seconds**
  - **Validates: Requirements 13.1**

- [x]\* 12.3 Write property test for search performance

  - **Property 40: Member Search Performance**
  - **Validates: Requirements 13.2**

- [x] 12.4 Implement photo optimization

  - Add automatic WebP conversion
  - Set quality to 85%
  - Resize to appropriate dimensions
  - _Requirements: 13.3_

- [x]\* 12.5 Write property test for photo optimization

  - **Property 41: Photos Are Optimized Automatically**
  - **Validates: Requirements 13.3**

- [x] 12.6 Optimize RBAC permission caching

  - Verify cache implementation in User model
  - Add cache invalidation on role changes
  - _Requirements: 13.4_

- [x]\* 12.7 Write property test for permission caching

  - **Property 42: RBAC Uses Cached Permissions**
  - **Validates: Requirements 13.4**

- [x] 12.8 Optimize asset loading
  - Configure Vite for code splitting
  - Add lazy loading for images
  - Minify CSS and JS
  - _Requirements: 13.1_

## Phase 13: Data Validation and Integrity

- [ ] 13. Implement Comprehensive Validation
- [x] 13.1 Add date of birth validation

  - Update StoreMemberRequest
  - Reject future dates
  - _Requirements: 14.1_

- [x]\* 13.2 Write property test for DOB validation

  - **Property 43: Date of Birth Cannot Be Future**
  - **Validates: Requirements 14.1**

- [x] 13.3 Add formation date chronology validation

  - Create custom validation rule
  - Enforce entry < first vows < perpetual vows
  - _Requirements: 14.2_

- [x]\* 13.4 Write property test for formation date order

  - **Property 44: Formation Dates Are Chronological**
  - **Validates: Requirements 14.2**

- [x] 13.5 Add assignment overlap validation

  - Create custom validation rule
  - Check for date range conflicts
  - _Requirements: 14.3_

- [x]\* 13.6 Write property test for assignment overlap

  - **Property 45: Assignments Cannot Overlap**
  - **Validates: Requirements 14.3**

- [x] 13.7 Add file upload validation

  - Validate file types (PDF, JPG, PNG for documents; JPG, PNG for photos)
  - Enforce size limits (10MB documents, 5MB photos)
  - _Requirements: 14.5_

- [x]\* 13.8 Write property test for file validation
  - **Property 47: File Uploads Are Validated**
  - **Validates: Requirements 14.5**

## Phase 14: Accessibility Compliance

- [x] 14. Ensure WCAG 2.1 Level AA Compliance
- [x] 14.1 Add ARIA attributes to forms
  - Review all form inputs
  - Add proper labels and aria-labels
  - Add aria-describedby for error messages
  - _Requirements: 15.1_
- [x] 14.2 Add alt text to all images
  - Review all image tags
  - Add descriptive alt attributes
  - _Requirements: 15.2_
- [x]\* 14.3 Write property test for image alt text

  - **Property 48: Images Have Alt Text**
  - **Validates: Requirements 15.2**

- [x] 14.4 Test keyboard navigation (Skipped as per user request)

  - Verify all interactive elements are reachable
  - Test tab order
  - _Requirements: 15.3_

- [x] 14.5 Verify color contrast ratios

  - Test all text/background combinations
  - Ensure 4.5:1 minimum for normal text
  - Ensure 3:1 minimum for large text (18px+)
  - _Requirements: 15.4_

- [x] 14.6 Add prefers-reduced-motion support
  - Update CSS with media query
  - Disable animations when requested
  - _Requirements: 15.5_

## Phase 15: Error Handling and User Experience

- [x] 15. Enhance Error Handling
- [x] 15.1 Create custom error messages

  - Update all FormRequests
  - Replace technical messages with user-friendly text
  - _Requirements: 8.2_

- [x]\* 15.2 Write property test for error message kindness

  - **Property 21: Error Messages Are User-Friendly**
  - **Validates: Requirements 8.2**

- [x] 15.3 Create error handling middleware

  - Catch common exceptions
  - Return appropriate responses
  - Log errors for debugging
  - _Requirements: Error Handling section_

- [x] 15.4 Create 403 and 404 error pages

  - Design user-friendly error pages
  - Use Sanctuary & Stone design
  - Provide helpful navigation
  - _Requirements: Error Handling section_

- [x] 15.5 Add flash message component
  - Create success/error toast notifications
  - Use Alpine.js for animations
  - Auto-dismiss after 3 seconds
  - _Requirements: 8.2_

## Phase 16: Navigation System Implementation

- [x] 16. Build Grouped Navigation
- [x] 16.1 Create NavDropdown component

  - Create resources/views/components/nav-dropdown.blade.php
  - Implement active state detection
  - Implement click-outside behavior
  - _Requirements: 16.1, 16.2_

- [x] 16.2 Create DropdownLink component

  - Create resources/views/components/dropdown-link.blade.php
  - Add styling for active/inactive states
  - _Requirements: 16.3_

- [x] 16.3 Implement Desktop Navigation

  - Update navigation.blade.php
  - Group Management, Finance, Reports, System items
  - Apply RBAC visibility checks
  - _Requirements: 16.1, 16.4_

- [x] 16.4 Implement Mobile Navigation

  - Create responsive accordion menu
  - Use Alpine.js for expand/collapse
  - _Requirements: 16.5_

- [x]\* 16.5 Write property test for navigation visibility

  - **Property 51: Navigation Dropdown Visibility**
  - **Validates: Requirements 16.1**

- [x]\* 16.6 Write property test for active state

  - **Property 52: Navigation Active State**
  - **Validates: Requirements 16.4**

  - Design user-friendly error pages
  - Use Sanctuary & Stone design
  - Provide helpful navigation
  - _Requirements: Error Handling section_

- [ ] 15.5 Add flash message component
  - Create success/error toast notifications
  - Use Alpine.js for animations
  - Auto-dismiss after 3 seconds
  - _Requirements: 8.2_

## Phase 16: Testing and Quality Assurance

- [ ] 16. Comprehensive Testing
- [ ]\* 16.1 Run all property-based tests

  - Execute all 48 property tests
  - Ensure 100 iterations each
  - Fix any failures
  - _Requirements: All_

- [ ]\* 16.2 Write unit tests for services

  - Test FormationService methods
  - Test FinancialService methods
  - Test NotificationService methods
  - Test AuditService methods
  - Test PdfService methods
  - _Requirements: All_

- [ ]\* 16.3 Write feature tests for controllers

  - Test all CRUD operations
  - Test authorization
  - Test file uploads
  - _Requirements: All_

- [ ]\* 16.4 Run browser tests with Dusk

  - Test critical user journeys
  - Test responsive design
  - Test keyboard navigation
  - _Requirements: 8.4, 15.3_

- [ ]\* 16.5 Run accessibility audit

  - Use axe-core or pa11y
  - Fix all violations
  - _Requirements: 15.1-15.5_

- [ ]\* 16.6 Run performance tests

  - Measure page load times
  - Measure query execution times
  - Optimize slow queries
  - _Requirements: 13.1, 13.2_

- [ ] 16.7 Run static analysis

  - Execute PHPStan at level 8
  - Fix all issues
  - _Requirements: 1.1_

- [ ] 16.8 Check test coverage
  - Generate coverage report
  - Ensure 80% minimum coverage
  - _Requirements: Testing Strategy_

## Phase 17: Final Checkpoint

- [ ] 17. Final System Verification
- [ ] 17.1 Ensure all tests pass

  - Run full test suite
  - Verify no failures
  - Ask user if questions arise

- [ ] 17.2 Verify Docker deployment

  - Test complete docker-compose setup
  - Verify all services are healthy
  - Test database migrations
  - _Requirements: 1.5_

- [ ] 17.3 Create deployment documentation

  - Document environment variables
  - Document deployment steps
  - Document backup procedures
  - _Requirements: System Management_

- [ ] 17.4 Create user documentation

  - Document key features
  - Create user guides for each role
  - Document common workflows
  - _Requirements: All_

- [ ] 17.5 Final code review
  - Review all code for best practices
  - Ensure Laravel conventions are followed
  - Verify UI/UX design system compliance
  - _Requirements: All_
