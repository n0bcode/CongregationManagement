# RBAC Permission Matrix

## Overview

This document provides a comprehensive view of all permissions in the Managing the Congregation application and their default assignments to user roles.

**Last Updated:** December 2025  
**Version:** 1.0

## Role Definitions

| Role                  | Code          | Description                                        | Scope                                 |
| --------------------- | ------------- | -------------------------------------------------- | ------------------------------------- |
| **Super Admin**       | `super_admin` | Full system access, bypasses all permission checks | Global (all communities)              |
| **General Secretary** | `general`     | Administrative access to all features              | Global (all communities)              |
| **Director**          | `director`    | Community management and member oversight          | Community-scoped (own community only) |
| **Member**            | `member`      | Basic access to view own information               | Self-scoped (own profile only)        |

## Permission Matrix

### Legend

-   ✅ = Permission granted by default
-   ❌ = Permission not granted
-   🔒 = Always granted (Super Admin bypass)
-   🏠 = Community-scoped (Director sees only own community)

### Members Module

Permissions for managing member profiles and information.

| Permission       | Description                   | Super Admin | General | Director | Member |
| ---------------- | ----------------------------- | ----------- | ------- | -------- | ------ |
| `members.view`   | View member list and profiles | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `members.create` | Create new member profiles    | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `members.edit`   | Edit existing member profiles | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `members.delete` | Delete member profiles        | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `members.export` | Export member data            | 🔒          | ✅      | ✅ 🏠    | ❌     |

**Use Cases:**

-   Directors manage members in their community
-   General Secretaries oversee all communities
-   Members cannot manage other members

### Financials Module

Permissions for financial transaction management and reporting.

| Permission           | Description                    | Super Admin | General | Director | Member |
| -------------------- | ------------------------------ | ----------- | ------- | -------- | ------ |
| `financials.view`    | View financial records         | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `financials.create`  | Create financial transactions  | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `financials.approve` | Approve financial transactions | 🔒          | ✅      | ❌       | ❌     |
| `financials.export`  | Export financial reports       | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `financials.manage`  | Full financial management      | 🔒          | ✅      | ❌       | ❌     |

**Use Cases:**

-   Directors manage finances for their community
-   Only General Secretaries can approve transactions
-   Financial management reserved for administrators

### Documents Module

Permissions for document storage and management.

| Permission           | Description              | Super Admin | General | Director | Member |
| -------------------- | ------------------------ | ----------- | ------- | -------- | ------ |
| `documents.view`     | View documents           | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `documents.upload`   | Upload new documents     | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `documents.download` | Download documents       | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `documents.delete`   | Delete documents         | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `documents.manage`   | Full document management | 🔒          | ✅      | ❌       | ❌     |

**Use Cases:**

-   Directors manage documents for their community
-   Document management includes folder organization
-   Members have no document access by default

### Communities Module

Permissions for community administration.

| Permission                   | Description                   | Super Admin | General | Director | Member |
| ---------------------------- | ----------------------------- | ----------- | ------- | -------- | ------ |
| `communities.view`           | View community list           | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `communities.create`         | Create new communities        | 🔒          | ✅      | ❌       | ❌     |
| `communities.edit`           | Edit community details        | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `communities.assign_members` | Assign members to communities | 🔒          | ✅      | ❌       | ❌     |

**Use Cases:**

-   Only administrators can create communities
-   Directors can edit their own community details
-   Member assignment reserved for General Secretaries

### Reports Module

Permissions for report generation and export.

| Permission         | Description                | Super Admin | General | Director | Member |
| ------------------ | -------------------------- | ----------- | ------- | -------- | ------ |
| `reports.view`     | View reports               | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `reports.generate` | Generate new reports       | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `reports.export`   | Export reports             | 🔒          | ✅      | ✅ 🏠    | ❌     |
| `reports.schedule` | Schedule automated reports | 🔒          | ✅      | ❌       | ❌     |

**Use Cases:**

-   Directors generate reports for their community
-   Report scheduling reserved for administrators
-   All reports respect community scoping

### Administrative Permissions

Special permissions for system administration.

| Permission           | Description             | Super Admin | General | Director | Member |
| -------------------- | ----------------------- | ----------- | ------- | -------- | ------ |
| `view-admin`         | Access admin panel      | 🔒          | ✅      | ❌       | ❌     |
| `manage-permissions` | Manage role permissions | 🔒          | ✅      | ❌       | ❌     |
| `view-audit-logs`    | View audit logs         | 🔒          | ✅      | ❌       | ❌     |

**Use Cases:**

-   Only Super Admin and General Secretary can access admin features
-   Permission management highly restricted
-   Audit logs for compliance and security

## Permission Inheritance

### Super Admin Bypass

Super Admins have a special bypass mechanism:

-   All permission checks return `true` automatically
-   No explicit permissions need to be assigned
-   Cannot be restricted or limited
-   Implemented at the policy level for performance

```php
// In Policy before() method
if ($user->role === UserRole::SUPER_ADMIN) {
    return true; // Bypass all checks
}
```

### Community Scoping

Directors have automatic community scoping:

-   All queries filtered to their `community_id`
-   Applied via Laravel Global Scopes
-   Cannot access other communities' data
-   Enforced at the database level

```php
// Automatic scope applied
Member::all(); // Director sees only their community
```

## Default Permission Assignments

### Super Admin

-   **All permissions** via bypass mechanism
-   No explicit assignments needed
-   Cannot be restricted

### General Secretary

-   All module permissions
-   All administrative permissions
-   Global scope (all communities)

### Director

-   Members: view, create, edit, delete, export
-   Financials: view, create, export
-   Documents: view, upload, download, delete
-   Communities: view, edit (own only)
-   Reports: view, generate, export
-   Community-scoped

### Member

-   No default permissions
-   Can view own profile (hardcoded)
-   Cannot access other features

## Customizing Permissions

### Adding Permissions to a Role

1. Navigate to **Admin** → **Permissions**
2. Select the role to modify
3. Check additional permissions
4. Click **Save Changes**

### Removing Permissions from a Role

1. Navigate to **Admin** → **Permissions**
2. Select the role to modify
3. Uncheck permissions to remove
4. Click **Save Changes**

### Creating Custom Permission Sets

While custom roles are not supported, you can customize existing roles:

**Example: Read-Only Director**

-   Keep: `*.view` permissions
-   Remove: `*.create`, `*.edit`, `*.delete` permissions

**Example: Finance-Only Director**

-   Keep: `financials.*` permissions
-   Remove: Other module permissions

## Permission Naming Convention

All permissions follow this pattern:

```
{module}.{action}
```

### Modules

-   `members` - Member management
-   `financials` - Financial management
-   `documents` - Document management
-   `communities` - Community administration
-   `reports` - Reporting

### Actions

-   `view` - Read access
-   `create` - Create new records
-   `edit` - Modify existing records
-   `delete` - Remove records
-   `export` - Export data
-   `manage` - Full management (includes all actions)
-   `approve` - Approval workflow
-   `upload` - File upload
-   `download` - File download
-   `generate` - Generate reports
-   `schedule` - Schedule automated tasks
-   `assign_members` - Assign users

## Permission Checking

### In Code

```php
// Check if user has permission
if ($user->hasPermission('members.view')) {
    // Allow access
}

// Using policies
if ($user->can('view', Member::class)) {
    // Allow access
}

// Using gates
if (Gate::allows('view-admin')) {
    // Allow access
}
```

### In Blade Templates

```blade
@can('view', $member)
    <a href="{{ route('members.show', $member) }}">View</a>
@endcan

@if(Auth::user()->hasPermission('members.edit'))
    <button>Edit</button>
@endif
```

### In Routes

```php
// Using middleware
Route::middleware(['auth', 'permission:members.view'])
    ->get('/members', [MemberController::class, 'index']);

// Using gate
Route::middleware(['auth', 'can:view-admin'])
    ->get('/admin', [AdminController::class, 'index']);
```

## Audit Trail

All permission changes are logged:

### Logged Events

-   Permission assignments to roles
-   Permission revocations from roles
-   User role changes
-   Permission sync operations

### Audit Log Fields

-   Timestamp
-   Admin user who made the change
-   Action type
-   Target (role or user)
-   Changes made
-   IP address
-   User agent

### Viewing Audit Logs

Navigate to **Admin** → **Permissions** → **Audit Log**

## Security Considerations

### Principle of Least Privilege

-   Grant minimum necessary permissions
-   Review permissions regularly
-   Remove unused permissions

### Separation of Duties

-   Financial approval separate from creation
-   Community assignment separate from management
-   Admin access limited to trusted users

### Regular Audits

-   Monthly permission reviews
-   Quarterly role audits
-   Annual access certification

## Troubleshooting

### User Can't Access Feature

1. Check user's role
2. Verify role has required permission
3. Clear cache: `php artisan cache:clear`
4. Check audit logs for recent changes

### Permission Changes Not Working

1. Clear application cache
2. Ask user to log out and back in
3. Verify permission exists in database
4. Check for typos in permission key

### Community Scoping Not Working

1. Verify user has `community_id` set
2. Check model uses `ScopedByCommunity` trait
3. Verify global scope is registered
4. Check for `withoutGlobalScopes()` calls

## References

-   [Admin Guide](./rbac-admin-guide.md)
-   [Performance Optimization](./rbac-performance-optimization.md)
-   [Deployment Checklist](./rbac-deployment-checklist.md)
-   [Error Handling](./rbac-error-handling.md)

---

**Maintained By:** Development Team  
**Review Schedule:** Quarterly  
**Next Review:** March 2026
