<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class InvoiceChat extends Component
{
    public Client $client;
    public Invoice $invoice;
    public ?Conversation $conversation = null;
    public $selectedConversation;

    public function mount(Client $client, Invoice $invoice)
    {
        $this->client = $client;
        $this->invoice = $invoice;
        
        // Find or create conversation for this specific invoice
        $existingConversation = Conversation::where('client_id', $client->id)
            ->where('invoice_id', $invoice->id)
            ->whereHas('users', function($q) {
                $q->where('users.id', Auth::id());
            })
            ->first();

        if ($existingConversation) {
            $this->conversation = $existingConversation;
            $this->selectedConversation = $existingConversation;
        } else {
            // Create new conversation for this invoice
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

        // Mark unread messages as read for this user
        if ($this->selectedConversation) {
            // Updated logic for marking read: Find messages where I am a recipient (or global logic)
            // For now, keep as is but aware that receiver_id might be null in group chats.
            
            // If it's an invoice chat, it's likely 2 people for now, or group?
            // If group, receiver_id in messages table might be null.
            // But 'InvoiceChat' creation above sets receiver_id.
            // Let's assume standard behavior for now.
            
            Message::where('conversation_id', $this->selectedConversation->id)
                ->where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    public function render()
    {
        $clientInvoices = $this->client->invoices()->get();
        return view('livewire.invoice-chat', compact('clientInvoices'));
    }
}
