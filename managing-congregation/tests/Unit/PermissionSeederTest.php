<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\Permission;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that all enum permissions are seeded
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_seeder_creates_all_enum_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $allEnumPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        $seededPermissions = Permission::pluck('key')->toArray();

        foreach ($allEnumPermissions as $enumPermission) {
            $this->assertContains(
                $enumPermission,
                $seededPermissions,
                "Permission '{$enumPermission}' from enum was not seeded"
            );
        }
    }

    /**
     * Test that seeder is idempotent (can run multiple times)
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_seeder_is_idempotent(): void
    {
        // Run seeder first time
        $this->seed(PermissionSeeder::class);
        $firstCount = Permission::count();

        // Run seeder second time
        $this->seed(PermissionSeeder::class);
        $secondCount = Permission::count();

        $this->assertEquals(
            $firstCount,
            $secondCount,
            'Seeder should be idempotent and not create duplicate permissions'
        );
    }

    /**
     * Test that default role assignments are correct for DIRECTOR
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_director_role_has_correct_default_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $expectedPermissions = [
            // Members
            'members.view',
            'members.create',
            'members.edit',
            'members.export',
            // Financials
            'financials.view',
            'financials.create',
            'financials.export',
            // Documents
            'documents.view',
            'documents.upload',
            'documents.download',
            // Communities
            'communities.view',
            // Reports
            'reports.view',
            'reports.generate',
            'reports.export',
        ];

        $directorPermissions = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', UserRole::DIRECTOR->value)
            ->pluck('permissions.key')
            ->toArray();

        foreach ($expectedPermissions as $expected) {
            $this->assertContains(
                $expected,
                $directorPermissions,
                "DIRECTOR role should have '{$expected}' permission"
            );
        }

        // Director should NOT have delete permissions
        $this->assertNotContains(
            'members.delete',
            $directorPermissions,
            'DIRECTOR should not have members.delete permission'
        );

        $this->assertNotContains(
            'financials.approve',
            $directorPermissions,
            'DIRECTOR should not have financials.approve permission'
        );
    }

    /**
     * Test that default role assignments are correct for GENERAL
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_general_role_has_correct_default_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $expectedPermissions = [
            // Members (full access)
            'members.view',
            'members.create',
            'members.edit',
            'members.delete',
            'members.export',
            // Financials (full access)
            'financials.view',
            // 'financials.create', // General Treasurer is read-only
            'financials.approve',
            'financials.export',
            'financials.manage',
            // Documents (full access)
            'documents.view',
            'documents.upload',
            'documents.download',
            'documents.delete',
            'documents.manage',
            // Reports (full access)
            'reports.view',
            'reports.generate',
            'reports.export',
            'reports.schedule',
        ];

        $generalPermissions = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', UserRole::GENERAL->value)
            ->pluck('permissions.key')
            ->toArray();

        foreach ($expectedPermissions as $expected) {
            $this->assertContains(
                $expected,
                $generalPermissions,
                "GENERAL role should have '{$expected}' permission"
            );
        }

        // General should NOT have community management permissions
        $this->assertNotContains(
            'communities.create',
            $generalPermissions,
            'GENERAL should not have communities.create permission'
        );

        $this->assertNotContains(
            'communities.edit',
            $generalPermissions,
            'GENERAL should not have communities.edit permission'
        );
    }

    /**
     * Test that default role assignments are correct for MEMBER
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_member_role_has_limited_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $expectedPermissions = [
            'members.view',
            'documents.view',
            'documents.download',
            'reports.view',
        ];

        $memberPermissions = DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->where('role_permissions.role', UserRole::MEMBER->value)
            ->pluck('permissions.key')
            ->toArray();

        foreach ($expectedPermissions as $expected) {
            $this->assertContains(
                $expected,
                $memberPermissions,
                "MEMBER role should have '{$expected}' permission"
            );
        }

        // Member should NOT have create/edit/delete permissions
        $this->assertNotContains(
            'members.create',
            $memberPermissions,
            'MEMBER should not have members.create permission'
        );

        $this->assertNotContains(
            'financials.view',
            $memberPermissions,
            'MEMBER should not have financials.view permission'
        );

        $this->assertNotContains(
            'documents.upload',
            $memberPermissions,
            'MEMBER should not have documents.upload permission'
        );
    }

    /**
     * Test that all permissions have required fields
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_all_seeded_permissions_have_required_fields(): void
    {
        $this->seed(PermissionSeeder::class);

        $permissions = Permission::all();

        $this->assertGreaterThan(0, $permissions->count());

        foreach ($permissions as $permission) {
            $this->assertNotEmpty($permission->key, 'Permission must have a key');
            $this->assertNotEmpty($permission->name, 'Permission must have a name');
            $this->assertNotEmpty($permission->module, 'Permission must have a module');
        }
    }

    /**
     * Test that permissions are grouped by correct modules
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_permissions_are_grouped_by_module(): void
    {
        $this->seed(PermissionSeeder::class);

        $moduleGroups = [
            'members' => ['members.view', 'members.create', 'members.edit', 'members.delete', 'members.export'],
            'financials' => ['financials.view', 'financials.create', 'financials.approve', 'financials.export', 'financials.manage'],
            'documents' => ['documents.view', 'documents.upload', 'documents.download', 'documents.delete', 'documents.manage'],
            'communities' => ['communities.view', 'communities.create', 'communities.edit', 'communities.assign_members'],
            'reports' => ['reports.view', 'reports.generate', 'reports.export', 'reports.schedule'],
        ];

        foreach ($moduleGroups as $module => $expectedKeys) {
            $modulePermissions = Permission::where('module', $module)->pluck('key')->toArray();

            foreach ($expectedKeys as $expectedKey) {
                $this->assertContains(
                    $expectedKey,
                    $modulePermissions,
                    "Module '{$module}' should contain permission '{$expectedKey}'"
                );
            }
        }
    }

    /**
     * Test that SUPER_ADMIN has no explicit permissions (uses bypass)
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_super_admin_has_no_explicit_permissions(): void
    {
        $this->seed(PermissionSeeder::class);

        $superAdminPermissions = DB::table('role_permissions')
            ->where('role', UserRole::SUPER_ADMIN->value)
            ->count();

        $this->assertEquals(
            0,
            $superAdminPermissions,
            'SUPER_ADMIN should have no explicit permissions (uses policy bypass)'
        );
    }
}
