<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class UnreadMessagesCount extends Component
{
    public $count = 0;

    protected $listeners = [
        'broadcastedNotificationReceived', 
        'refreshUnreadCount' => 'updateCount'
    ];

    public function getListeners()
    {
        $auth_id = Auth::id();
        return [
            "echo-private:chat.{$auth_id},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotificationReceived',
            'refreshUnreadCount' => 'updateCount'
        ];
    }
    
    public function mount()
    {
        $this->updateCount();
    }

    public function updateCount()
    {
        $this->count = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
    }

    public function broadcastedNotificationReceived($event)
    {
        // Whenever a notification is received (new message, or even message read), update count.
        // Specifically for new messages, the count increases.
        $this->updateCount();
    }

    public function render()
    {
        return view('livewire.unread-messages-count');
    }
}
