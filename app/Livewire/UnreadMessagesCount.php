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
        $myId = Auth::id();

        // Count distinct conversations (both private and invoice) that have
        // unread messages for the current user via chat_receivers table.
        $this->count = \DB::table('conversations as c')
            ->join('messages as m', 'm.conversation_id', '=', 'c.id')
            ->join('chat_receivers as cr', function ($join) use ($myId) {
                $join->on('cr.message_id', '=', 'm.id')
                     ->where('cr.receiver_id', '=', $myId)
                     ->where('cr.is_read', '=', false);
            })
            ->join('conversation_participants as cp', function ($join) use ($myId) {
                $join->on('cp.conversation_id', '=', 'c.id')
                     ->where('cp.user_id', '=', $myId);
            })
            ->whereIn('c.type', ['private', 'invoice'])
            ->selectRaw('COUNT(DISTINCT c.id) as cnt')
            ->value('cnt') ?? 0;
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
