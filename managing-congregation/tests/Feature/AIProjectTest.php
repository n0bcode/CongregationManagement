<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AIProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_ai_wizard()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->get(route('projects.ai.create'));

        $response->assertStatus(200);
        $response->assertSee('AI Project Wizard');
    }

    public function test_can_generate_project_structure()
    {
        // Mock config to provide API key
        config(['services.gemini.key' => 'fake-test-key']);
        
        // Mock Gemini API response
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [[
                    'content' => [
                        'parts' => [[
                            'text' => json_encode([
                                'project_name' => 'AI Generated App',
                                'description' => 'Mobile fitness tracking application',
                                'tasks' => [
                                    ['title' => 'Design UI/UX', 'type' => 'epic', 'priority' => 'high'],
                                    ['title' => 'Setup Backend', 'type' => 'task', 'priority' => 'high'],
                                ]
                            ])
                        ]]
                    ]
                ]]
            ], 200)
        ]);
        
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);

        $response = $this->actingAs($admin)->post(route('projects.ai.generate'), [
            'project_name' => 'AI Generated App',
            'description' => 'A mobile app for tracking fitness goals.',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Review Generated Structure');
        $response->assertSee('Design UI/UX'); // Keyword from mock service
    }

    public function test_can_store_generated_project()
    {
        $community = \App\Models\Community::factory()->create();
        $admin = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
            'community_id' => $community->id,
        ]);
        
        // Ensure user has a member profile for manager assignment
        \App\Models\Member::factory()->create([
            'email' => $admin->email,
            'community_id' => $community->id,
        ]);

        $tasks = [
            ['title' => 'Task 1', 'type' => 'epic', 'priority' => 'high'],
            ['title' => 'Task 2', 'type' => 'story', 'priority' => 'medium'],
        ];

        $response = $this->actingAs($admin)->post(route('projects.ai.store'), [
            'project_name' => 'Final AI Project',
            'description' => 'Description',
            'tasks' => $tasks,
        ]);

        $project = Project::where('name', 'Final AI Project')->first();
        
        $response->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', ['name' => 'Final AI Project']);
        $this->assertDatabaseHas('tasks', ['title' => 'Task 1', 'project_id' => $project->id]);
    }
}
