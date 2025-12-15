<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityExtendedFieldsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_community_with_extended_fields()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $this->actingAs($user);

        $response = $this->post(route('communities.store'), [
            'name' => 'St. Joseph Community',
            'location' => 'Nazareth',
            'patron_saint' => 'St. Joseph',
            'founded_at' => '2023-01-01',
            'feast_day' => '2023-03-19',
            'email' => 'joseph@example.com',
            'phone' => '123-456-7890',
        ]);

        $response->assertRedirect(route('communities.index'));
        $this->assertDatabaseHas('communities', [
            'name' => 'St. Joseph Community',
            'patron_saint' => 'St. Joseph',
            'email' => 'joseph@example.com',
            'phone' => '123-456-7890',
        ]);
    }

    public function test_can_update_community_with_extended_fields()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $this->actingAs($user);

        $community = Community::factory()->create([
            'name' => 'Old Name',
        ]);

        $response = $this->put(route('communities.update', $community), [
            'name' => 'New Name',
            'patron_saint' => 'St. Peter',
            'founded_at' => '2020-05-05',
            'feast_day' => '2020-06-29',
            'email' => 'peter@example.com',
            'phone' => '987-654-3210',
        ]);

        $response->assertRedirect(route('communities.show', $community));
        $this->assertDatabaseHas('communities', [
            'id' => $community->id,
            'name' => 'New Name',
            'patron_saint' => 'St. Peter',
            'email' => 'peter@example.com',
        ]);
    }

    public function test_fields_are_nullable()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $this->actingAs($user);

        $response = $this->post(route('communities.store'), [
            'name' => 'Minimal Community',
        ]);

        $response->assertRedirect(route('communities.index'));
        $this->assertDatabaseHas('communities', [
            'name' => 'Minimal Community',
            'patron_saint' => null,
            'email' => null,
        ]);
    }

    public function test_validation_rules()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $this->actingAs($user);

        $response = $this->post(route('communities.store'), [
            'name' => 'Invalid Community',
            'email' => 'not-an-email',
            'founded_at' => 'not-a-date',
            'feast_day' => 'not-a-date',
        ]);

        $response->assertSessionHasErrors(['email', 'founded_at', 'feast_day']);
    }
}
