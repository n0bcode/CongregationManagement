<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_backup_page()
    {
        $admin = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.backups.index'));

        $response->assertStatus(200);
        $response->assertViewIs('settings.backups');
    }

    public function test_admin_can_create_backup()
    {
        Storage::fake('local');
        
        $admin = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        // We can't easily test the actual DB dump in this environment without mysqldump
        // But we can test the controller logic and redirect.
        // The controller uses Spatie\DbDumper which might throw exception if binary not found.
        // We expect a redirect back, either with success or error.
        
        $response = $this->actingAs($admin)->post(route('admin.backups.create'));

        $response->assertRedirect();
        
        // Assert that a file was created in the backups directory
        $files = Storage::disk('local')->files('backups');
        $this->assertNotEmpty($files, 'No backup file was created.');
        
        $this->assertTrue(session()->has('success') || session()->has('error'));
    }

    public function test_admin_can_download_backup()
    {
        Storage::fake('local');
        $filename = 'backup-test.sql';
        Storage::disk('local')->put('backups/' . $filename, 'content');
        
        $admin = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get(route('admin.backups.download', $filename));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=' . $filename);
    }

    public function test_non_admin_cannot_access_backup_page()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::MEMBER]);

        $response = $this->actingAs($user)->get(route('admin.backups.index'));

        $response->assertStatus(403);
    }
}
