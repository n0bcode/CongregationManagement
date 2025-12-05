<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_director_can_add_skill()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($director)->post(route('members.skills.store', $member), [
            'category' => 'special',
            'name' => 'Piano',
            'proficiency' => 'expert',
            'notes' => 'Can play classical music',
        ]);

        $response->assertRedirect(route('members.show', $member));
        $this->assertDatabaseHas('skills', [
            'member_id' => $member->id,
            'name' => 'Piano',
        ]);
    }

    public function test_director_cannot_add_skill_to_other_community_member()
    {
        $community1 = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community1->id]);

        $community2 = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community2->id]);

        $response = $this->actingAs($director)->post(route('members.skills.store', $member), [
            'category' => 'special',
            'name' => 'Piano',
            'proficiency' => 'expert',
        ]);

        $response->assertStatus(404); // Scoped
    }

    public function test_director_can_delete_skill()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);
        
        $skill = $member->skills()->create([
            'category' => 'special',
            'name' => 'Guitar',
            'proficiency' => 'intermediate',
        ]);

        $response = $this->actingAs($director)->delete(route('skills.destroy', $skill));

        $response->assertRedirect(route('members.show', $member));
        $this->assertDatabaseMissing('skills', ['id' => $skill->id]);
    }
}
