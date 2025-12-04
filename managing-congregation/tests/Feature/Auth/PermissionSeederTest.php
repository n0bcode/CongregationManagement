<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PermissionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_permission_seeder_creates_all_mvp_permissions(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        // Count all permissions from PermissionKey enum
        $expectedCount = count(PermissionKey::cases());
        $this->assertDatabaseCount('permissions', $expectedCount);

        // Check that all new module permissions exist
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::MEMBERS_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::FINANCIALS_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::DOCUMENTS_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::COMMUNITIES_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::REPORTS_VIEW->value]);

        // Check legacy permissions still exist
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::TERRITORIES_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::PUBLISHERS_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::FORMATION_VIEW->value]);
    }

    public function test_permission_seeder_assigns_correct_default_permissions_to_roles(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        // DIRECTOR should have appropriate permissions (not delete/approve)
        $directorPermissions = DB::table('role_permissions')
            ->where('role', UserRole::DIRECTOR->value)
            ->count();
        $this->assertGreaterThan(10, $directorPermissions, 'DIRECTOR should have multiple permissions');

        // GENERAL should have most permissions (full access to modules)
        $generalPermissions = DB::table('role_permissions')
            ->where('role', UserRole::GENERAL->value)
            ->count();
        $this->assertGreaterThan($directorPermissions, $generalPermissions, 'GENERAL should have more permissions than DIRECTOR');

        // MEMBER should have limited permissions (view only)
        $memberPermissions = DB::table('role_permissions')
            ->where('role', UserRole::MEMBER->value)
            ->count();
        $this->assertLessThan(10, $memberPermissions, 'MEMBER should have limited permissions');
        $this->assertGreaterThan(0, $memberPermissions, 'MEMBER should have at least some permissions');

        // SUPER_ADMIN should have no explicit permissions (uses bypass)
        $superAdminPermissions = DB::table('role_permissions')
            ->where('role', UserRole::SUPER_ADMIN->value)
            ->count();
        $this->assertEquals(0, $superAdminPermissions, 'SUPER_ADMIN should have no explicit permissions');
    }

    public function test_permission_seeder_is_idempotent(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $firstCount = Permission::count();

        // Run seeder again
        $this->seed(\Database\Seeders\PermissionSeeder::class);
        $secondCount = Permission::count();

        $this->assertEquals($firstCount, $secondCount);
    }
}
