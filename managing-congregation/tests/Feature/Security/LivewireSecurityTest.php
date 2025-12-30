<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Models\Member;
use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LivewireSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    /**
     * Test that MembersTable updateMember validates field names.
     *
     * @test
     */
    public function it_validates_field_names_in_update_member()
    {
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPER_ADMIN,
        ]);
        
        $community = Community::factory()->create();
        $member = Member::factory()->create([
            'community_id' => $community->id,
            'first_name' => 'John',
        ]);

        $this->actingAs($user);

        // Test allowed field update
        \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->call('updateMember', $member->id, 'first_name', 'Jane');

        $member->refresh();
        $this->assertEquals('Jane', $member->first_name);

        // Test disallowed field update (should be rejected - community_id should not change)
        $originalCommunityId = $member->community_id;
        
        \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->call('updateMember', $member->id, 'community_id', 999);

        $member->refresh();
        $this->assertEquals($originalCommunityId, $member->community_id, 
            'community_id should not be updatable via updateMember');
    }

    /**
     * Test that MembersTable sortBy validates field names.
     *
     * @test
     */
    public function it_validates_sort_field_names()
    {
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPER_ADMIN,
        ]);

        $this->actingAs($user);

        // Test allowed sort field
        $component = \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->call('sortBy', 'first_name');

        $this->assertEquals('first_name', $component->get('sortField'));

        // Test disallowed sort field (should be ignored)
        $component = \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->set('sortField', 'created_at')
            ->call('sortBy', 'malicious_field; DROP TABLE members;');

        // Sort field should remain unchanged
        $this->assertEquals('created_at', $component->get('sortField'));
    }

    /**
     * Test that MembersTable search escapes wildcards.
     *
     * @test
     */
    public function it_escapes_wildcards_in_livewire_search()
    {
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPER_ADMIN,
        ]);
        
        $community = Community::factory()->create();
        
        $member1 = Member::factory()->create([
            'community_id' => $community->id,
            'first_name' => '50% Off',
        ]);
        
        $member2 = Member::factory()->create([
            'community_id' => $community->id,
            'first_name' => '50X Off',
        ]);

        $this->actingAs($user);

        // Search for "50% Off" - should only match exact wildcard
        $component = \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->set('search', '50% Off');

        // Get the members from the rendered view data
        $members = $component->viewData('members');
        $this->assertCount(1, $members);
        $this->assertEquals($member1->id, $members->first()->id);
    }

    /**
     * Test that updateMember validates field values.
     *
     * @test
     */
    public function it_validates_field_values_in_update_member()
    {
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::SUPER_ADMIN,
        ]);
        
        $community = Community::factory()->create();
        $member = Member::factory()->create([
            'community_id' => $community->id,
            'email' => 'valid@example.com',
            'status' => \App\Enums\MemberStatus::Active,
        ]);

        $this->actingAs($user);

        // Test invalid email - should not update
        \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->call('updateMember', $member->id, 'email', 'invalid-email');

        $member->refresh();
        $this->assertEquals('valid@example.com', $member->email, 
            'Invalid email should not update');

        // Test valid email update
        \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->call('updateMember', $member->id, 'email', 'newemail@example.com');

        $member->refresh();
        $this->assertEquals('newemail@example.com', $member->email,
            'Valid email should update successfully');
    }

    /**
     * Test authorization is checked in updateMember.
     *
     * @test
     */
    public function it_checks_authorization_in_update_member()
    {
        // Create a Director user with limited permissions
        $directorCommunity = Community::factory()->create();
        $director = User::factory()->create([
            'role' => \App\Enums\UserRole::DIRECTOR,
            'community_id' => $directorCommunity->id,
        ]);

        // Create a member in the director's community
        $ownMember = Member::factory()->create([
            'community_id' => $directorCommunity->id,
            'first_name' => 'OwnMember',
        ]);

        // Create a member in a different community
        $otherCommunity = Community::factory()->create();
        $otherMember = Member::factory()->create([
            'community_id' => $otherCommunity->id,
            'first_name' => 'OtherMember',
        ]);

        $this->actingAs($director);

        // Director should be able to update member from their own community
        \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->call('updateMember', $ownMember->id, 'first_name', 'UpdatedName');

        $ownMember->refresh();
        $this->assertEquals('UpdatedName', $ownMember->first_name);

        // Director should NOT be able to update member from another community
        // Due to ScopedByCommunity, the member won't be found
        \Livewire\Livewire::test(\App\Livewire\MembersTable::class)
            ->call('updateMember', $otherMember->id, 'first_name', 'Hacked');

        $otherMember->refresh();
        $this->assertEquals('OtherMember', $otherMember->first_name,
            'Director should not be able to update member from another community');
    }
}
