<?php

namespace Tests\Feature;

use App\Enums\PermissionKey;
use App\Enums\UserRole;
use App\Models\Permission;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FooterSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary permissions
        Permission::create([
            'key' => PermissionKey::SETTINGS_MANAGE->value,
            'name' => 'Manage System Settings',
            'module' => 'settings',
        ]);

        // Seed default footer settings
        $this->artisan('db:seed', ['--class' => 'FooterSettingsSeeder']);
    }

    /** @test */
    public function unauthorized_users_cannot_access_footer_settings()
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);

        $response = $this->actingAs($user)->get(route('admin.settings.footer.edit'));

        $response->assertStatus(403);
    }

    /** @test */
    public function director_without_permission_cannot_access_footer_settings()
    {
        $user = User::factory()->create(['role' => UserRole::DIRECTOR]);

        $response = $this->actingAs($user)->get(route('admin.settings.footer.edit'));

        $response->assertStatus(403);
    }

    /** @test */
    public function super_admin_can_view_footer_settings_page()
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($user)->get(route('admin.settings.footer.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('settings.footer');
        $response->assertViewHas('footerSettings');
    }

    /** @test */
    public function general_user_with_permission_can_view_footer_settings()
    {
        $user = User::factory()->create(['role' => UserRole::GENERAL]);
        
        // Assign SETTINGS_MANAGE permission to GENERAL role
        $user->role->permissions()->attach(
            Permission::where('key', PermissionKey::SETTINGS_MANAGE->value)->first()
        );

        $response = $this->actingAs($user)->get(route('admin.settings.footer.edit'));

        $response->assertStatus(200);
    }

    /** @test */
    public function super_admin_can_update_footer_settings()
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $data = [
            'footer_description' => 'Updated description for testing',
            'footer_address' => '456 Test Street, Test City',
            'footer_email' => 'test@example.com',
            'footer_copyright' => '&copy; 2025 Test Organization',
        ];

        $response = $this->actingAs($user)->put(route('admin.settings.footer.update'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('Updated description for testing', SystemSetting::get('footer_description'));
        $this->assertEquals('456 Test Street, Test City', SystemSetting::get('footer_address'));
        $this->assertEquals('test@example.com', SystemSetting::get('footer_email'));
        $this->assertEquals('&copy; 2025 Test Organization', SystemSetting::get('footer_copyright'));
    }

    /** @test */
    public function footer_validation_requires_all_fields()
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($user)->put(route('admin.settings.footer.update'), []);

        $response->assertSessionHasErrors(['footer_description', 'footer_address', 'footer_email', 'footer_copyright']);
    }

    /** @test */
    public function footer_email_must_be_valid()
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $data = [
            'footer_description' => 'Test description',
            'footer_address' => 'Test address',
            'footer_email' => 'invalid-email',
            'footer_copyright' => 'Test copyright',
        ];

        $response = $this->actingAs($user)->put(route('admin.settings.footer.update'), $data);

        $response->assertSessionHasErrors('footer_email');
    }

    /** @test */
    public function footer_description_has_max_length()
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $data = [
            'footer_description' => str_repeat('a', 501), // Over 500 chars
            'footer_address' => 'Test address',
            'footer_email' => 'test@example.com',
            'footer_copyright' => 'Test copyright',
        ];

        $response = $this->actingAs($user)->put(route('admin.settings.footer.update'), $data);

        $response->assertSessionHasErrors('footer_description');
    }

    /** @test */
    public function updated_footer_content_appears_on_application_layout()
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        // Update footer settings
        SystemSetting::set('footer_description', 'Custom footer description');
        SystemSetting::set('footer_address', '789 Custom Ave');
        SystemSetting::set('footer_email', 'custom@test.org');
        SystemSetting::set('footer_copyright', '&copy; 2025 Custom Org');

        // Visit dashboard (which uses app layout)
        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertSee('Custom footer description', false);
        $response->assertSee('789 Custom Ave', false);
        $response->assertSee('custom@test.org', false);
        $response->assertSee('&copy; 2025 Custom Org', false);
    }

    /** @test */
    public function default_footer_values_are_used_when_settings_not_set()
    {
        // Clear all footer settings
        SystemSetting::where('group', 'footer')->delete();

        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $response = $this->actingAs($user)->get(route('dashboard'));

        // Should show default values
        $response->assertSee('Supporting our community with grace and efficiency', false);
        $response->assertSee('123 Congregation Ave, City, Country', false);
        $response->assertSee('contact@congregation.org', false);
    }
}
