<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CelebrationCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_celebrations_page_loads()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)
            ->get(route('celebrations.index'));

        $response->assertStatus(200);
        $response->assertViewIs('celebrations.index');
    }

    public function test_can_generate_birthday_card()
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->get(route('celebrations.birthday.generate', $member));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_can_download_birthday_card()
    {
        $community = \App\Models\Community::factory()->create();
        $user = User::factory()->create(['community_id' => $community->id]);
        $member = Member::factory()->create(['community_id' => $community->id]);

        $response = $this->actingAs($user)
            ->get(route('celebrations.birthday.download', $member));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
        $response->assertHeader('Content-Disposition', 'attachment; filename="birthday-card-' . $member->id . '.png"');
    }
}
