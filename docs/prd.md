---
stepsCompleted: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
inputDocuments:
  [
    "/media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/docs/analysis/product-brief-Managing_the_Congregation-2025-12-01.md",
    "/media/truc2tz/SantaSSD/SKS/Sources/repos/aistudio/System_Blood_Group/plans/origin_require.txt",
  ]
workflowType: "prd"
lastStep: 0
project_name: "Managing the Congregation (at the organizational level)"
user_name: "Wavister"
date: "2025-12-01"
---

# Product Requirements Document - Managing the Congregation (at the organizational level)

**Author:** Wavister
**Date:** 2025-12-01

## Executive Summary

The **Managing the Congregation** system is a specialized Member Management Solution designed to modernize the administration of religious orders. By centralizing member data—from personal history and formation stages to service records and health profiles—the system eliminates data fragmentation and reduces administrative overhead. Built on a robust **PHP (Laravel) + MySQL** stack, it ensures long-term data integrity, standardized reporting, and operational efficiency for congregations operating in diverse international contexts.

### What Makes This Special

Unlike generic HR or CRM tools, this system is **purpose-built for religious life**, natively handling unique workflows such as:

- **Formation Lifecycle Tracking:** Seamlessly managing the progression from Postulancy to Perpetual Vows.
- **Community-Centric Logic:** Handling annual assignments, transfers, and community-level financial reporting.
- **Long-Term Stewardship:** Prioritizing the preservation of historical records and "lifelong" member data.
- **Global Accessibility:** Designed for ease of use in regions with varying technical infrastructure, such as Africa.

## Project Classification

**Technical Type:** `web_app`
**Domain:** `general` (Religious Order Management)
**Complexity:** `medium`

The project involves building a centralized web application with role-based access control, complex data relationships (lifelong member history), and essential financial and document management modules. While the MVP focuses on core CRUD and reporting, the long-term vision includes predictive insights and broader interoperability.

## Success Criteria

### User Success

- **Community Directors:** Reduce monthly financial reporting time from 4 hours to **< 30 minutes**.
- **General Secretary:** Achieve **100% data accuracy** (zero discrepancies found during annual audit) for General Chapter statistics.
- **Formation Directress:** **Zero missed critical dates** (vows, health checks) for candidates in the first year of operation.
- **Adoption:** **90% of communities** submitting monthly reports via the system within 3 months of rollout.

### Business Success

- **Operational Efficiency:** Reduce administrative overhead costs at the Generalate by **40% within 12 months**.
- **Standardization:** **100% of regions** (including Africa) using the unified reporting format by Month 12.
- **Data Integrity:** Establish a "Single Source of Truth" by Q4, eliminating reliance on disparate spreadsheets.

### Technical Success

- **Scalability:** System handles **500+ member records** with page load times under 2 seconds.
- **Reliability:** **Zero data loss** during the pilot phase; 99.9% uptime.
- **Security:** Role-Based Access Control (RBAC) successfully restricts sensitive data (e.g., health records) to authorized roles only.

### Measurable Outcomes

- **Active Usage:** >80% of Community Directors logging in at least weekly.
- **Report Submission:** >95% of monthly reports submitted by the 5th of the following month.

## Product Scope

### MVP - Minimum Viable Product

- **Member Management:** Full CRUD for profiles, including photos and mandatory fields.
- **Formation Tracking:** Lifecycle management (Postulancy to Perpetual Vows) with date tracking.
- **Community Management:** House creation, member assignment (Service History), and basic asset tracking.
- **Financial Reporting:** Simple expense entry and monthly PDF report generation.
- **Dashboard:** Basic admin view (Member counts, upcoming birthdays/feast days).
- **Access Control:** Super Admin, General Secretary, Community Director roles.

### Growth Features (Post-MVP)

- **Project Management:** Detailed tracking of congregation projects and grant management.
- **Advanced Reporting:** Custom report builder and demographic analytics.
- **Document Management:** Full folder structure and permissioned file sharing.
- **Celebration Cards:** Automated greeting card generation.

### Vision (Future)

- **Mobile App:** Dedicated app for individual members.
- **AI Insights:** Predictive analytics for aging demographics and financial forecasting.
- **Global Interop:** API integration with Vatican or other provincial databases.

## User Journeys

### Journey 1: Sr. Mary - The End-of-Month Relief

**Persona:** Community Director, 60s, not tech-savvy, cares deeply for her sisters but dreads paperwork.
**Goal:** Submit the monthly financial report without stress.

It's the 30th of the month. In the past, Sr. Mary would be surrounded by piles of receipts, a calculator, and a ledger, feeling anxious about balancing the books before evening prayer. Today, she logs into "Managing the Congregation" on the community laptop. Throughout the month, she had spent 2 minutes each evening entering daily expenses as they happened.

She clicks "Financials" and sees her community's dashboard. She notices a "Review" alert for a large medical expense from last week. She clicks it, attaches the scanned receipt she saved, and approves it. Then, she clicks "Generate Monthly Report". The system displays a clean, balanced PDF. She smiles, clicks "Submit to General Treasurer," and is done in 15 minutes. She has time to visit the sick sisters before prayer, feeling light and organized.

### Journey 2: Sr. Anne - The Strategic Insight

**Persona:** General Secretary, organized, strategic, needs accurate data for the General Chapter.
**Goal:** Analyze the congregation's demographics for future planning.

Sr. Anne is preparing for the General Chapter meeting. The Mother General asks, "What is our projected aging index for the next 5 years?" Previously, this would have meant a week of digging through paper files and calling 50 communities.

Sr. Anne opens her dashboard and navigates to "Reports > Demographics". She selects "Aging Forecast" and filters by "Region". Instantly, a chart appears showing a steep rise in members over 70 in the European province, while the African province shows a youth boom. She exports the data to a presentation-ready format. She walks into the meeting with confidence, armed with real-time data to support a proposal for new mission openings in Africa.

### Journey 3: Sr. Teresa - Guiding the Future

**Persona:** Formation Directress, empathetic, focused on spiritual growth, worries about missing details.
**Goal:** Ensure all candidates are on track for their vows without administrative oversight.

Sr. Teresa is reviewing the file of a Novice, Maria, who is due for First Vows soon. In the old system, she might have scrambled to find Maria's baptismal certificate or health clearance.

She opens Maria's profile in the "Formation" module. The timeline view clearly shows Maria's journey: Postulancy start date, Novitiate entry, and a yellow flag: "Health Check Overdue". Sr. Teresa realizes she forgot to schedule Maria's physical. She immediately books the appointment. The system also reminds her that Maria's vow application needs to be submitted to the Council by next Friday. Sr. Teresa feels supported, knowing the system is watching out for the details so she can focus on Maria's spiritual preparation.

### Journey 4: Admin - The Guardian of Access

**Persona:** IT Support / Super Admin (Lay Staff or Tech-Savvy Sister).
**Goal:** Securely onboard a new Community Director.

A new local superior, Sr. Joan, has been appointed to the House of Bethany. The Admin receives the request to set up her access. He logs in and navigates to "User Management". He finds Sr. Joan's existing member profile and promotes her role to "Community Director" for the "House of Bethany" context.

He sets her permissions: she can now see the finances for Bethany House but not others. He also checks the "Audit Log" to verify that the previous Director's access was automatically revoked upon her transfer. He sends a password reset link to Sr. Joan's email. The entire handover of digital authority takes 2 minutes and is fully auditable.

### Journey Requirements Summary

These journeys reveal the need for:

- **Financials:** Simple daily expense entry, receipt attachment, one-click monthly report generation.
- **Reporting:** Real-time demographic aggregation, filtering by region/age, export capabilities.
- **Formation:** Visual timeline of member history, automated alerts for critical dates/missing requirements.
- **Admin/Security:** Role-based promotion (Member -> Director), context-specific access (House level), audit logging.

## Innovation & Novel Patterns

### Detected Innovation Areas

- **Scientific Management in Religious Life:** Applying rigorous data structure (lifecycle tracking, formation stages) to a domain traditionally managed by oral tradition or paper records. This "digitization of the vow" is a novel application of CRM logic.
- **Context-Aware Design:** The system is explicitly designed for "International Readiness," implying a focus on usability in regions with varying technical literacy and infrastructure (e.g., Africa), which differentiates it from Western-centric church management software.

### Market Context & Competitive Landscape

- **Current State:** Most religious orders use generic tools (Excel, Word) or ill-fitting parish software.
- **Gap:** There is a lack of specialized tools that handle the specific legal and spiritual lifecycle of a consecrated person (Postulancy -> Novitiate -> Vows).
- **Opportunity:** To become the standard operating system for religious orders globally, starting with underserved regions.

### Validation Approach

- **Pilot Program:** Deploy in 3 diverse communities (one in Africa, one in Europe/US) to validate the "International Readiness" and usability assumptions.
- **Audit Comparison:** Run the system parallel to the manual process for one month to prove the "Time Savings" and "Accuracy" metrics.

### Risk Mitigation

- **Adoption Risk:** Non-tech-savvy users (e.g., elderly sisters) may resist. _Mitigation:_ Extremely simple UI for end-users, focusing complexity only on the Admin/Secretary roles.
- **Cultural Resistance:** Perceived "bureaucratization" of spiritual life. _Mitigation:_ Emphasize the "Peace of Mind" and "Care" aspects—the system helps take care of the sisters better.

## Web App Specific Requirements

### Project-Type Overview

The system will be a **Multi-Page Application (MPA)** utilizing Laravel's server-side rendering (Blade templates) for robustness and SEO friendliness, potentially enhanced with Livewire or Vue.js for dynamic interactivity where needed. This architecture suits the "Scientific Management" approach by prioritizing stability and standard HTTP lifecycles over complex client-side state management.

### Technical Architecture Considerations

- **Backend:** PHP 8.x (Laravel Framework)
- **Database:** MySQL 8.0
- **Frontend:** Blade Templates + Tailwind CSS (for "Premium Design") + Alpine.js/Livewire (for interactivity).
- **Infrastructure:** Dockerized for easy deployment (as per user preference).

### Browser Matrix & Support

- **Primary Targets:** Chrome (latest), Firefox (latest), Edge (latest), Safari (latest).
- **Legacy Support:** IE11 is explicitly **excluded**.
- **Mobile Web:** Must be fully responsive and functional on mobile browsers (iOS Safari, Android Chrome) to support the "International Readiness" goal.

### Responsive Design Strategy

- **Breakpoints:** Mobile-first approach (Small -> Medium -> Large -> Extra Large).
- **Navigation:** Collapsible sidebar or hamburger menu on mobile; full sidebar on desktop.
- **Data Tables:** Must support horizontal scrolling or card-view transformation on small screens (critical for financial reports).

### Performance Targets

- **Page Load:** < 2 seconds for main dashboard on 4G networks.
- **Time to Interactive:** < 1.5 seconds.
- **Optimization:** Image optimization (WebP) for member photos; asset minification.

### Accessibility (A11y)

- **Standard:** WCAG 2.1 Level AA compliance.
- **Focus:** High contrast text (for elderly users/Sr. Mary), keyboard navigation support, and screen reader compatibility for forms.

### SEO Strategy

- **Internal:** Not applicable for public SEO (authenticated app).
- **Technical:** Proper semantic HTML (H1-H6 structure) to assist screen readers and future-proof the codebase.

## Project Scoping & Phased Development

### MVP Strategy & Philosophy

**MVP Approach:** **Experience MVP**. The goal is not just to "store data" (which Excel does) but to provide a superior _experience_ of care and organization. We will launch with a polished, complete feel for the core modules rather than a buggy "kitchen sink."
**Resource Requirements:** 1 Full-Stack Developer (Laravel), 1 Part-time Designer/PM.

### MVP Feature Set (Phase 1)

**Core User Journeys Supported:**

- Sr. Mary's Monthly Financial Report
- Sr. Anne's Demographic Analysis
- Sr. Teresa's Formation Tracking
- Admin's User Onboarding

**Must-Have Capabilities:**

- **Member Database:** Create/Read/Update/Delete members with photos and basic sacramental data.
- **Formation Timeline:** Visual tracking of dates (Postulancy -> Vows) with simple color-coded alerts.
- **Financials:** Daily expense entry + One-click PDF generation for the monthly report.
- **RBAC:** Strict separation of duties (Director vs. General vs. Admin).
- **Search:** Instant search for members by name or community.

### Post-MVP Features

**Phase 2 (Growth - Month 4-6):**

- **Project Management:** Tracking grant applications and construction projects.
- **Asset Management:** Inventory of community property (cars, computers).
- **Advanced Export:** CSV/Excel dumps for custom analysis.

**Phase 3 (Expansion - Month 7+):**

- **Mobile App:** Native app for offline access in remote areas.
- **Celebration Cards:** Automated email/PDF greetings for birthdays/feast days.
- **Inter-Congregation API:** Standard for sharing data with the Vatican (if required).

### Risk Mitigation Strategy

**Technical Risks:**

- _Risk:_ Poor internet in some communities.
- _Mitigation:_ Ensure the web app is "Offline Tolerant" (PWA capabilities) or at least fails gracefully.

**Market Risks:**

- _Risk:_ Communities refusing to adopt the system.
- _Mitigation:_ The "Generalate Mandate"—adoption is required by the Mother General, but facilitated by excellent training and support.

**Resource Risks:**

- _Risk:_ Developer burnout or departure.
- _Mitigation:_ Strict adherence to Laravel standards (no "clever" custom code) so any Laravel dev can pick it up.

## Functional Requirements

### Member Management

- **FR1:** Admin/Director can create a new member profile with mandatory fields (Name, DOB, Entry Date).
- **FR2:** Admin/Director can upload and crop a profile photo for a member.
- **FR3:** Admin/Director can update member status (Active, Deceased, Exited, Transferred).
- **FR4:** Admin/Director can search for members by name, religious name, or civil name.
- **FR5:** System must prevent duplicate member entries based on Civil Name + DOB.

### Formation Tracking

- **FR6:** Admin/Director can view a visual timeline of a member's formation stages (Postulancy, Novitiate, Vows).
- **FR7:** System must automatically calculate eligibility dates for the next stage based on Canon Law rules (e.g., 1 year for Novitiate).
- **FR8:** Admin/Director can upload documents (Baptismal Cert, Health Report) to a specific formation stage.
- **FR9:** System must display a "Critical Alert" on the dashboard for vows expiring within 30 days.

### Financial Management

- **FR10:** Community Director can enter a daily expense with Category, Amount, Date, and Description.
- **FR11:** Community Director can upload a receipt image/PDF for any expense > threshold (configurable).
- **FR12:** Community Director can generate a "Monthly Financial Report" PDF that aggregates expenses by category.
- **FR13:** General Treasurer can view read-only financial reports from all communities.
- **FR14:** System must lock financial records after the monthly report is submitted.

### Community & Housing

- **FR15:** Super Admin can create new Houses/Communities and assign a Community Director.
- **FR16:** Admin can transfer a member from one House to another (updating their current location).
- **FR17:** System must maintain a history log of all member transfers (Service History).

### Security & Access Control

- **FR18:** Super Admin can create user accounts and assign roles (General, Director, Member).
- **FR19:** System must restrict Community Directors to view/edit ONLY members and finances of their assigned House.
- **FR20:** System must log all critical actions (Create, Delete, Transfer) in an audit trail visible to Super Admin.
- **FR21:** Users can reset their own passwords via email link.

## Non-Functional Requirements

### Security & Privacy

- **NFR1:** All passwords must be hashed using Bcrypt (Laravel default).
- **NFR2:** System must enforce HTTPS for all connections.
- **NFR3:** Session timeout must be set to 60 minutes of inactivity to prevent unauthorized access on shared community computers.
- **NFR4:** Database backups must be encrypted at rest.

### Performance

- **NFR5:** Dashboard page load must be under 2 seconds on a standard 4G connection.
- **NFR6:** PDF Report generation must complete within 5 seconds for a community with < 1000 transactions.
- **NFR7:** System must support 50 concurrent users without degradation (initial pilot scale).

### Reliability & Availability

- **NFR8:** System availability target is 99.9% during business hours (8 AM - 8 PM local time).
- **NFR9:** Scheduled maintenance must be performed outside of peak usage hours (e.g., Sunday nights).
- **NFR10:** System must retain daily backups for 30 days.

### Usability

- **NFR11:** All critical workflows (Expense Entry, Member Search) must be navigable via keyboard.
- **NFR12:** Font sizes for main content must be at least 16px to accommodate older users.
- **NFR13:** Error messages must be displayed in plain language (no error codes) with clear recovery steps.
