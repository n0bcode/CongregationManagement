<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CommunityShowTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_community_show_page_loads_without_error()
    {
        $user = \App\Models\User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $community = \App\Models\Community::factory()->create();
        $member = \App\Models\Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)->get(route('communities.show', $community));

        $response->assertStatus(200);
        $response->assertSee($community->name);
    }
}
