# System Features Overview

This document provides a high-level overview of the features implemented in the `managing-congregation` application, bridging the gap between the initial PRD and the current implementation.

## 1. Member Management (Epic 2)
Complete lifecycle management for religious members.
-   **Profiles**: Detailed tracking of personal info, religious names, and sacramental data.
-   **Timeline**: Visual history of formation stages (Postulancy, Novitiate, etc.) and assignments.
-   **Documents**: Secure storage for diverse documents (Passports, Medical Records).

## 2. Community & Housing (Epic 3)
Management of the congregation's physical presence.
-   **Community Details**: Tracking of houses, patron saints, and foundation dates.
-   **Assignments**: Managing where members are stationed and their roles (e.g., Superior, Member).

## 3. Celebration & Communication
Tools to foster community spirit.
-   **Birthday Cards**: Automated generation of personalized birthday cards.
    -   **Feature**: Dynamic font selection (Caveat, Fleur De Leah, Roboto).
    -   **Feature**: Automatic branding based on the member's community.
    -   **delivery**: Instant email or download options.

## 4. Security & RBAC (Epic 1)
Robust security model.
-   **Roles**: Super Admin, General, Director, Member.
-   **Permissions**: Granular control over every action.
-   **Scoping**: Directors usually only see data for their own community.

## 5. Technology Stack
-   **Framework**: Laravel 11.x
-   **Language**: PHP 8.2+
-   **Frontend**: Livewire 3 + TailwindCSS
-   **Database**: MySQL 8.0

For detailed technical usage, refer to the `managing-congregation/docs/features.md`.
