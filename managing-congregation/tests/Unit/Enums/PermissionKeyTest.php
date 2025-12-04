<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\PermissionKey;
use PHPUnit\Framework\TestCase;

class PermissionKeyTest extends TestCase
{
    /**
     * Property 1: All required module permissions exist
     * Validates: Requirements 1.1, 1.2, 1.3, 1.4, 1.5
     */
    public function test_permission_key_enum_has_all_required_module_permissions(): void
    {
        // Define all required permissions by module
        $requiredPermissions = [
            // Members module (Requirement 1.1)
            'members.view',
            'members.create',
            'members.edit',
            'members.delete',
            'members.export',

            // Financials module (Requirement 1.2)
            'financials.view',
            'financials.create',
            'financials.approve',
            'financials.export',
            'financials.manage',

            // Documents module (Requirement 1.3)
            'documents.view',
            'documents.upload',
            'documents.download',
            'documents.delete',
            'documents.manage',

            // Communities module (Requirement 1.4)
            'communities.view',
            'communities.create',
            'communities.edit',
            'communities.assign_members',

            // Reports module (Requirement 1.5)
            'reports.view',
            'reports.generate',
            'reports.export',
            'reports.schedule',
        ];

        $actualPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        // Check that all required permissions exist
        foreach ($requiredPermissions as $required) {
            $this->assertContains(
                $required,
                $actualPermissions,
                "Required permission '{$required}' is missing from PermissionKey enum"
            );
        }
    }

    public function test_members_module_has_all_required_permissions(): void
    {
        $requiredMembersPermissions = [
            'members.view',
            'members.create',
            'members.edit',
            'members.delete',
            'members.export',
        ];

        $actualPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        foreach ($requiredMembersPermissions as $permission) {
            $this->assertContains(
                $permission,
                $actualPermissions,
                "Members module permission '{$permission}' is missing"
            );
        }
    }

    public function test_financials_module_has_all_required_permissions(): void
    {
        $requiredFinancialsPermissions = [
            'financials.view',
            'financials.create',
            'financials.approve',
            'financials.export',
            'financials.manage',
        ];

        $actualPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        foreach ($requiredFinancialsPermissions as $permission) {
            $this->assertContains(
                $permission,
                $actualPermissions,
                "Financials module permission '{$permission}' is missing"
            );
        }
    }

    public function test_documents_module_has_all_required_permissions(): void
    {
        $requiredDocumentsPermissions = [
            'documents.view',
            'documents.upload',
            'documents.download',
            'documents.delete',
            'documents.manage',
        ];

        $actualPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        foreach ($requiredDocumentsPermissions as $permission) {
            $this->assertContains(
                $permission,
                $actualPermissions,
                "Documents module permission '{$permission}' is missing"
            );
        }
    }

    public function test_communities_module_has_all_required_permissions(): void
    {
        $requiredCommunitiesPermissions = [
            'communities.view',
            'communities.create',
            'communities.edit',
            'communities.assign_members',
        ];

        $actualPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        foreach ($requiredCommunitiesPermissions as $permission) {
            $this->assertContains(
                $permission,
                $actualPermissions,
                "Communities module permission '{$permission}' is missing"
            );
        }
    }

    public function test_reports_module_has_all_required_permissions(): void
    {
        $requiredReportsPermissions = [
            'reports.view',
            'reports.generate',
            'reports.export',
            'reports.schedule',
        ];

        $actualPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        foreach ($requiredReportsPermissions as $permission) {
            $this->assertContains(
                $permission,
                $actualPermissions,
                "Reports module permission '{$permission}' is missing"
            );
        }
    }

    public function test_permission_key_enum_values_follow_naming_convention(): void
    {
        foreach (PermissionKey::cases() as $permission) {
            $this->assertMatchesRegularExpression(
                '/^[a-z_]+\.[a-z_]+$/',
                $permission->value,
                "Permission '{$permission->value}' must follow module.action format (lowercase with underscores)"
            );
        }
    }

    public function test_permission_keys_are_unique(): void
    {
        $permissionValues = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        $uniqueValues = array_unique($permissionValues);

        $this->assertCount(
            count($permissionValues),
            $uniqueValues,
            'All permission keys must be unique'
        );
    }

    public function test_permission_keys_have_valid_module_names(): void
    {
        $validModules = [
            'members',
            'financials',
            'documents',
            'communities',
            'reports',
            'territories',
            'publishers',
            'formation',
        ];

        foreach (PermissionKey::cases() as $permission) {
            $module = explode('.', $permission->value)[0];

            $this->assertContains(
                $module,
                $validModules,
                "Permission '{$permission->value}' has invalid module name '{$module}'"
            );
        }
    }
}
