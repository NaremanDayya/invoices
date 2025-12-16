<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;

class MessageRead extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $reader;
    public $message;
    public $conversation;
    public $originalSenderId;

    /**
     * Create a new notification instance.
     */
    public function __construct($reader, $message, $conversation, $originalSenderId = null)
    {
        $this->reader = $reader;
        $this->message = $message;
        $this->conversation = $conversation;
        $this->originalSenderId = $originalSenderId ?? $message->sender_id;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'conversation_id' => $this->conversation->id,
            'message_id' => $this->message->id,
            'reader_id' => $this->reader->id,
            'read_at' => $this->message->read_at,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->originalSenderId);
    }
}
