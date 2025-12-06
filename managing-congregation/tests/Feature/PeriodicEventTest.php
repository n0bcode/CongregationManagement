<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PeriodicEvent;
use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodicEventTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_periodic_events()
    {
        $user = User::factory()->create();
        $event = PeriodicEvent::create([
            'name' => 'Annual Retreat',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(15),
            'recurrence' => 'annual',
        ]);

        $response = $this->actingAs($user)->get(route('periodic-events.index'));

        $response->assertStatus(200);
        $response->assertSee('Annual Retreat');
    }

    public function test_user_can_create_periodic_event()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        $response = $this->actingAs($user)->post(route('periodic-events.store'), [
            'name' => 'Monthly Meeting',
            'description' => 'Regular community meeting',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-01',
            'recurrence' => 'monthly',
            'community_id' => $community->id,
        ]);

        $response->assertRedirect(route('periodic-events.index'));
        $this->assertDatabaseHas('periodic_events', [
            'name' => 'Monthly Meeting',
            'recurrence' => 'monthly',
            'community_id' => $community->id,
        ]);
    }

    public function test_user_can_update_periodic_event()
    {
        $user = User::factory()->create();
        $event = PeriodicEvent::create([
            'name' => 'Old Name',
            'start_date' => now(),
            'end_date' => now(),
            'recurrence' => 'one-time',
        ]);

        $response = $this->actingAs($user)->put(route('periodic-events.update', $event), [
            'name' => 'New Name',
            'start_date' => '2025-02-01',
            'end_date' => '2025-02-05',
            'recurrence' => 'annual',
        ]);

        $response->assertRedirect(route('periodic-events.index'));
        $this->assertDatabaseHas('periodic_events', [
            'id' => $event->id,
            'name' => 'New Name',
            'recurrence' => 'annual',
        ]);
    }

    public function test_user_can_delete_periodic_event()
    {
        $user = User::factory()->create();
        $event = PeriodicEvent::create([
            'name' => 'To Delete',
            'start_date' => now(),
            'end_date' => now(),
            'recurrence' => 'one-time',
        ]);

        $response = $this->actingAs($user)->delete(route('periodic-events.destroy', $event));

        $response->assertRedirect(route('periodic-events.index'));
        $this->assertSoftDeleted($event);
    }
}
