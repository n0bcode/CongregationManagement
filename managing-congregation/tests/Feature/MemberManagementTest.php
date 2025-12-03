<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_director_can_view_member_list(): void
    {
        $user = User::factory()->director()->create();

        $response = $this->actingAs($user)->get('/members');

        $response->assertStatus(200);
    }

    public function test_director_can_view_create_member_form(): void
    {
        $user = User::factory()->director()->create();

        $response = $this->actingAs($user)->get('/members/create');

        $response->assertStatus(200);
    }

    public function test_director_can_view_member_profile(): void
    {
        $user = User::factory()->director()->create();
        $member = \App\Models\Member::factory()->forCommunity($user->community)->create();

        $response = $this->actingAs($user)->get("/members/{$member->id}");

        $response->assertStatus(200);
        $response->assertSee($member->first_name);
    }

    public function test_director_can_create_member(): void
    {
        $user = User::factory()->director()->create();
        $memberData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'religious_name' => 'Brother John',
            'dob' => '1990-01-01',
            'entry_date' => '2010-01-01',
        ];

        $response = $this->actingAs($user)->post('/members', $memberData);

        $response->assertRedirect();
        $this->assertDatabaseHas('members', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'community_id' => $user->community_id,
        ]);
    }

    public function test_duplicate_member_creation_fails(): void
    {
        $user = User::factory()->director()->create();
        \App\Models\Member::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
            'community_id' => $user->community_id,
        ]);

        $memberData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'dob' => '1990-01-01',
            'entry_date' => '2010-01-01',
        ];

        $response = $this->actingAs($user)->post('/members', $memberData);

        $response->assertSessionHasErrors(['first_name']);
    }

    public function test_director_cannot_view_member_from_another_community(): void
    {
        $user = User::factory()->director()->create();
        $otherCommunity = \App\Models\Community::factory()->create();
        $member = \App\Models\Member::factory()->forCommunity($otherCommunity)->create();

        $response = $this->actingAs($user)->get("/members/{$member->id}");

        $response->assertStatus(404);
    }
}
