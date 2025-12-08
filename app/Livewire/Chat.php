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
    public Conversation $conversation;
    public ?Invoice $invoice = null;
    public $selectedConversation;

    public function mount(Client $client, Conversation $conversation, Invoice $invoice = null)
    {
        $this->client = $client;
        $this->conversation = $conversation;
        $this->selectedConversation = $conversation;

        // If invoice is provided, check if this conversation is for this specific invoice
        if ($invoice) {
            $this->invoice = $invoice;
            
            // Check if we need to find/create an invoice-specific conversation
            $invoiceConversation = Conversation::where('client_id', $client->id)
                ->where('invoice_id', $invoice->id)
                ->where(function ($q) {
                    $q->where('sender_id', Auth::id())
                        ->orWhere('receiver_id', Auth::id());
                })
                ->first();

            if ($invoiceConversation) {
                $this->conversation = $invoiceConversation;
                $this->selectedConversation = $invoiceConversation;
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
        } else {
            // General client chat (no specific invoice)
            $this->invoice = $client->invoices()->first(); // Default to first invoice for display
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
    public function startInvoiceChat($invoiceId)
    {
        $user = Auth::user();
        $invoice = Invoice::with('client')->find($invoiceId);

        if (!$invoice) {
            return;
        }

        // Check for existing conversation for this invoice
        $existing = Conversation::where('client_id', $invoice->client_id)
            ->where('invoice_id', $invoiceId)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->first();

        if ($existing) {
            // Redirect to existing invoice chat
            return redirect()->route('client.chat.invoice', [
                'client' => $invoice->client_id,
                'conversation' => $existing->id,
                'invoice' => $invoiceId
            ]);
        }

        // Create new conversation for this invoice
        $receiverId = $invoice->client->sales_rep_id ?? $user->id;

        $newConversation = Conversation::create([
            'id' => (string)Str::uuid(),
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'client_id' => $invoice->client_id,
            'invoice_id' => $invoiceId,
        ]);

        // Redirect to new invoice chat
        return redirect()->route('client.chat.invoice', [
            'client' => $invoice->client_id,
            'conversation' => $newConversation->id,
            'invoice' => $invoiceId
        ]);
    }
}
