<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'receiver_id',
        'edited_at',
        'sender_id',
        'message',
        'is_admin',
        'conversation_id',
        'invoice_id',
        'message_type',
        'metadata',
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at',
        'image_path'
    ];

    protected $dates = [
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at',
        'edited_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'read_at' => 'datetime',
        'receiver_deleted_at' => 'datetime',
        'sender_deleted_at' => 'datetime',
        'edited_at' => 'datetime',
    ];

    /* Relationships */

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function mentions()
    {
        return $this->belongsToMany(User::class, 'message_mentions', 'message_id', 'user_id');
    }

    public function reads()
    {
        return $this->hasMany(MessageRead::class, 'message_id');
    }

    /* Status Methods */

    public function isRead(): bool
    {
        return $this->read_at != null;
    }

    public function isReadBy($userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }

    public function markAsReadBy($userId)
    {
        if (!$this->isReadBy($userId)) {
            $this->reads()->create([
                'user_id' => $userId,
                'read_at' => now(),
            ]);
        }

        // Also update the main read_at if this is the receiver
        if ($this->receiver_id == $userId && !$this->read_at) {
            $this->update(['read_at' => now()]);
        }
    }

    public function getReadStatusAttribute()
    {
        if (!$this->read_at) {
            return 'sent'; // Single tick
        }
        return 'read'; // Double tick
    }

    public function isEdited()
    {
        return !is_null($this->edited_at);
    }

    public function canBeEdited()
    {
        if (auth()->user()->isAdmin()) {
            return true;
        }

        return $this->created_at->diffInHours(now()) <= 1 &&
               $this->sender_id === auth()->id();
    }

    public function getIsAdminAttribute()
    {
        return $this->sender->isAdmin();
    }

    /* Message Type Helpers */

    public function isInvoiceMessage(): bool
    {
        return $this->message_type === 'invoice_info';
    }

    public function isSystemMessage(): bool
    {
        return $this->message_type === 'system';
    }

    public function isImageMessage(): bool
    {
        return $this->message_type === 'image' || !empty($this->image_path);
    }

    public function isTextMessage(): bool
    {
        return $this->message_type === 'text';
    }

    /* Invoice Message Helpers */

    public function getInvoiceDetails()
    {
        if ($this->isInvoiceMessage() && $this->metadata) {
            return $this->metadata;
        }

        if ($this->invoice) {
            return [
                'invoice_number' => $this->invoice->number,
                'service_name' => $this->invoice->service->name ?? 'N/A',
                'total_price' => $this->invoice->total_price,
                'due_date' => $this->invoice->due_date?->format('Y-m-d'),
                'payment_status' => $this->invoice->payment_status,
            ];
        }

        return null;
    }
}

