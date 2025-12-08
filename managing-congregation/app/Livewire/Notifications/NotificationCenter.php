<?php

namespace App\Livewire\Notifications;

use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class NotificationCenter extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, unread

    public function getListeners()
    {
        return [
            'echo-private:App.Models.User.' . auth()->id() . ',.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated' => 'refreshNotifications',
        ];
    }

    public function refreshNotifications()
    {
        $this->resetPage();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = \App\Models\Notification::where('user_id', auth()->id())->find($notificationId);

        if ($notification) {
            $notification->update(['read_at' => now()]);
        }
    }

    public function markAllAsRead()
    {
        \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->update(['read_at' => now()]);
    }

    public function deleteNotification($notificationId)
    {
        $notification = \App\Models\Notification::where('user_id', auth()->id())->find($notificationId);

        if ($notification) {
            $notification->delete();
        }
    }

    public function render()
    {
        $query = \App\Models\Notification::where('user_id', auth()->id())->orderBy('created_at', 'desc');

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        }

        return view('livewire.notifications.notification-center', [
            'notifications' => $query->paginate(10),
        ]);
    }
}
