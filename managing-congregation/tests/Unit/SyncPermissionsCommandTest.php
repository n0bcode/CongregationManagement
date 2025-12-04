<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SyncPermissionsCommandTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_sync_command_creates_new_permissions(): void
    {
        // Delete all permissions to start fresh
        Permission::query()->delete();

        $this->artisan('permissions:sync', ['--force' => true])
            ->assertSuccessful();

        // Should have created the view-admin permission from routes
        $this->assertDatabaseHas('permissions', [
            'key' => 'view-admin',
            'is_active' => true,
        ]);
    }

    public function test_sync_command_marks_orphaned_permissions_as_inactive(): void
    {
        // Create a permission that doesn't exist in routes
        Permission::create([
            'key' => 'fake.permission',
            'name' => 'Fake Permission',
            'module' => 'fake',
            'is_active' => true,
        ]);

        $this->artisan('permissions:sync', ['--force' => true])
            ->assertSuccessful();

        // Should mark the fake permission as inactive
        $this->assertDatabaseHas('permissions', [
            'key' => 'fake.permission',
            'is_active' => false,
        ]);
    }

    public function test_sync_command_reactivates_previously_inactive_permissions(): void
    {
        // Create view-admin as inactive
        Permission::create([
            'key' => 'view-admin',
            'name' => 'View Admin',
            'module' => 'admin',
            'is_active' => false,
        ]);

        $this->artisan('permissions:sync', ['--force' => true])
            ->assertSuccessful();

        // Should reactivate the permission
        $this->assertDatabaseHas('permissions', [
            'key' => 'view-admin',
            'is_active' => true,
        ]);
    }

    public function test_sync_command_is_idempotent(): void
    {
        // Run sync twice
        $this->artisan('permissions:sync', ['--force' => true])
            ->assertSuccessful();

        $firstCount = Permission::count();

        $this->artisan('permissions:sync', ['--force' => true])
            ->assertSuccessful();

        $secondCount = Permission::count();

        // Count should remain the same
        $this->assertEquals($firstCount, $secondCount);
    }

    public function test_sync_command_dry_run_does_not_make_changes(): void
    {
        $initialCount = Permission::count();

        $this->artisan('permissions:sync', ['--dry-run' => true])
            ->assertSuccessful();

        $finalCount = Permission::count();

        // Count should remain the same
        $this->assertEquals($initialCount, $finalCount);
    }

    public function test_sync_command_updates_permission_metadata(): void
    {
        // Create view-admin with wrong metadata
        Permission::create([
            'key' => 'view-admin',
            'name' => 'Wrong Name',
            'module' => 'wrong',
            'is_active' => true,
        ]);

        $this->artisan('permissions:sync', ['--force' => true])
            ->assertSuccessful();

        // Should update the metadata
        $permission = Permission::where('key', 'view-admin')->first();
        $this->assertEquals('View Admin', $permission->name);
        $this->assertEquals('admin', $permission->module);
    }
}
