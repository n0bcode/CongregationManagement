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
        // Create all MVP permissions (idempotent)
        $permissions = [
            // Territories
            ['key' => PermissionKey::TERRITORIES_VIEW->value, 'name' => 'View Territories', 'module' => 'territories'],
            ['key' => PermissionKey::TERRITORIES_ASSIGN->value, 'name' => 'Assign Territories', 'module' => 'territories'],
            ['key' => PermissionKey::TERRITORIES_MANAGE->value, 'name' => 'Manage Territories', 'module' => 'territories'],
            
            // Publishers
            ['key' => PermissionKey::PUBLISHERS_VIEW->value, 'name' => 'View Publishers', 'module' => 'publishers'],
            ['key' => PermissionKey::PUBLISHERS_MANAGE->value, 'name' => 'Manage Publishers', 'module' => 'publishers'],
            
            // Reports
            ['key' => PermissionKey::REPORTS_VIEW->value, 'name' => 'View Reports', 'module' => 'reports'],
            ['key' => PermissionKey::REPORTS_EXPORT->value, 'name' => 'Export Reports', 'module' => 'reports'],
            
            // Formation
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

        // DIRECTOR: territories.view, territories.assign, publishers.view, publishers.manage, formation.view, formation.manage
        $permissionService->assignPermissionsToRole(UserRole::DIRECTOR, [
            PermissionKey::TERRITORIES_VIEW->value,
            PermissionKey::TERRITORIES_ASSIGN->value,
            PermissionKey::PUBLISHERS_VIEW->value,
            PermissionKey::PUBLISHERS_MANAGE->value,
            PermissionKey::FORMATION_VIEW->value,
            PermissionKey::FORMATION_MANAGE->value,
        ]);

        // GENERAL: All permissions except user management
        $permissionService->assignPermissionsToRole(UserRole::GENERAL, [
            PermissionKey::TERRITORIES_VIEW->value,
            PermissionKey::TERRITORIES_ASSIGN->value,
            PermissionKey::TERRITORIES_MANAGE->value,
            PermissionKey::PUBLISHERS_VIEW->value,
            PermissionKey::PUBLISHERS_MANAGE->value,
            PermissionKey::REPORTS_VIEW->value,
            PermissionKey::REPORTS_EXPORT->value,
            PermissionKey::FORMATION_VIEW->value,
            PermissionKey::FORMATION_MANAGE->value,
        ]);

        // MEMBER: territories.view (own assigned only - enforced in future story)
        $permissionService->assignPermissionsToRole(UserRole::MEMBER, [
            PermissionKey::TERRITORIES_VIEW->value,
        ]);
    }
}
