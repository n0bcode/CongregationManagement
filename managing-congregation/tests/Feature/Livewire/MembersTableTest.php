<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MembersTable;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MembersTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_members_table_renders_correctly()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        Member::factory()->count(5)->create();

        Livewire::actingAs($user)
            ->test(MembersTable::class)
            ->assertStatus(200)
            ->assertViewIs('livewire.members-table')
            ->assertSee(Member::first()->first_name);
    }

    public function test_search_functionality()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $member = Member::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        Member::factory()->create(['first_name' => 'Jane', 'last_name' => 'Doe']);

        Livewire::actingAs($user)
            ->test(MembersTable::class)
            ->set('search', 'John')
            ->assertSee('John')
            ->assertDontSee('Jane');
    }

    public function test_sorting_functionality()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $memberA = Member::factory()->create(['first_name' => 'Alice']);
        $memberB = Member::factory()->create(['first_name' => 'Bob']);

        Livewire::actingAs($user)
            ->test(MembersTable::class)
            ->set('sortField', 'first_name')
            ->set('sortDirection', 'asc')
            ->assertSeeInOrder(['Alice', 'Bob'])
            ->set('sortDirection', 'desc')
            ->assertSeeInOrder(['Bob', 'Alice']);
    }

    public function test_bulk_delete()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $members = Member::factory()->count(3)->create();

        Livewire::actingAs($user)
            ->test(MembersTable::class)
            ->set('selected', $members->pluck('id')->map(fn($id) => (string) $id)->toArray())
            ->call('deleteSelected')
            ->assertSet('selected', []);

        foreach ($members as $member) {
            $this->assertSoftDeleted('members', ['id' => $member->id]);
        }
    }

    public function test_inline_update()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $member = Member::factory()->create(['status' => \App\Enums\MemberStatus::Active]);

        Livewire::actingAs($user)
            ->test(MembersTable::class)
            ->call('updateMember', $member->id, 'status', \App\Enums\MemberStatus::Deceased->value);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'status' => \App\Enums\MemberStatus::Deceased->value,
        ]);
    }

    public function test_filter_preset()
    {
        $user = User::factory()->create(['role' => \App\Enums\UserRole::SUPER_ADMIN]);
        $activeMember = Member::factory()->create(['status' => \App\Enums\MemberStatus::Active, 'first_name' => 'ActiveMember']);
        $deceasedMember = Member::factory()->create(['status' => \App\Enums\MemberStatus::Deceased, 'first_name' => 'DeceasedMember']);

        Livewire::actingAs($user)
            ->test(MembersTable::class)
            ->call('applyPreset', 'active')
            ->assertSet('status', \App\Enums\MemberStatus::Active->value)
            ->assertSee('ActiveMember')
            ->assertDontSee('DeceasedMember');
    }
}
