<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Notifications\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function sendImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // Max 10MB
            'caption' => 'nullable|string|max:500',
            'conversation_id' => 'required|exists:conversations,id',
        ]);

        try {
            $conversation = Conversation::findOrFail($request->conversation_id);
            
            // Store the image
            $image = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('chat-images', $imageName, 'public');

            // Create message with image
            $messageText = $request->caption ? $request->caption : '[Image]';
            
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => Auth::id(),
                'receiver_id' => $conversation->getReceiver()->id,
                'message' => $messageText,
                'image_path' => $imagePath, // You'll need to add this column to messages table
            ]);

            // Update conversation timestamp
            $conversation->updated_at = now();
            $conversation->save();

            // Send notification
            $receiver = $conversation->getReceiver();
            $receiver->notify(new MessageSent(
                Auth::user(),
                $message,
                $conversation,
                $receiver->id
            ));

            return response()->json([
                'success' => true,
                'message' => 'Image sent successfully',
                'data' => [
                    'message_id' => $message->id,
                    'image_url' => Storage::url($imagePath),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function unreadConversationsCount()
    {
        // Add your unread count logic here
        return response()->json(['count' => 0]);
    }
}
