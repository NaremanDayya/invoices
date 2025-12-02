<?php

namespace App\Livewire;

use App\Models\Message;
use App\Notifications\MessageRead;
use App\Notifications\MessageSent;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatBox extends Component
{
    public $selectedConversation;
    public $message;
    public $loadedMessages;
    public $paginate_var = 10;
    public $client_id;
    public $last_contact_date;

    protected $listeners = [
        'loadMore',
        'broadcastedNotificationReceived',
    ];

    public function getListeners()
    {
        $auth_id = Auth::id();
        return [
            'loadMore',
            'refresh' => 'refresh',
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

    public function loadMore(): void
    {
        $this->paginate_var += 10;
        $this->loadMessages();
        $this->dispatch('update-chat-height');
    }
    public function loadMessages()
    {
        $count = Message::where('conversation_id', $this->selectedConversation->id)->count();

        $this->loadedMessages = Message::with([
            'sender',
            'receiver',
            'conversation.client.salesRep' // Eager load nested relationships
        ])
            ->where('conversation_id', $this->selectedConversation->id)
            ->skip(max(0, $count - $this->paginate_var))
            ->take($this->paginate_var)
            ->orderBy('created_at')
            ->get();

        return $this->loadedMessages;
    }

    public function broadcastedNotificationReceived($event)
    {
        if ($event['type'] == MessageSent::class) {
            if ($event['conversation_id'] == $this->selectedConversation->id) {
                $this->dispatch('scroll-bottom');

                // Eager load the new message with relationships
                $newMessage = Message::with(['sender', 'receiver'])
                    ->find($event['message_id']);

                $this->loadedMessages->push($newMessage);

                $newMessage->read_at = now();
                $newMessage->save();

                // Use the eager loaded receiver
                $this->selectedConversation->getReceiver()
                    ->notify(new MessageRead(
                        Auth::user(),
                        $newMessage,
                        $this->selectedConversation,
                        $newMessage->receiver_id,
                    ));
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

        $this->dispatch('scroll-bottom');
    }

    public function sendMessage()
    {
        $this->validate(['message' => 'required|string']);

        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::id(),
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'message' => $this->message
        ]);

        // Eager load relationships when pushing new message
        $createdMessage->load(['sender', 'receiver']);

        $this->message = '';
        $this->dispatch('scroll-bottom');

        $this->loadedMessages->push($createdMessage);

        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        $this->dispatch('refresh')->to('chat-list');

        // Use pre-loaded receiver instead of triggering new query
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
        dd('test');
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

        // Check if the user owns this message
        if ($message && $message->sender_id === Auth::id()) {
            $message->message = $newMessage;
            $message->edited_at = now();
            $message->save();

            // Refresh messages
            $this->loadMessages();

            // Notify the receiver about the edit
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

        // Check if the user owns this message
        if ($message && $message->sender_id === Auth::id()) {
            $message->delete();

            // Refresh messages
            $this->loadMessages();

            // Update conversation timestamp
            $this->selectedConversation->updated_at = now();
            $this->selectedConversation->save();

            // Refresh chat list
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
