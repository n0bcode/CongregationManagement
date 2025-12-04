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

        // Director should have view, create, edit, export permissions
        $this->assertTrue($user->hasPermission(PermissionKey::MEMBERS_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::MEMBERS_CREATE));
        $this->assertTrue($user->hasPermission(PermissionKey::FINANCIALS_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::REPORTS_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::REPORTS_EXPORT));

        // Director should NOT have delete or approve permissions
        $this->assertFalse($user->hasPermission(PermissionKey::MEMBERS_DELETE));
        $this->assertFalse($user->hasPermission(PermissionKey::FINANCIALS_APPROVE));
        $this->assertFalse($user->hasPermission(PermissionKey::COMMUNITIES_CREATE));
    }

    public function test_member_has_limited_permissions(): void
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);

        // Member should have view-only permissions
        $this->assertTrue($user->hasPermission(PermissionKey::MEMBERS_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::DOCUMENTS_VIEW));
        $this->assertTrue($user->hasPermission(PermissionKey::REPORTS_VIEW));

        // Member should NOT have create/edit/delete permissions
        $this->assertFalse($user->hasPermission(PermissionKey::MEMBERS_CREATE));
        $this->assertFalse($user->hasPermission(PermissionKey::DOCUMENTS_UPLOAD));
        $this->assertFalse($user->hasPermission(PermissionKey::FINANCIALS_VIEW));
    }

    public function test_has_permission_works_with_both_enum_and_string(): void
    {
        $user = User::factory()->create(['role' => UserRole::DIRECTOR]);

        // Test with enum
        $this->assertTrue($user->hasPermission(PermissionKey::MEMBERS_VIEW));

        // Test with string
        $this->assertTrue($user->hasPermission('members.view'));
    }

    public function test_is_super_admin_helper_method(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $director = User::factory()->create(['role' => UserRole::DIRECTOR]);

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertFalse($director->isSuperAdmin());
    }
}
