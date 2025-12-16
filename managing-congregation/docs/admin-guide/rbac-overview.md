# Role-Based Access Control (RBAC) Overview

This system uses a granular permission model to ensure data security. Every user is assigned a **Role**, which determines what they can see and do.

![Permissions Management Screen - Screenshot](../images/admin-permissions.png)

## 🎭 Roles

The system comes with pre-defined roles to fit standard organizational structures:

1.  **Super Admin**:
    -   _access_: Unlimited.
    -   _capabilities_: Can change settings, manage permissions, view audit logs, and access all data.
2.  **General Admin**:
    -   _access_: High-level management.
    -   _capabilities_: Can create members and specific communities, but cannot change system-wide settings or RBAC.
3.  **Community Superior** (Manager):
    -   _access_: Community-focused.
    -   _capabilities_: Can edit details for members in their own community, approve expenses, and manage local projects.
4.  **Observer**:
    -   _access_: Read-only.
    -   _capabilities_: Can view member profiles and basic reports but cannot make changes.

---

## 🔑 Managing Permissions

Permissions are granular rights (e.g., `member.create`, `financials.view`).

### Assigning Roles

1.  Go to **Admin > Users**.
2.  Click **Edit** on a user.
3.  Select the new **Role** from the dropdown.
4.  Click **Save**.

### Modifying Role Permissions

_Caution: Changing permissions affects all users with that role._

1.  Go to **Admin > Permissions**.
2.  Select the **Role** to edit.
3.  Check or Uncheck the specific boxes (e.g., `Export Data`).
4.  Click **Update Permissions**.

![Edit Role Permissions - Screenshot](../images/admin-role-edit.png)

## 🛡 Audit Logs

To see who changed what:

-   Go to **Admin > Audit Logs**.
-   The log shows **User**, **Action** (e.g., "Updated Member Profile"), **Target**, and **Date**.

![Audit Log Table - Screenshot](../images/admin-audit-log.png)
