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
    public Invoice $invoice;
    public $selectedConversation;

    public function mount(Client $client, Conversation $conversation,Invoice $invoice)
    {
        $this->client = $client;
        $this->invoice = $invoice;
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
            $this->dispatch('selectConversation', ['id' => $existing->id]);
            return;
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

        $this->refresh();
        $this->dispatch('conversationSelected', id: $newConversation->id);
        $this->dispatch('invoiceSelected', invoiceId: $invoiceId);
    }
}
