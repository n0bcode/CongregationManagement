<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\AuditLog;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_recent_activity()
    {
        $user = User::factory()->create();
        
        // Create some audit logs
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'created',
            'auditable_type' => Member::class,
            'auditable_id' => 1,
            'target_type' => 'member',
            'target_id' => 1,
            'description' => 'Test Member Created',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Recent Activity');
        $response->assertSee('Test Member Created');
    }

    public function test_dashboard_displays_no_activity_message_when_empty()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('No recent activity found');
    }
}
