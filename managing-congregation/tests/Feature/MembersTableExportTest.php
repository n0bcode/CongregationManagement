<?php

namespace Tests\Feature;

use App\Livewire\MembersTable;
use App\Models\City;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MembersTableExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_export_members_including_those_without_community()
    {
        $user = User::factory()->create();
        
        // Member with community
        $community = Community::factory()->create(['name' => 'Test Community']);
        $member1 = Member::factory()->create([
            'community_id' => $community->id,
            'first_name' => 'John',
        ]);

        // Member with Soft Deleted community (Simulating P1 condition where relation is null)
        $deletedCommunity = Community::factory()->create();
        $member2 = Member::factory()->create([
            'community_id' => $deletedCommunity->id,
            'first_name' => 'Jane',
        ]);
        $deletedCommunity->delete();

        Livewire::actingAs($user)
            ->test(MembersTable::class)
            ->set('selected', [$member1->id, $member2->id])
            ->call('exportSelected')
            ->assertFileDownloaded('members-export-' . now()->format('Y-m-d-His') . '.csv');
    }
}
