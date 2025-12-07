<?php

namespace Tests\Unit\Services;

use App\Models\Reminder;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_send_notification()
    {
        $service = new NotificationService();
        $user = User::factory()->create();
        
        $this->actingAs($user);

        $service->send($user, 'Test Title', 'Test Message');

        $this->assertDatabaseHas('notifications', [
            'title' => 'Test Title',
            'message' => 'Test Message',
            'type' => 'info',
            'user_id' => $user->id,
        ]);
    }

    public function test_it_can_get_unread_notifications()
    {
        $service = new NotificationService();
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create a notification
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'title' => 'Unread Notification',
            'message' => 'Message',
            'type' => 'info',
            'priority' => 'normal',
        ]);

        $unread = $service->getUnread($user);
        
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $unread);
        $this->assertCount(1, $unread);
    }
}
