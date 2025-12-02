<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\Permission;
use App\Services\PermissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PermissionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PermissionService::class);
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_assign_permissions_to_role_removes_old_permissions(): void
    {
        // Assign initial permissions
        $this->service->assignPermissionsToRole(UserRole::DIRECTOR, [
            PermissionKey::TERRITORIES_VIEW->value,
        ]);

        $this->assertEquals(1, DB::table('role_permissions')
            ->where('role', UserRole::DIRECTOR->value)
            ->count());

        // Assign new permissions (should replace old ones)
        $this->service->assignPermissionsToRole(UserRole::DIRECTOR, [
            PermissionKey::PUBLISHERS_VIEW->value,
            PermissionKey::REPORTS_VIEW->value,
        ]);

        $this->assertEquals(2, DB::table('role_permissions')
            ->where('role', UserRole::DIRECTOR->value)
            ->count());

        $this->assertDatabaseMissing('role_permissions', [
            'role' => UserRole::DIRECTOR->value,
            'permission_id' => Permission::where('key', PermissionKey::TERRITORIES_VIEW->value)->first()->id,
        ]);
    }

    public function test_assign_permissions_to_role_handles_empty_array(): void
    {
        $this->service->assignPermissionsToRole(UserRole::DIRECTOR, []);

        $this->assertEquals(0, DB::table('role_permissions')
            ->where('role', UserRole::DIRECTOR->value)
            ->count());
    }

    public function test_assign_permissions_to_role_ignores_invalid_permission_keys(): void
    {
        $this->service->assignPermissionsToRole(UserRole::DIRECTOR, [
            PermissionKey::TERRITORIES_VIEW->value,
            'invalid.permission',
        ]);

        // Should only assign the valid permission
        $this->assertEquals(1, DB::table('role_permissions')
            ->where('role', UserRole::DIRECTOR->value)
            ->count());
    }
}
