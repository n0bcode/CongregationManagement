<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class ErrorHandlingPropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 21: Error Messages Are User-Friendly
     * Validates: Requirements 8.2
     */
    public function test_error_messages_are_user_friendly()
    {
        $user = User::factory()->create();
        $user = User::factory()->create();
        
        // Seed permission for the test
        $permissionId = \Illuminate\Support\Facades\DB::table('permissions')->insertGetId([
            'key' => 'members.create', 
            'name' => 'Create Members', 
            'module' => 'members',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
            'role' => \App\Enums\UserRole::MEMBER->value, // Assuming default factory creates MEMBER
            'permission_id' => $permissionId,
        ]);

        // Test Member Creation Validation
        $response = $this->actingAs($user)->post(route('members.store'), [
            // Missing required fields
        ]);

        $response->assertSessionHasErrors(['first_name', 'last_name', 'dob', 'entry_date']);
        
        // We can't easily check the exact message in session errors without specific keys,
        // but we can check if the custom message is defined in the request.
        $request = new \App\Http\Requests\StoreMemberRequest();
        $messages = $request->messages();
        
        $this->assertArrayHasKey('first_name.unique', $messages);
        $this->assertEquals('A member with this name and date of birth already exists.', $messages['first_name.unique']);
        
        $this->assertArrayHasKey('dob.before', $messages);
        $this->assertEquals('The date of birth must be in the past.', $messages['dob.before']);
    }

    public function test_assignment_error_messages_are_user_friendly()
    {
        $request = new \App\Http\Requests\StoreAssignmentRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('end_date.after_or_equal', $messages);
        $this->assertEquals('The end date must be after or equal to the start date.', $messages['end_date.after_or_equal']);
    }

    public function test_success_flash_message_is_displayed()
    {
        $user = User::factory()->create();
        
        // Define a route that flashes a message
        \Illuminate\Support\Facades\Route::get('/flash-success', function () {
            session()->flash('success', 'Operation successful');
            return \Illuminate\Support\Facades\Blade::render('<x-flash-message />');
        });

        $response = $this->actingAs($user)->get('/flash-success');
        $response->assertSeeText('Operation successful');
    }

    public function test_error_flash_message_is_displayed()
    {
        $user = User::factory()->create();
        
        // Define a route that flashes an error
        \Illuminate\Support\Facades\Route::get('/flash-error', function () {
            session()->flash('error', 'Operation failed');
            return \Illuminate\Support\Facades\Blade::render('<x-flash-message />');
        });

        $response = $this->actingAs($user)->get('/flash-error');
        $response->assertSeeText('Operation failed');
    }

    public function test_error_pages_render_correctly()
    {
        // 404 Page
        $response = $this->get('/non-existent-page');
        $response->assertStatus(404);
        $response->assertSee('404');
        $response->assertSee('Page Not Found');

        // 403 Page (Simulate by aborting)
        \Illuminate\Support\Facades\Route::get('/force-403', function () {
            abort(403);
        });
        
        $response = $this->get('/force-403');
        $response->assertStatus(403);
        $response->assertSee('403');
        $response->assertSee('Access Forbidden');
        
        // 500 Page (Simulate by aborting)
        \Illuminate\Support\Facades\Route::get('/force-500', function () {
            abort(500);
        });

        $response = $this->get('/force-500');
        $response->assertStatus(500);
        $response->assertSee('500');
        $response->assertSee('Server Error');
    }
}
