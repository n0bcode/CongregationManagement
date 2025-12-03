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

        $this->assertDatabaseCount('permissions', 7);

        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::TERRITORIES_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::TERRITORIES_ASSIGN->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::TERRITORIES_MANAGE->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::PUBLISHERS_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::PUBLISHERS_MANAGE->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::REPORTS_VIEW->value]);
        $this->assertDatabaseHas('permissions', ['key' => PermissionKey::REPORTS_EXPORT->value]);
    }

    public function test_permission_seeder_assigns_correct_default_permissions_to_roles(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        // DIRECTOR should have 4 permissions
        $directorPermissions = DB::table('role_permissions')
            ->where('role', UserRole::DIRECTOR->value)
            ->count();
        $this->assertEquals(4, $directorPermissions);

        // GENERAL should have all 7 permissions
        $generalPermissions = DB::table('role_permissions')
            ->where('role', UserRole::GENERAL->value)
            ->count();
        $this->assertEquals(7, $generalPermissions);

        // MEMBER should have 1 permission
        $memberPermissions = DB::table('role_permissions')
            ->where('role', UserRole::MEMBER->value)
            ->count();
        $this->assertEquals(1, $memberPermissions);
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
