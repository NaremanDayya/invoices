<?php

namespace App\Livewire;

use App\Models\Message;
use App\Models\Conversation;
use App\Notifications\MessageRead;
use App\Notifications\MessageSent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\On;

class ChatBox extends Component
{
    public $selectedConversation;
    public $message = '';
    public $loadedMessages;
    public $paginate_var = 20; // Increased for better UX
    public $client_id;
    public $last_contact_date;
    public $hasMoreMessages = false;
    public $loading = false;

    protected $listeners = [
        'loadMore',
        'broadcastedNotificationReceived',
        'scroll-to-bottom',
        'messageSent' => 'refresh',
    ];

    public function getListeners()
    {
        $auth_id = Auth::id();
        return [
            'loadMore',
            'refresh' => 'refresh',
            'scroll-to-bottom' => 'scrollToBottom',
            "echo-private:chat.{$auth_id},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotificationReceived',
        ];
    }

    public function mount($client_id = null)
    {
        $this->client_id = $client_id;

        if ($this->selectedConversation) {
            $this->loadMessages();
            $this->last_contact_date = $this->selectedConversation->client->last_contact_date ?? null;
        }
    }

    #[On('scroll-to-bottom')]
    public function scrollToBottom()
    {
        $this->dispatch('scroll-bottom');
    }

    public function loadMore(): void
    {
        if (!$this->loading) {
            $this->loading = true;
            $this->paginate_var += 20;
            $this->loadMessages();
            $this->loading = false;
            $this->dispatch('messages-loaded');
        }
    }

    public function loadMessages()
    {
        $totalCount = Message::where('conversation_id', $this->selectedConversation->id)->count();

        // Check if there are more messages to load
        $this->hasMoreMessages = $totalCount > $this->paginate_var;

        $this->loadedMessages = Message::with([
            'sender',
            'receiver',
            'conversation.client.invoices'
        ])
            ->where('conversation_id', $this->selectedConversation->id)
            ->orderBy('created_at', 'desc')
            ->take($this->paginate_var)
            ->get()
            ->reverse();

        return $this->loadedMessages;
    }

    public function broadcastedNotificationReceived($event)
    {
        if ($event['type'] == MessageSent::class) {
            if ($event['conversation_id'] == $this->selectedConversation->id) {
                // Refresh messages
                $this->loadMessages();

                // Scroll to bottom
                $this->dispatch('scroll-bottom');

                // Mark message as read
                $message = Message::find($event['message_id']);
                if ($message && $message->receiver_id == Auth::id()) {
                    $message->read_at = now();
                    $message->save();

                    $message->sender->notify(new MessageRead(
                        Auth::user(),
                        $message,
                        $this->selectedConversation,
                        $message->sender_id,
                    ));
                }
            }
        }
    }

    public function sendLike()
    {
        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'message' => 'like',
        ]);

        $createdMessage->load(['sender', 'receiver']);

        $this->loadedMessages->push($createdMessage);

        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        $this->dispatch('refresh')->to('chat-list');

        $receiver = $this->selectedConversation->getReceiver();
        $receiver->notify(new MessageSent(
            Auth::user(),
            $createdMessage,
            $this->selectedConversation,
            $receiver->id,
        ));

        // Clear input and scroll
        $this->message = '';
        $this->dispatch('scroll-bottom');
    }

    public function sendMessage()
    {
        $this->validate(['message' => 'required|string|max:1000']);
        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedConversation->receiver->id,
            'message' => trim($this->message)
        ]);

        $createdMessage->load(['sender', 'receiver']);

        // Clear message input
        $this->message = '';

        // Refresh messages to include the new one
        $this->loadMessages();

        // Dispatch events for UI updates
        $this->dispatch('messageSent');
        $this->dispatch('scroll-bottom');

        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        $this->dispatch('refresh')->to('chat-list');

        $receiver = $createdMessage->receiver;
        $receiver->notify(new MessageSent(
            Auth::user(),
            $createdMessage,
            $this->selectedConversation,
            $receiver->id,
        ));
    }

    public function deleteConversation($conversationId)
    {
        if (!auth()->user()->isAdmin()) {
            return;
        }

        $conversation = Conversation::find($conversationId);

        if ($conversation) {
            Message::where('conversation_id', $conversationId)->delete();

            $conversation->delete();

            session()->flash('message', "تم حذف المحادثة الخاصة بالعميل $conversation->client->company_name");

            return redirect()->route('chat.index');
        }
    }

    public function editMessage($messageId, $newMessage)
    {
        $message = Message::find($messageId);

        if ($message && $message->sender_id === Auth::id()) {
            $message->message = $newMessage;
            $message->edited_at = now();
            $message->save();

            $this->loadMessages();

            $receiver = $this->selectedConversation->getReceiver();
            $receiver->notify(new MessageSent(
                Auth::user(),
                $message,
                $this->selectedConversation,
                $receiver->id,
            ));
        }
    }

    public function deleteMessage($messageId)
    {
        $message = Message::find($messageId);

        if ($message && $message->sender_id === Auth::id()) {
            $message->delete();

            $this->loadMessages();

            $this->selectedConversation->updated_at = now();
            $this->selectedConversation->save();

            $this->dispatch('refresh')->to('chat-list');
        }
    }

    public function refresh()
    {
        $this->loadMessages();
        $this->dispatch('scroll-bottom');
    }

    public function render()
    {
        return view('livewire.chat-box');
    }
}
