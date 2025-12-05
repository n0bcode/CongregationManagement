<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessibilityPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 48: Images Have Alt Text
     * Validates: Requirements 15.2
     */
    public function test_member_profile_images_have_alt_text()
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->director($community)->create();

        // Seed permission for the test
        $permissionId = \Illuminate\Support\Facades\DB::table('permissions')->insertGetId([
            'key' => 'members.view',
            'name' => 'View Members',
            'module' => 'members',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
            'role' => \App\Enums\UserRole::DIRECTOR->value,
            'permission_id' => $permissionId,
        ]);

        $member = Member::factory()->create(['community_id' => $community->id]);

        // 1. Check Member Profile Page
        $response = $this->actingAs($user)->get(route('members.show', $member));
        $response->assertStatus(200);

        // Assert that the profile image has an alt attribute
        $response->assertSee('alt="'.e($member->full_name).'"', false);
    }

    public function test_application_logo_has_alt_text()
    {
        // 2. Check Application Logo (in login page)
        $response = $this->get(route('login'));
        $response->assertStatus(200);

        // Assert logo has aria-label (since it's SVG)
        $response->assertSee('aria-label="Congregation Management System Logo"', false);
    }

    /**
     * Property 49: Forms Have ARIA Attributes
     * Validates: Requirements 15.1
     */
    public function test_forms_have_aria_attributes()
    {
        // Render the login view with errors manually using the global view helper
        // We use View::share to ensure errors are available to anonymous components
        $errors = new \Illuminate\Support\ViewErrorBag;
        $bag = new \Illuminate\Support\MessageBag(['email' => 'Invalid email']);
        $errors->put('default', $bag);
        \Illuminate\Support\Facades\View::share('errors', $errors);

        $view = view('auth.login');
        $rendered = $view->render();

        // Check for aria-invalid="true" on email input
        $this->assertStringContainsString('aria-invalid="true"', $rendered);

        // Check for aria-describedby="email-error"
        $this->assertStringContainsString('aria-describedby="email-error"', $rendered);

        // Check for id="email-error" on the error message
        $this->assertStringContainsString('id="email-error"', $rendered);
    }
}
