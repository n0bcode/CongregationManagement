# Scope Conflict: Project Management Module

**Date:** 2025-12-01
**Status:** Resolved
**Resolution Date:** 2025-12-02

## Conflict Description

There is a discrepancy between the approved `product-brief` and the current user request regarding the "Project Management" module.

- **Source A (Product Brief):** Explicitly lists "Project Management Module (tracking projects and grants)" as **Out of Scope for MVP (Deferred to Phase 2)**.
- **Source B (User Request - Step 51):** User has provided specific requirements for Project Management (Jira-like process, budgeting, evidence uploading, balance checks) and requested immediate diagram generation.

## Resolution Strategy

**Action:** Proceed with generating the requested diagrams (Flowchart, Sequence, Class, State) based on the specific requirements provided in the chat.

**Resolution:** The Product Brief, PRD, and Epics have been updated to explicitly include "Project Management" in the MVP scope (Epic 5). The conflict is resolved.

**Implication:** The `product-brief` and `epics.md` are now out of sync with the design work being performed. These documents will need to be updated later to reflect that Project Management is now being actively designed/considered, or these diagrams will serve as "Phase 2 Preview" artifacts.

## Specific Requirements Captured

- **Workflow:** Create Project -> Plan (Duration, Budget, Target) -> Assign Roles (PM, BM, Staff) -> Monitor/Track -> Upload Evidence -> Close -> Report.
- **Key Logic:** "Balance Check" at closing. If Actual Expenses < Budget (e.g., 70k vs 100k), trigger a warning to prevent unbalanced closing.
- **Domain Examples:** Schools, Hospitals, Agriculture (e.g., South Sudan).
- **Audit:** Distinct auditing capability required.
