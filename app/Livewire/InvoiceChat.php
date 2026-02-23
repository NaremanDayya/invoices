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

        // Mark unread messages as read for this user via chat_receivers table
        if ($this->selectedConversation) {
            \DB::table('chat_receivers')
                ->join('messages', 'messages.id', '=', 'chat_receivers.message_id')
                ->where('messages.conversation_id', $this->selectedConversation->id)
                ->where('chat_receivers.receiver_id', Auth::id())
                ->where('chat_receivers.is_read', false)
                ->update([
                    'chat_receivers.is_read' => true,
                    'chat_receivers.read_at' => now(),
                ]);
        }
    }

    public function render()
    {
        $clientInvoices = $this->client->invoices()->get();
        return view('livewire.invoice-chat', compact('clientInvoices'));
    }
}
