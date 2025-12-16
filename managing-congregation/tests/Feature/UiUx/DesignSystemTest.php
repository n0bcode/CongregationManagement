<?php

namespace Tests\Feature\UiUx;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DesignSystemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Members Create page renders with the correct Design System components.
     */
    public function test_members_create_page_uses_design_system_components(): void
    {
        // 1. Arrange: Create a user and acting as them
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        // 2. Act: Visit the page
        $response = $this->actingAs($user)->get(route('members.create'));

        // 3. Assert: Check for success
        $response->assertStatus(200);

        // 4. Assert: Check for key Design System classes/elements
        
        // Button Styles (from x-ui.button / app.css)
        // Note: x-ui.button renders as 'inline-flex items-center...' depending on variant
        $response->assertSee('inline-flex items-center'); 
        
        // We refactored the "Back" button to be a ghost variant
        // It should have some specific utility classes or the hover state
        $response->assertSee('hover:bg-stone-100'); 

        // Alert Styles (from x-ui.alert)
        // The help text uses 'bg-blue-50' (variant=info)
        $response->assertSee('bg-blue-50');
        $response->assertSee('border-blue-200');
        $response->assertSee('text-blue-800');
        
        // We know the Livewire component is rendered inside
        $response->assertSeeLivewire('members.create-member');
    }

    /**
     * Test that the Livewire component renders standardized form inputs.
     */
    public function test_create_member_form_renders_standard_inputs(): void
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);

        // Use Livewire test helper
        \Livewire\Livewire::actingAs($user)
            ->test(\App\Livewire\Members\CreateMember::class)
            ->assertStatus(200)
            // Check for the standardized input classes (form-input)
            // These might be obscured if x-ui.input merges defaults, but 'form-input' class is key
            ->assertSeeHtml('form-input')
            ->assertSeeHtml('form-label')
            
            // Check for the new Select component usage (Member Type)
            ->assertSeeHtml('form-select')
            
            // Check for the new Alert usage in conditional fields
            ->assertSeeHtml('bg-amber-50') // Warning variant for Formation Dates
            ->assertSeeHtml('text-amber-800');
    }
}
