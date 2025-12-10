<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Invoice;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Chat extends Component
{
    public Client $client;
    public ?Conversation $conversation = null;
    public ?Invoice $invoice = null;
    public $selectedConversation;

    public function mount(Client $client, Invoice $invoice = null, Conversation $conversation = null)
    {
        $this->client = $client;
        
        // Handle two navigation modes:
        // 1. Invoice-based: When clicking from invoice sidebar (invoice provided)
        // 2. Conversation-based: When clicking from chat list (conversation provided)
        
        if ($invoice) {
            // Invoice-based navigation
            $this->invoice = $invoice;
            
            // Find or create conversation for this invoice
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
        } elseif ($conversation) {
            // Conversation-based navigation
            $this->conversation = $conversation;
            $this->selectedConversation = $conversation;
            
            // Load the invoice from the conversation if it exists
            if ($conversation->invoice_id) {
                $this->invoice = $conversation->invoice;
            }
        } else {
            // No invoice or conversation provided - create a general conversation
            $existingConversation = Conversation::where('client_id', $client->id)
                ->whereNull('invoice_id')
                ->where(function ($q) {
                    $q->where('sender_id', Auth::id())
                        ->orWhere('receiver_id', Auth::id());
                })
                ->first();

            if ($existingConversation) {
                $this->conversation = $existingConversation;
                $this->selectedConversation = $existingConversation;
            } else {
                // Create new general conversation
                $receiverId = $client->sales_rep_id ?? Auth::id();
                
                $newConversation = Conversation::create([
                    'id' => (string)Str::uuid(),
                    'sender_id' => Auth::id(),
                    'receiver_id' => $receiverId,
                    'client_id' => $client->id,
                    'invoice_id' => null,
                ]);
                
                $this->conversation = $newConversation;
                $this->selectedConversation = $newConversation;
            }
        }

        // Mark unread messages as read for this user
        Message::where('conversation_id', $this->selectedConversation->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $clientInvoices = $this->client->invoices()->get();
        return view('livewire.chat', compact('clientInvoices'));
    }
}
