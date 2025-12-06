<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\Member;
use App\Models\Community;
use App\Enums\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_invite_member_to_project()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        $member = Member::factory()->create();

        $response = $this->actingAs($admin)->post(route('projects.members.store', $project), [
            'member_id' => $member->id,
            'role' => 'member',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('project_members', [
            'project_id' => $project->id,
            'member_id' => $member->id,
            'role' => 'member',
            'status' => 'pending',
        ]);
    }

    public function test_cannot_invite_same_member_twice()
    {
        $admin = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $community = Community::factory()->create();
        $project = Project::create([
            'name' => 'Test Project',
            'community_id' => $community->id,
            'status' => 'active',
        ]);
        $member = Member::factory()->create();

        $project->members()->attach($member->id, ['role' => 'member']);

        $response = $this->actingAs($admin)->post(route('projects.members.store', $project), [
            'member_id' => $member->id,
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('member_id');
    }
}
