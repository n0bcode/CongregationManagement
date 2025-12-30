<?php

namespace Tests\Feature\Livewire\Notifications;

use App\Livewire\Notifications\NotificationCenter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(NotificationCenter::class)
            ->assertStatus(200);
    }

    public function test_can_filter_notifications()
    {
        $user = User::factory()->create();
        
        // Create notifications
        \Illuminate\Support\Facades\DB::table('user_notifications')->insert([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Unread',
            'message' => 'Test',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        \Illuminate\Support\Facades\DB::table('user_notifications')->insert([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Read',
            'message' => 'Test',
            'read_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(NotificationCenter::class)
            ->set('filter', 'unread')
            ->assertViewHas('notifications', function ($notifications) {
                return $notifications->count() === 1 && $notifications->first()->title === 'Unread';
            });
    }

    public function test_can_mark_as_read()
    {
        $user = User::factory()->create();
        $id = \Illuminate\Support\Facades\DB::table('user_notifications')->insertGetId([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Unread',
            'message' => 'Test',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(NotificationCenter::class)
            ->call('markAsRead', $id);

        $this->assertNotNull(\Illuminate\Support\Facades\DB::table('user_notifications')->find($id)->read_at);
    }

    public function test_can_mark_all_as_read()
    {
        $user = User::factory()->create();
        \Illuminate\Support\Facades\DB::table('user_notifications')->insert([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Unread 1',
            'message' => 'Test',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        \Illuminate\Support\Facades\DB::table('user_notifications')->insert([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Unread 2',
            'message' => 'Test',
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Livewire::actingAs($user)
            ->test(NotificationCenter::class)
            ->call('markAllAsRead');

        $this->assertEquals(0, \Illuminate\Support\Facades\DB::table('user_notifications')->where('user_id', $user->id)->whereNull('read_at')->count());
    }
}
