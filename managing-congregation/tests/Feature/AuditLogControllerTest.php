<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_super_admin_can_view_audit_logs()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'test',
            'auditable_type' => User::class,
            'auditable_id' => $admin->id,
            'description' => 'Test Log',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
        ]);

        $response = $this->actingAs($admin)->get(route('audit-logs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('audit-logs.index');
    }

    public function test_director_cannot_view_audit_logs()
    {
        $director = User::factory()->create(['role' => UserRole::DIRECTOR]);

        $response = $this->actingAs($director)->get(route('audit-logs.index'));

        $response->assertStatus(403);
    }

    public function test_can_filter_audit_logs()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        AuditLog::create([
            'user_id' => $admin->id,
            'action' => 'login',
            'auditable_type' => User::class,
            'auditable_id' => $admin->id,
            'description' => 'Login Log',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Test Agent',
        ]);

        $response = $this->actingAs($admin)->get(route('audit-logs.index', ['action' => 'login']));

        $response->assertStatus(200);
        $response->assertSee('Login Log');
    }
}
