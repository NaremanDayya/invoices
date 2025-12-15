<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;

class Chat extends Component
{
    public Client $client;
    public ?Conversation $conversation = null;
    public Invoice $invoice;
    public $selectedConversation;

    public function mount(Client $client, Invoice $invoice, Conversation $conversation = null)
    {
        $this->client = $client;
        $this->invoice = $invoice;

        // 1. Try to find the conversation specifically for this invoice
        // 1. Try to find the conversation specifically for this invoice
        $existingConversation = Conversation::where('client_id', $client->id)
            ->where('invoice_id', $invoice->id)
            // Ensure we check that the current user is part of the conversation
            ->whereHas('users', function($q) {
                $q->where('users.id', Auth::id());
            })
            ->first();

        if ($existingConversation) {
            $this->conversation = $existingConversation;
            $this->selectedConversation = $existingConversation;
        } else {
            $receiverId = $client->sales_rep_id ?? Auth::id();

            $newConversation = Conversation::create([
                'id' => (string)Str::uuid(),
                'sender_id' => Auth::id(),
                'receiver_id' => $receiverId,
                'client_id' => $client->id,
                'invoice_id' => $invoice->id,
                'type' => 'invoice'
            ]);
            
            // Attach ALL system users to the conversation as requested
            $allUserIds = \App\Models\User::pluck('id')->toArray();
            $participants = array_unique($allUserIds);
            
            $newConversation->users()->sync($participants);

            $this->conversation = $newConversation;
            $this->selectedConversation = $newConversation;
        }

        // Mark messages read
        if($this->selectedConversation) {
            Message::where('conversation_id', $this->selectedConversation->id)
                ->where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }
    public function render()
    {
        if ($this->invoice) {
            Log::info('Invoice in chat:', [
                'id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'client_id' => $this->invoice->client_id,
                'route_client_id' => $this->client->id,
            ]);
        }
        $clientInvoices = $this->client->invoices()->get();
        return view('livewire.chat', compact('clientInvoices'));
    }
}
