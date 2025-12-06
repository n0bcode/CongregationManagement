<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Member;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_projects()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => Community::factory()->create()->id,
            'status' => 'planned',
            'budget' => 1000,
        ]);

        $response = $this->actingAs($user)->get(route('projects.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Project');
    }

    public function test_user_can_create_project()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        $response = $this->actingAs($user)->post(route('projects.store'), [
            'name' => 'New Project',
            'community_id' => $community->id,
            'status' => 'planned',
            'budget' => 5000,
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }

    public function test_user_can_update_project()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Old Project',
            'community_id' => Community::factory()->create()->id,
            'status' => 'planned',
            'budget' => 1000,
        ]);

        $response = $this->actingAs($user)->put(route('projects.update', $project), [
            'name' => 'Updated Project',
            'community_id' => $project->community_id,
            'status' => 'active',
            'budget' => 2000,
        ]);

        $response->assertRedirect(route('projects.index'));
        $this->assertDatabaseHas('projects', ['name' => 'Updated Project', 'status' => 'active']);
    }

    public function test_user_can_delete_project()
    {
        $user = User::factory()->create();
        $project = Project::create([
            'name' => 'Delete Me',
            'community_id' => Community::factory()->create()->id,
            'status' => 'planned',
            'budget' => 1000,
        ]);

        $response = $this->actingAs($user)->delete(route('projects.destroy', $project));

        $response->assertRedirect(route('projects.index'));
        $this->assertSoftDeleted($project);
    }
}
