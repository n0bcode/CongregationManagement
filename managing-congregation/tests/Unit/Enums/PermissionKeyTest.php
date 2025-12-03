<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\PermissionKey;
use PHPUnit\Framework\TestCase;

class PermissionKeyTest extends TestCase
{
    public function test_permission_key_enum_has_all_required_mvp_permissions(): void
    {
        $expectedPermissions = [
            'territories.view',
            'territories.assign',
            'territories.manage',
            'publishers.view',
            'publishers.manage',
            'reports.view',
            'reports.export',
        ];

        $actualPermissions = array_map(
            fn (PermissionKey $case) => $case->value,
            PermissionKey::cases()
        );

        $this->assertEquals(
            sort($expectedPermissions),
            sort($actualPermissions),
            'PermissionKey enum must have all MVP permissions'
        );
    }

    public function test_permission_key_enum_values_follow_naming_convention(): void
    {
        foreach (PermissionKey::cases() as $permission) {
            $this->assertMatchesRegularExpression(
                '/^[a-z]+\.[a-z]+$/',
                $permission->value,
                "Permission '{$permission->value}' must follow module.action format"
            );
        }
    }
}
