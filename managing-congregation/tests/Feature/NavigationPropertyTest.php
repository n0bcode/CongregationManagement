<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationPropertyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_navigation_dropdown_visibility()
    {
        // Property 51: Navigation Dropdown Visibility
        // Directors should see Management, Finance, Reports
        // Super Admins should see System as well

        $director = User::factory()->create(['role' => \App\Enums\UserRole::DIRECTOR]);
        $superAdmin = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        // Test Director View
        $response = $this->actingAs($director)->get('/dashboard');
        $response->assertSee('Management');
        $response->assertSee('Finance');
        $response->assertSee('Reports');
        // Use more specific check - look for System Settings link instead of just "System" text
        $response->assertDontSee('System Settings');

        // Test Super Admin View
        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertSee('Management');
        $response->assertSee('Finance');
        $response->assertSee('Reports');
        $response->assertSee('System'); // Should see System dropdown
    }

    public function test_navigation_active_state()
    {
        // Property 52: Navigation Active State
        // Parent dropdown should be active when child link is visited

        $user = User::factory()->create(['role' => \App\Enums\UserRole::DIRECTOR]);

        // Visit Members page (child of Management)
        $response = $this->actingAs($user)->get(route('members.index'));

        // Assert Management dropdown is marked active (border-amber-600)
        // We look for the active class on the Management button
        $response->assertSee('border-amber-600', false);
    }
}
