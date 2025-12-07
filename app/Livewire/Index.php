<?php

namespace App\Livewire;

use App\Models\Conversation;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public $selectedConversation = null;

    #[On('conversationSelected')]
    public function loadConversation($id)
    {
        $this->selectedConversation = Conversation::with('client')->find($id);
    }

    public function render()
    {
        return view('livewire.index');
    }
}
