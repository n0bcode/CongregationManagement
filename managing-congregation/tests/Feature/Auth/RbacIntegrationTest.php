<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class RbacIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_complete_rbac_flow_works_end_to_end(): void
    {
        // Create a director user
        $director = User::factory()->create(['role' => UserRole::DIRECTOR]);

        // Verify role
        $this->assertTrue($director->hasRole(UserRole::DIRECTOR));

        // Verify permissions
        $this->assertTrue($director->hasPermission(PermissionKey::TERRITORIES_VIEW));
        $this->assertFalse($director->hasPermission(PermissionKey::REPORTS_EXPORT));

        // Verify super admin bypass doesn't apply
        $this->assertFalse($director->isSuperAdmin());
    }

    public function test_changing_user_role_updates_permissions(): void
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);

        // Member has limited permissions
        $this->assertFalse($user->hasPermission(PermissionKey::PUBLISHERS_MANAGE));

        // Change to DIRECTOR
        $user->role = UserRole::DIRECTOR;
        $user->save();
        $user->refresh();

        // Now has director permissions
        $this->assertTrue($user->hasPermission(PermissionKey::PUBLISHERS_MANAGE));
    }

    public function test_gate_integration_works_with_permissions(): void
    {
        $superAdmin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $general = User::factory()->create(['role' => UserRole::GENERAL]);
        $director = User::factory()->create(['role' => UserRole::DIRECTOR]);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('view-admin'));
        $this->assertTrue(Gate::forUser($general)->allows('view-admin'));
        $this->assertFalse(Gate::forUser($director)->allows('view-admin'));
    }
}
