<?php

namespace Tests\Support;

use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Test Notification',
            'message' => 'Test notification'
        ];
    }

    public function toArray($notifiable)
    {
        return ['message' => 'Test notification'];
    }
}
