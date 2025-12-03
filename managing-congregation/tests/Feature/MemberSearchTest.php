<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_search_members_by_religious_name()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $user->community_id = $community->id;
        $user->save();

        Member::factory()->create([
            'community_id' => $community->id,
            'religious_name' => 'Sister Mary',
        ]);

        Member::factory()->create([
            'community_id' => $community->id,
            'religious_name' => 'Sister Martha',
        ]);

        $response = $this->actingAs($user)->get(route('members.index', ['search' => 'Mary']));

        $response->assertOk();
        $response->assertSee('Sister Mary');
        $response->assertDontSee('Sister Martha');
    }

    public function test_can_search_members_by_civil_name()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $user->community_id = $community->id;
        $user->save();

        Member::factory()->create([
            'community_id' => $community->id,
            'first_name' => 'Alice',
            'last_name' => 'Smith',
        ]);

        Member::factory()->create([
            'community_id' => $community->id,
            'first_name' => 'Bob',
            'last_name' => 'Jones',
        ]);

        $response = $this->actingAs($user)->get(route('members.index', ['search' => 'Alice']));

        $response->assertOk();
        $response->assertSee('Alice');
        $response->assertDontSee('Bob');

        $response = $this->actingAs($user)->get(route('members.index', ['search' => 'Smith']));

        $response->assertOk();
        $response->assertSee('Alice');
        $response->assertDontSee('Bob');
    }

    public function test_search_is_case_insensitive_and_partial()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $user->community_id = $community->id;
        $user->save();

        Member::factory()->create([
            'community_id' => $community->id,
            'religious_name' => 'Sister Bernadette',
        ]);

        $response = $this->actingAs($user)->get(route('members.index', ['search' => 'berna']));

        $response->assertOk();
        $response->assertSee('Sister Bernadette');
    }

    public function test_search_respects_community_scope()
    {
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        $user = User::factory()->create(['community_id' => $community1->id]);

        Member::factory()->create([
            'community_id' => $community1->id,
            'religious_name' => 'Sister Mary (C1)',
        ]);

        Member::factory()->create([
            'community_id' => $community2->id,
            'religious_name' => 'Sister Mary (C2)',
        ]);

        $response = $this->actingAs($user)->get(route('members.index', ['search' => 'Mary']));

        $response->assertOk();
        $response->assertSee('Sister Mary (C1)');
        $response->assertDontSee('Sister Mary (C2)');
    }
}
