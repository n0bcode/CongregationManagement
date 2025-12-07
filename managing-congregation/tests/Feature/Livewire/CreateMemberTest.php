<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Members\CreateMember;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateMemberTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_create_member_component()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(CreateMember::class)
            ->assertStatus(200);
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(CreateMember::class)
            ->set('entry_date', '')
            ->call('save')
            ->assertHasErrors(['first_name', 'last_name', 'dob', 'entry_date', 'community_id']);
    }

    /** @test */
    public function it_creates_member_successfully()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $this->actingAs($user);

        Livewire::test(CreateMember::class)
            ->set('first_name', 'John')
            ->set('last_name', 'Doe')
            ->set('dob', '1990-01-01')
            ->set('entry_date', '2020-01-01')
            ->set('community_id', $community->id)
            ->call('save')
            ->assertRedirect(route('members.index'));

        $this->assertDatabaseHas('members', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'community_id' => $community->id,
        ]);
    }

    /** @test */
    public function it_performs_real_time_validation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Livewire::test(CreateMember::class)
            ->set('first_name', '')
            ->assertHasErrors(['first_name'])
            ->set('first_name', 'John')
            ->assertHasNoErrors(['first_name']);
    }
}
