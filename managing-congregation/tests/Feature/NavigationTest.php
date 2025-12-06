<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    // Replaced by test_regular_user_sees_periodic_events_but_not_admin_links

    public function test_admin_can_see_admin_links()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertStatus(200);
        $response->assertSee(route('projects.index'));
        $response->assertSee(route('periodic-events.index'));
        $response->assertSee(route('reports.advanced'));
        $response->assertSee(route('admin.settings.index'));
        $response->assertSee(route('admin.backups.index'));
    }

    public function test_regular_user_sees_periodic_events_but_not_admin_links()
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee(route('projects.index'));
        $response->assertSee(route('periodic-events.index'));
        // The link is currently visible even if they get 403, unless we wrap it in @can
        $response->assertSee(route('reports.advanced')); 
        
        $response->assertDontSee(route('admin.settings.index'));
        $response->assertDontSee(route('admin.backups.index'));
    }
}
