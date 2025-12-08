<?php

namespace App\Livewire;

use App\Models\Client;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public Client $client;
    public Conversation $conversation;
    public $selectedConversation;
    public $showChatList = false; // Add this property

    public function mount(Client $client, Conversation $conversation = null)
    {
        $this->client = $client;

        // If conversation is provided, use it
        if ($conversation) {
            $this->conversation = $conversation;
            $this->selectedConversation = $conversation;
        }

        // Mark unread messages as read for current conversation
        if (isset($this->selectedConversation)) {
            Message::where('conversation_id', $this->selectedConversation->id)
                ->where('receiver_id', Auth::id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }
    }

    /**
     * Open or create chat for a specific invoice
     */
    public function openInvoiceChat($invoiceId)
    {
        // Find the invoice
        $invoice = Invoice::findOrFail($invoiceId);

        // Check if conversation already exists for this invoice
        $conversation = Conversation::where('invoice_id', $invoiceId)
            ->where('client_id', $this->client->id)
            ->first();

        if (!$conversation) {
            // Create new conversation for this invoice
            $conversation = Conversation::create([
                'client_id' => $this->client->id,
                'invoice_id' => $invoiceId,
                'title' => 'Invoice #' . $invoice->invoice_number,
                'last_message_at' => now(),
            ]);

            // Add participants (adjust based on your logic)
            // Example: Current user and client's primary contact
            $conversation->participants()->attach([
                Auth::id() => ['role' => 'user'],
                    $this->client->primary_contact_id ?? $this->client->user_id => ['role' => 'client']
            ]);
        }

        // Update the selected conversation
        $this->selectedConversation = $conversation;
        $this->conversation = $conversation;

        // Mark messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Dispatch event to update UI
        $this->dispatch('conversation-selected', conversationId: $conversation->id);
    }

    /**
     * Toggle chat list visibility (for the top right arrow)
     */
    public function toggleChatList()
    {
        $this->showChatList = !$this->showChatList;
    }

    public function render()
    {
        return view('livewire.chat', [
            'invoices' => $this->client->invoices()->latest()->get()
        ]);
    }
}
