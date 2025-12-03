<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberTransferTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_transfer_member_to_new_community()
    {
        $oldCommunity = Community::factory()->create(['name' => 'Old House']);
        $user = User::factory()->create(['community_id' => $oldCommunity->id]);
        $newCommunity = Community::factory()->create(['name' => 'New House']);
        
        $member = Member::factory()->create([
            'community_id' => $oldCommunity->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        // Create an initial assignment for the old community
        Assignment::create([
            'member_id' => $member->id,
            'community_id' => $oldCommunity->id,
            'start_date' => now()->subYear(),
        ]);

        $response = $this->actingAs($user)
            ->post(route('members.transfer', $member), [
                'community_id' => $newCommunity->id,
                'transfer_date' => now()->format('Y-m-d'),
            ]);

        $response->assertRedirect(route('members.show', $member));
        $response->assertSessionHas('success');

        // Verify Member updated
        $this->assertEquals($newCommunity->id, $member->fresh()->community_id);

        // Verify Old Assignment Closed
        $oldAssignment = Assignment::where('member_id', $member->id)
            ->where('community_id', $oldCommunity->id)
            ->first();
        $this->assertNotNull($oldAssignment->end_date);
        $this->assertEquals(now()->format('Y-m-d'), $oldAssignment->end_date->format('Y-m-d'));

        // Verify New Assignment Created
        $newAssignment = Assignment::where('member_id', $member->id)
            ->where('community_id', $newCommunity->id)
            ->first();
        $this->assertNotNull($newAssignment);
        $this->assertEquals(now()->format('Y-m-d'), $newAssignment->start_date->format('Y-m-d'));
        $this->assertNull($newAssignment->end_date);
    }

    public function test_transfer_validation()
    {
        $community = Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->post(route('members.transfer', $member), [
                'community_id' => 999, // Non-existent
                'transfer_date' => 'invalid-date',
            ]);

        $response->assertSessionHasErrors(['community_id', 'transfer_date']);
    }
}
