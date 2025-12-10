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
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())
                    ->orWhere('receiver_id', Auth::id());
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
            ]);
            
            $this->conversation = $newConversation;
            $this->selectedConversation = $newConversation;
        }

        // Mark unread messages as read for this user
        if ($this->selectedConversation) {
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
