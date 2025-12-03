<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_super_admin_has_all_permissions_automatically(): void
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $this->assertTrue($user->hasPermission(PermissionKey::TERRITORIES_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::TERRITORIES_MANAGE));
        $this->assertTrue($user->hasPermission(PermissionKey::PUBLISHERS_MANAGE));
        $this->assertTrue($user->hasPermission(PermissionKey::REPORTS_EXPORT));
        $this->assertTrue($user->hasPermission('any.permission'));
    }

    public function test_director_has_assigned_permissions_only(): void
    {
        $user = User::factory()->create(['role' => UserRole::DIRECTOR]);

        $this->assertTrue($user->hasPermission(PermissionKey::TERRITORIES_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::TERRITORIES_ASSIGN));
        $this->assertTrue($user->hasPermission(PermissionKey::PUBLISHERS_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::PUBLISHERS_MANAGE));

        $this->assertFalse($user->hasPermission(PermissionKey::TERRITORIES_MANAGE));
        $this->assertFalse($user->hasPermission(PermissionKey::REPORTS_EXPORT));
    }

    public function test_member_has_limited_permissions(): void
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);

        $this->assertTrue($user->hasPermission(PermissionKey::TERRITORIES_VIEW));
        $this->assertFalse($user->hasPermission(PermissionKey::TERRITORIES_ASSIGN));
        $this->assertFalse($user->hasPermission(PermissionKey::PUBLISHERS_MANAGE));
    }

    public function test_has_permission_works_with_both_enum_and_string(): void
    {
        $user = User::factory()->create(['role' => UserRole::DIRECTOR]);

        // Test with enum
        $this->assertTrue($user->hasPermission(PermissionKey::TERRITORIES_VIEW));

        // Test with string
        $this->assertTrue($user->hasPermission('territories.view'));
    }

    public function test_is_super_admin_helper_method(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $director = User::factory()->create(['role' => UserRole::DIRECTOR]);

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($director->isSuperAdmin());
    }
}
