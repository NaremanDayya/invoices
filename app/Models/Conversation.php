<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'id',
        'receiver_id',
        'sender_id',
        'client_id',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function getUnreadMessagesCountAttribute()
    {
        if (array_key_exists('unread_count', $this->attributes)) {
            return $this->attributes['unread_count'];
        }

        return $this->unreadMessagesCount();
    }

    public function getReceiver()
    {
        if ($this->sender_id === Auth::id()) {

            return User::firstWhere('id', $this->receiver_id);
        } else {

            return User::firstWhere('id', $this->sender_id);
        }
    }
    public function unreadMessagesCount(): int
    {
        return $unreadMessages = Message::where('conversation_id', '=', $this->id)
            ->where('receiver_id', Auth::user()->id)
            ->whereNull('read_at')->count();
    }

    public function isLastMessageReadByUser()
    {


        $user = Auth::user();
        $lastMessage = $this->messages()->latest()->first();

        if ($lastMessage) {
            return $lastMessage->read_at !== null && $lastMessage->sender_id == $user->id;
        }
    }
    public function scopeWhereNotDeleted($query)
    {
        $userId = Auth::id();

        return $query->where(function ($query) use ($userId) {

            #where message is not deleted
            $query->whereHas('messages', function ($query) use ($userId) {

                $query->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)
                        ->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {

                    $query->where('receiver_id', $userId)
                        ->whereNull('receiver_deleted_at');
                });


            })
                #include conversations without messages
                ->orWhereDoesntHave('messages');


        });

    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    public function getLatestMessageTextAttribute()
    {
        if (!$this->relationLoaded('latestMessage') && !isset($this->attributes['latest_message_text'])) {
            $latestMessage = Message::where('conversation_id', $this->id)
                ->latest('id')
                ->first();

            return $latestMessage ? $latestMessage->message : '';
        }

        return $this->latestMessage?->message ?? $this->attributes['latest_message_text'] ?? '';
    }

    public function getLatestMessageTimeAttribute()
    {
        if (!$this->relationLoaded('latestMessage') && !isset($this->attributes['latest_message_time'])) {
            $latestMessage = Message::where('conversation_id', $this->id)
                ->latest('id')
                ->first();

            return $latestMessage ? $latestMessage->created_at : null;
        }

        return $this->latestMessage?->created_at ?? $this->attributes['latest_message_time'] ?? null;
    }

    public function getLatestMessageSenderIdAttribute()
    {
        if (!$this->relationLoaded('latestMessage') && !isset($this->attributes['latest_message_sender_id'])) {
            $latestMessage = Message::where('conversation_id', $this->id)
                ->latest('id')
                ->first();

            return $latestMessage ? $latestMessage->sender_id : null;
        }

        return $this->latestMessage?->sender_id ?? $this->attributes['latest_message_sender_id'] ?? null;
    }
}
