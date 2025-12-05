<?php

namespace Tests\Feature;

use App\Enums\MemberStatus;
use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    public function test_director_can_view_edit_page()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($director)->get(route('members.edit', $member));

        $response->assertStatus(200);
        $response->assertViewIs('members.edit');
        $response->assertViewHas('member', $member);
    }

    public function test_director_can_update_member()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id, 'status' => MemberStatus::Active]);

        $response = $this->actingAs($director)->put(route('members.update', $member), [
            'first_name' => 'Updated Name',
            'last_name' => 'Updated Last',
            'dob' => '1990-01-01',
            'entry_date' => '2010-01-01',
            'status' => MemberStatus::Exited->value,
        ]);

        $response->assertRedirect(route('members.show', $member));
        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'first_name' => 'Updated Name',
            'status' => MemberStatus::Exited->value,
        ]);
    }

    public function test_director_cannot_edit_member_from_another_community()
    {
        $community1 = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community1->id]);

        $community2 = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community2->id]);

        $response = $this->actingAs($director)->get(route('members.edit', $member));
        $response->assertStatus(404); // Or 403 depending on scope implementation
    }

    public function test_director_cannot_update_member_from_another_community()
    {
        $community1 = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community1->id]);

        $community2 = Community::factory()->create();
        $member = Member::factory()->create(['community_id' => $community2->id]);

        $response = $this->actingAs($director)->put(route('members.update', $member), [
            'first_name' => 'Hacked',
        ]);

        $response->assertStatus(404); // Or 403
    }

    public function test_update_validation()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($director)->put(route('members.update', $member), [
            'first_name' => '', // Required
            'status' => 'invalid-status', // Enum validation
        ]);

        $response->assertSessionHasErrors(['first_name', 'status']);
    }

    public function test_regular_member_cannot_update_member()
    {
        $community = Community::factory()->create();
        $regularMember = User::factory()->create(['role' => UserRole::MEMBER, 'community_id' => $community->id]);
        $memberToEdit = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($regularMember)->put(route('members.update', $memberToEdit), [
            'first_name' => 'Hacked',
            'last_name' => 'Hacked',
            'dob' => '1990-01-01',
            'entry_date' => '2010-01-01',
            'status' => MemberStatus::Active->value,
        ]);

        $response->assertStatus(403);
        $response->assertStatus(403);
    }

    public function test_edit_button_visibility()
    {
        $community = Community::factory()->create();
        $director = User::factory()->create(['role' => UserRole::DIRECTOR, 'community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $regularMember = User::factory()->create(['role' => UserRole::MEMBER, 'community_id' => $community->id]);

        // Director sees Edit button
        $response = $this->actingAs($director)->get(route('members.show', $member));
        $response->assertSee('Edit');
        $response->assertSee(route('members.edit', $member));

        // Regular Member does NOT see Edit button
        $response = $this->actingAs($regularMember)->get(route('members.show', $member));
        $response->assertDontSee(route('members.edit', $member));
    }
}
