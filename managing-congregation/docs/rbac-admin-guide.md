# RBAC System - Administrator Guide

## Overview

The Role-Based Access Control (RBAC) system manages user permissions and access rights throughout the Managing the Congregation application. This guide will help administrators understand and manage the permission system.

## Table of Contents

1. [Understanding Roles and Permissions](#understanding-roles-and-permissions)
2. [Managing Permissions](#managing-permissions)
3. [Syncing Permissions](#syncing-permissions)
4. [Viewing Audit Logs](#viewing-audit-logs)
5. [Common Tasks](#common-tasks)
6. [Troubleshooting](#troubleshooting)

## Understanding Roles and Permissions

### User Roles

The system has four predefined roles:

| Role                  | Description           | Access Level                       |
| --------------------- | --------------------- | ---------------------------------- |
| **Super Admin**       | Full system access    | All features, all communities      |
| **General Secretary** | Administrative access | All features, all communities      |
| **Director**          | Community management  | All features, own community only   |
| **Member**            | Basic access          | View own profile, limited features |

### Permission Structure

Permissions follow a `module.action` naming convention:

-   **Module**: The feature area (members, financials, documents, etc.)
-   **Action**: The operation (view, create, edit, delete, etc.)

**Examples:**

-   `members.view` - View member list
-   `members.create` - Create new members
-   `financials.approve` - Approve financial transactions
-   `documents.upload` - Upload documents

### Permission Modules

The system includes permissions for these modules:

1. **Members** - Member profile management
2. **Financials** - Financial transactions and reporting
3. **Documents** - Document management
4. **Communities** - Community administration
5. **Reports** - Report generation and export

## Managing Permissions

### Accessing Permission Management

1. Log in as a **Super Admin** or **General Secretary**
2. Navigate to **Admin** → **Permissions** in the main menu
3. You'll see the Permission Management interface

### Permission Matrix

The permission matrix shows:

-   **Rows**: Available permissions grouped by module
-   **Columns**: User roles
-   **Checkboxes**: Indicate which permissions are assigned to each role

### Assigning Permissions

To assign permissions to a role:

1. Select the role from the dropdown at the top
2. Check the boxes for permissions you want to grant
3. Uncheck boxes for permissions you want to revoke
4. Click **Save Changes**
5. Confirm the success message

**Important Notes:**

-   Changes take effect immediately
-   All users with that role will have their permission cache invalidated
-   Super Admin role always has all permissions (cannot be modified)

### Best Practices

✅ **DO:**

-   Follow the principle of least privilege (grant minimum necessary permissions)
-   Test permission changes with a test user account
-   Document why you're making permission changes
-   Review permissions regularly (quarterly recommended)

❌ **DON'T:**

-   Grant all permissions to non-admin roles
-   Remove critical permissions without testing
-   Make changes during peak usage hours
-   Forget to communicate changes to affected users

## Syncing Permissions

### What is Permission Syncing?

Permission syncing automatically discovers permissions from your application routes and updates the database. This ensures your permission list stays in sync with your codebase.

### When to Sync

Sync permissions when:

-   New features are deployed
-   Routes are added or modified
-   Permission middleware is updated
-   After major application updates

### How to Sync

#### Via Web Interface

1. Go to **Admin** → **Permissions**
2. Click the **Sync Permissions** button
3. Review the sync report showing:
    - New permissions created
    - Updated permissions
    - Orphaned permissions (marked inactive)
4. Confirm the changes

#### Via Command Line

```bash
# Preview changes without applying
php artisan permissions:sync --dry-run

# Apply changes with confirmation prompt
php artisan permissions:sync

# Apply changes without confirmation
php artisan permissions:sync --force
```

### Understanding Sync Results

**New Permissions:**

-   Permissions found in routes but not in database
-   Automatically created and set to active

**Updated Permissions:**

-   Existing permissions with changed metadata
-   Name or module updated to match routes

**Orphaned Permissions:**

-   Permissions in database but not in routes
-   Marked as inactive (not deleted)
-   Can be reactivated if routes are restored

## Viewing Audit Logs

### Accessing Audit Logs

1. Navigate to **Admin** → **Permissions** → **Audit Log**
2. View chronological list of all permission changes

### Audit Log Information

Each log entry shows:

-   **Date/Time**: When the change occurred
-   **Admin User**: Who made the change
-   **Action**: Type of change (permission updated, role changed)
-   **Target**: What was changed (role name, user)
-   **Details**: Specific permissions affected
-   **IP Address**: Where the change originated

### Filtering Audit Logs

Use the filters to find specific changes:

-   Filter by date range
-   Filter by admin user
-   Filter by action type
-   Filter by target role

### Exporting Audit Logs

For compliance or reporting:

1. Apply desired filters
2. Click **Export** button
3. Choose format (CSV, PDF)
4. Download the report

## Common Tasks

### Task 1: Grant a User Access to Financials

**Scenario:** A Director needs to manage financial records.

**Steps:**

1. Go to **Admin** → **Permissions**
2. Select **Director** role
3. Check these permissions:
    - `financials.view`
    - `financials.create`
    - `financials.edit`
4. Click **Save Changes**
5. Notify the user of their new access

### Task 2: Create a Read-Only Role

**Scenario:** You want users who can view but not modify data.

**Note:** Custom roles are not currently supported. Use the **Member** role and adjust its permissions:

1. Select **Member** role
2. Check only `*.view` permissions:
    - `members.view`
    - `financials.view`
    - `documents.view`
3. Uncheck all create/edit/delete permissions
4. Save changes

### Task 3: Revoke Access After User Leaves

**Scenario:** A Director is leaving and should lose access.

**Steps:**

1. Go to **Users** management
2. Find the user
3. Change their role to **Member** (lowest access)
4. Or deactivate their account entirely
5. Their permission cache is automatically cleared

### Task 4: Audit Recent Permission Changes

**Scenario:** Investigate who changed permissions last week.

**Steps:**

1. Go to **Admin** → **Permissions** → **Audit Log**
2. Set date filter to last 7 days
3. Review all changes
4. Export report if needed for documentation

### Task 5: Restore Permissions After Deployment

**Scenario:** New deployment added features, need to update permissions.

**Steps:**

1. Run `php artisan permissions:sync --dry-run` to preview
2. Review the changes
3. Run `php artisan permissions:sync --force` to apply
4. Go to Permission Management UI
5. Assign new permissions to appropriate roles
6. Test with a user account

## Troubleshooting

### Problem: User Can't Access Feature They Should Have

**Possible Causes:**

1. Permission not assigned to their role
2. Cache not invalidated
3. User has wrong role

**Solutions:**

1. Check Permission Matrix - verify role has the permission
2. Clear cache: `php artisan cache:clear`
3. Check user's role in Users management
4. Ask user to log out and log back in

### Problem: Permission Changes Not Taking Effect

**Possible Causes:**

1. Cache not invalidating properly
2. Browser cache
3. Multiple sessions

**Solutions:**

1. Clear application cache: `php artisan cache:clear`
2. Ask user to clear browser cache (Ctrl+Shift+Delete)
3. Ask user to log out completely and log back in
4. Check cache driver is working: `php artisan cache:clear && php artisan cache:forget user_permissions_*`

### Problem: Sync Command Shows Many Orphaned Permissions

**Possible Causes:**

1. Routes were removed or renamed
2. Middleware was changed
3. Features were deprecated

**Solutions:**

1. Review the orphaned permissions list
2. If intentional (feature removed), leave them inactive
3. If unintentional (route renamed), update routes to use correct middleware
4. Run sync again to verify

### Problem: Audit Log Not Recording Changes

**Possible Causes:**

1. Database connection issue
2. Audit logger service error
3. User not authenticated

**Solutions:**

1. Check application logs: `storage/logs/laravel.log`
2. Verify database connection
3. Ensure admin is properly logged in
4. Check AuditLog model and table exist

### Problem: Super Admin Can't Access Permission Management

**Possible Causes:**

1. User role not set correctly
2. Gate definition issue
3. Middleware not applied

**Solutions:**

1. Verify user has `role = 'super_admin'` in database
2. Check `app/Providers/AppServiceProvider.php` for gate definition
3. Clear config cache: `php artisan config:clear`
4. Check routes have `can:view-admin` middleware

## Security Best Practices

### Access Control

1. **Limit Super Admin Accounts**

    - Only 2-3 trusted individuals
    - Use strong passwords
    - Enable 2FA if available

2. **Regular Audits**

    - Review audit logs monthly
    - Check for suspicious permission changes
    - Verify user roles are appropriate

3. **Principle of Least Privilege**
    - Grant minimum necessary permissions
    - Review and revoke unused permissions
    - Don't grant "just in case" permissions

### Monitoring

1. **Watch for Anomalies**

    - Unusual permission changes
    - Changes outside business hours
    - Bulk permission grants

2. **Set Up Alerts**
    - Email notifications for permission changes
    - Slack/Teams integration for audit events
    - Monitor failed authorization attempts

### Compliance

1. **Document Changes**

    - Keep records of why permissions changed
    - Note who requested changes
    - Track approval process

2. **Regular Reviews**
    - Quarterly permission audits
    - Annual role reviews
    - Update documentation

## Quick Reference

### Common Commands

```bash
# Sync permissions from routes
php artisan permissions:sync

# Preview sync without changes
php artisan permissions:sync --dry-run

# Clear permission cache
php artisan cache:clear

# View all routes with permissions
php artisan route:list --columns=uri,name,middleware
```

### Permission Naming Convention

```
{module}.{action}

Examples:
- members.view
- members.create
- members.edit
- members.delete
- members.export
```

### Role Hierarchy

```
Super Admin (all permissions)
    ↓
General Secretary (all permissions, all communities)
    ↓
Director (full access, own community only)
    ↓
Member (limited access, own profile only)
```

## Getting Help

### Support Resources

1. **Technical Documentation**

    - `/docs/rbac-performance-optimization.md`
    - `/docs/rbac-error-handling.md`

2. **Application Logs**

    - `storage/logs/laravel.log`
    - Filter for "permission" or "RBAC"

3. **Database**
    - `permissions` table - all available permissions
    - `role_permissions` table - role assignments
    - `audit_logs` table - change history

### Contact

For technical support or questions:

-   Check application logs first
-   Review this documentation
-   Contact your system administrator
-   Escalate to development team if needed

---

**Last Updated:** December 2025  
**Version:** 1.0  
**Maintained By:** Development Team
