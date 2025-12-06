<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Member;
use App\Models\Community;
use App\Models\Task;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_task()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        $member = Member::factory()->create();

        $response = $this->actingAs($admin)->post(route('projects.tasks.store', $project), [
            'title' => 'Test Task',
            'description' => 'Description',
            'type' => 'story',
            'status' => 'todo',
            'priority' => 'medium',
            'assignee_id' => $member->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Test Task',
            'type' => 'story',
            'assignee_id' => $member->id,
        ]);
    }

    public function test_task_hierarchy()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        $member = Member::factory()->create();

        $epic = Task::create([
            'project_id' => $project->id,
            'title' => 'Epic Task',
            'type' => 'epic',
            'reporter_id' => $member->id,
        ]);

        $response = $this->actingAs($admin)->post(route('projects.tasks.store', $project), [
            'title' => 'Child Story',
            'type' => 'story',
            'status' => 'todo',
            'priority' => 'medium',
            'parent_id' => $epic->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Child Story',
            'parent_id' => $epic->id,
        ]);
    }
}
