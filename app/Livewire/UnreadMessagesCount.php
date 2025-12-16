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
        // Count messages in conversations where I am a participant
        // Sent by someone else
        // And NOT in my message_reads
        
        $myId = Auth::id();
        
        // This query assumes we can get conversations via User relationship or just all messages where I am receiver
        // Since we are moving to group chat, 'receiver_id' might be unreliable if it was 1-on-1 before.
        // Assuming we rely on Conversation->users()
        
        $this->count = Message::whereHas('conversation.users', function($q) use ($myId) {
                $q->where('users.id', $myId);
            })
            ->where('sender_id', '!=', $myId)
            ->whereDoesntHave('reads', function($q) use ($myId) {
                $q->where('user_id', $myId);
            })
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
