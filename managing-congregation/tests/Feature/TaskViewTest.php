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

class TaskViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_project_tasks_board()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        $member = Member::factory()->create();
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Test Task',
            'type' => 'task',
            'status' => 'todo',
            'priority' => 'medium',
            'reporter_id' => $member->id,
        ]);

        $response = $this->actingAs($admin)->get(route('projects.show', $project));

        $response->assertStatus(200);
        $response->assertSee('Test Task');
        $response->assertSee('To Do');
    }

    public function test_can_view_create_task_page()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('projects.tasks.create', $project));

        $response->assertStatus(200);
        $response->assertSee('Create Task');
    }

    public function test_can_view_my_tasks_page()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create([
            'role' => UserRole::MEMBER,
            'community_id' => $community->id,
        ]);
        $member = Member::factory()->create([
            'email' => $user->email,
            'community_id' => $community->id,
        ]);
        
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'My Assigned Task',
            'type' => 'task',
            'status' => 'todo',
            'priority' => 'medium',
            'assignee_id' => $member->id,
            'reporter_id' => $member->id,
        ]);

        $response = $this->actingAs($user)->get(route('my-tasks.index'));

        $response->assertStatus(200);
        $response->assertSee('My Tasks');
        $response->assertSee('My Assigned Task');
    }

    public function test_can_view_edit_task_page()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        $member = Member::factory()->create();
        $task = Task::create([
            'project_id' => $project->id,
            'title' => 'Test Task',
            'type' => 'task',
            'status' => 'todo',
            'priority' => 'medium',
            'reporter_id' => $member->id,
        ]);

        $response = $this->actingAs($admin)->get(route('projects.tasks.edit', [$project, $task]));

        $response->assertStatus(200);
        $response->assertSee('Edit Task');
        $response->assertSee($task->title);
    }
    public function test_can_view_project_members_list()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        
        $member = Member::factory()->create(['community_id' => $community->id]);
        $project->members()->attach($member->id, ['role' => 'member', 'status' => 'active']);

        $response = $this->actingAs($admin)->get(route('projects.show', $project));

        $response->assertStatus(200);
        $response->assertSee($member->first_name);
        $response->assertSee('Remove'); // Verifies the form is rendered
    }
}
