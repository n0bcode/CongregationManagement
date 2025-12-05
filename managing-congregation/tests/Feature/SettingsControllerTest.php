<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_super_admin_can_view_settings()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('settings.index');
    }

    public function test_super_admin_can_update_settings()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'settings' => [
                'service_year_start_month' => ['key' => 'service_year_start_month', 'value' => 9],
                'reminder_period_days' => ['key' => 'reminder_period_days', 'value' => 45],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_director_cannot_view_settings()
    {
        $director = User::factory()->create(['role' => UserRole::DIRECTOR]);

        $response = $this->actingAs($director)->get(route('admin.settings.index'));

        $response->assertStatus(403);
    }
}
