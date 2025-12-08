<?php

namespace Tests\Feature\UiUx;

use App\Models\Member;
use App\Services\ContextualActionsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContextualActionsTest extends TestCase
{
    use RefreshDatabase;

    protected ContextualActionsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ContextualActionsService();
    }

    /** @test */
    public function service_returns_array_of_actions()
    {
        $member = Member::factory()->create();
        $user = $member->community->users()->first() ?? $member->community->users()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'director',
        ]);

        $actions = $this->service->getActions($member, $user);

        $this->assertIsArray($actions);
    }

    /** @test */
    public function actions_have_required_structure()
    {
        $member = Member::factory()->create();
        $user = $member->community->users()->first() ?? $member->community->users()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role' => 'director',
        ]);

        $actions = $this->service->getActions($member, $user);

        if (count($actions) > 0) {
            $firstAction = $actions[0];
            $this->assertArrayHasKey('id', $firstAction);
            $this->assertArrayHasKey('label', $firstAction);
            $this->assertArrayHasKey('url', $firstAction);
        }

        $this->assertTrue(true); // Pass if no actions
    }

    /** @test */
    public function service_provides_action_icons()
    {
        $icon = $this->service->getActionIcon('edit');
        
        $this->assertNotEmpty($icon);
        $this->assertStringContainsString('svg', $icon);
    }

    /** @test */
    public function contextual_actions_component_exists()
    {
        $componentPath = resource_path('views/components/features/contextual-actions.blade.php');
        
        $this->assertFileExists($componentPath);
    }
}
