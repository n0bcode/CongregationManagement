<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TimelineTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_project_timeline()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => \App\Models\Community::factory()->create()->id,
            'status' => 'active',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);
        
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Timeline Task',
            'type' => 'story',
            'status' => 'todo',
            'priority' => 'medium',
            'start_date' => now()->addDay(),
            'due_date' => now()->addDays(5),
            'reporter_id' => \App\Models\Member::factory()->create()->id,
        ]);

        $response = $this->actingAs($admin)->get(route('projects.tasks.timeline', $project));

        $response->assertStatus(200);
        $response->assertSee('Project Timeline');
        $response->assertSee('Timeline Task');
    }
}
