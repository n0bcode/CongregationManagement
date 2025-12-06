<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_can_see_projects_link()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee(route('projects.index'));
        $response->assertDontSee(route('admin.settings.index'));
        $response->assertDontSee(route('admin.backups.index'));
    }

    public function test_admin_can_see_admin_links()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee(route('projects.index'));
        $response->assertSee(route('admin.settings.index'));
        $response->assertSee(route('admin.backups.index'));
    }
}
