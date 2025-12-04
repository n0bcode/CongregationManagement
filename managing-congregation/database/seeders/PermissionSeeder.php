<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all permissions (idempotent)
        $permissions = [
            // Members Module
            ['key' => PermissionKey::MEMBERS_VIEW->value, 'name' => 'View Members', 'module' => 'members'],
            ['key' => PermissionKey::MEMBERS_CREATE->value, 'name' => 'Create Members', 'module' => 'members'],
            ['key' => PermissionKey::MEMBERS_EDIT->value, 'name' => 'Edit Members', 'module' => 'members'],
            ['key' => PermissionKey::MEMBERS_DELETE->value, 'name' => 'Delete Members', 'module' => 'members'],
            ['key' => PermissionKey::MEMBERS_EXPORT->value, 'name' => 'Export Members', 'module' => 'members'],

            // Financials Module
            ['key' => PermissionKey::FINANCIALS_VIEW->value, 'name' => 'View Financials', 'module' => 'financials'],
            ['key' => PermissionKey::FINANCIALS_CREATE->value, 'name' => 'Create Financials', 'module' => 'financials'],
            ['key' => PermissionKey::FINANCIALS_APPROVE->value, 'name' => 'Approve Financials', 'module' => 'financials'],
            ['key' => PermissionKey::FINANCIALS_EXPORT->value, 'name' => 'Export Financials', 'module' => 'financials'],
            ['key' => PermissionKey::FINANCIALS_MANAGE->value, 'name' => 'Manage Financials', 'module' => 'financials'],

            // Documents Module
            ['key' => PermissionKey::DOCUMENTS_VIEW->value, 'name' => 'View Documents', 'module' => 'documents'],
            ['key' => PermissionKey::DOCUMENTS_UPLOAD->value, 'name' => 'Upload Documents', 'module' => 'documents'],
            ['key' => PermissionKey::DOCUMENTS_DOWNLOAD->value, 'name' => 'Download Documents', 'module' => 'documents'],
            ['key' => PermissionKey::DOCUMENTS_DELETE->value, 'name' => 'Delete Documents', 'module' => 'documents'],
            ['key' => PermissionKey::DOCUMENTS_MANAGE->value, 'name' => 'Manage Documents', 'module' => 'documents'],

            // Communities Module
            ['key' => PermissionKey::COMMUNITIES_VIEW->value, 'name' => 'View Communities', 'module' => 'communities'],
            ['key' => PermissionKey::COMMUNITIES_CREATE->value, 'name' => 'Create Communities', 'module' => 'communities'],
            ['key' => PermissionKey::COMMUNITIES_EDIT->value, 'name' => 'Edit Communities', 'module' => 'communities'],
            ['key' => PermissionKey::COMMUNITIES_ASSIGN_MEMBERS->value, 'name' => 'Assign Members to Communities', 'module' => 'communities'],

            // Reports Module
            ['key' => PermissionKey::REPORTS_VIEW->value, 'name' => 'View Reports', 'module' => 'reports'],
            ['key' => PermissionKey::REPORTS_GENERATE->value, 'name' => 'Generate Reports', 'module' => 'reports'],
            ['key' => PermissionKey::REPORTS_EXPORT->value, 'name' => 'Export Reports', 'module' => 'reports'],
            ['key' => PermissionKey::REPORTS_SCHEDULE->value, 'name' => 'Schedule Reports', 'module' => 'reports'],

            // Legacy Modules (keeping for backward compatibility)
            ['key' => PermissionKey::TERRITORIES_VIEW->value, 'name' => 'View Territories', 'module' => 'territories'],
            ['key' => PermissionKey::TERRITORIES_ASSIGN->value, 'name' => 'Assign Territories', 'module' => 'territories'],
            ['key' => PermissionKey::TERRITORIES_MANAGE->value, 'name' => 'Manage Territories', 'module' => 'territories'],
            ['key' => PermissionKey::PUBLISHERS_VIEW->value, 'name' => 'View Publishers', 'module' => 'publishers'],
            ['key' => PermissionKey::PUBLISHERS_MANAGE->value, 'name' => 'Manage Publishers', 'module' => 'publishers'],
            ['key' => PermissionKey::FORMATION_VIEW->value, 'name' => 'View Formation', 'module' => 'formation'],
            ['key' => PermissionKey::FORMATION_MANAGE->value, 'name' => 'Manage Formation', 'module' => 'formation'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['key' => $permission['key']],
                $permission
            );
        }

        // Assign default permissions to roles
        $permissionService = app(PermissionService::class);

        // DIRECTOR: Can view and manage their community's data
        $permissionService->assignPermissionsToRole(UserRole::DIRECTOR, [
            // Members
            PermissionKey::MEMBERS_VIEW->value,
            PermissionKey::MEMBERS_CREATE->value,
            PermissionKey::MEMBERS_EDIT->value,
            PermissionKey::MEMBERS_EXPORT->value,
            // Financials
            PermissionKey::FINANCIALS_VIEW->value,
            PermissionKey::FINANCIALS_CREATE->value,
            PermissionKey::FINANCIALS_EXPORT->value,
            // Documents
            PermissionKey::DOCUMENTS_VIEW->value,
            PermissionKey::DOCUMENTS_UPLOAD->value,
            PermissionKey::DOCUMENTS_DOWNLOAD->value,
            // Communities (view only their own)
            PermissionKey::COMMUNITIES_VIEW->value,
            // Reports
            PermissionKey::REPORTS_VIEW->value,
            PermissionKey::REPORTS_GENERATE->value,
            PermissionKey::REPORTS_EXPORT->value,
            // Legacy
            PermissionKey::TERRITORIES_VIEW->value,
            PermissionKey::TERRITORIES_ASSIGN->value,
            PermissionKey::PUBLISHERS_VIEW->value,
            PermissionKey::PUBLISHERS_MANAGE->value,
            PermissionKey::FORMATION_VIEW->value,
            PermissionKey::FORMATION_MANAGE->value,
        ]);

        // GENERAL: Full access to all modules except community management
        $permissionService->assignPermissionsToRole(UserRole::GENERAL, [
            // Members
            PermissionKey::MEMBERS_VIEW->value,
            PermissionKey::MEMBERS_CREATE->value,
            PermissionKey::MEMBERS_EDIT->value,
            PermissionKey::MEMBERS_DELETE->value,
            PermissionKey::MEMBERS_EXPORT->value,
            // Financials
            PermissionKey::FINANCIALS_VIEW->value,
            PermissionKey::FINANCIALS_CREATE->value,
            PermissionKey::FINANCIALS_APPROVE->value,
            PermissionKey::FINANCIALS_EXPORT->value,
            PermissionKey::FINANCIALS_MANAGE->value,
            // Documents
            PermissionKey::DOCUMENTS_VIEW->value,
            PermissionKey::DOCUMENTS_UPLOAD->value,
            PermissionKey::DOCUMENTS_DOWNLOAD->value,
            PermissionKey::DOCUMENTS_DELETE->value,
            PermissionKey::DOCUMENTS_MANAGE->value,
            // Communities (view all)
            PermissionKey::COMMUNITIES_VIEW->value,
            // Reports
            PermissionKey::REPORTS_VIEW->value,
            PermissionKey::REPORTS_GENERATE->value,
            PermissionKey::REPORTS_EXPORT->value,
            PermissionKey::REPORTS_SCHEDULE->value,
            // Legacy
            PermissionKey::TERRITORIES_VIEW->value,
            PermissionKey::TERRITORIES_ASSIGN->value,
            PermissionKey::TERRITORIES_MANAGE->value,
            PermissionKey::PUBLISHERS_VIEW->value,
            PermissionKey::PUBLISHERS_MANAGE->value,
            PermissionKey::FORMATION_VIEW->value,
            PermissionKey::FORMATION_MANAGE->value,
        ]);

        // MEMBER: Limited view-only access
        $permissionService->assignPermissionsToRole(UserRole::MEMBER, [
            // Members (view only)
            PermissionKey::MEMBERS_VIEW->value,
            // Documents (view and download only)
            PermissionKey::DOCUMENTS_VIEW->value,
            PermissionKey::DOCUMENTS_DOWNLOAD->value,
            // Reports (view only)
            PermissionKey::REPORTS_VIEW->value,
            // Legacy
            PermissionKey::TERRITORIES_VIEW->value,
        ]);

        // SUPER_ADMIN: No explicit permissions needed (bypass in policies)
        // Super admins have universal access without permission checks
    }
}
