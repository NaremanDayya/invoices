<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public Client $client;
    public Conversation $conversation;
    public $selectedConversation;

    public function mount(Client $client, Conversation $conversation)
    {
        $this->client = $client;
        $this->conversation = $conversation;
        $this->selectedConversation = $conversation;

        // Mark unread messages as read for this user
        Message::where('conversation_id', $this->selectedConversation->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        return view('livewire.chat');
    }
}
