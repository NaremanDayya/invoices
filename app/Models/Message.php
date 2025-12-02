<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $fillable = ['receiver_id', 'edited_at',  'sender_id', 'message', 'is_admin', 'conversation_id', 'read_at', 'receiver_deleted_at', 'sender_deleted_at'];
    protected $dates = [
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at',
	'edited_at',
    ];

    /* relationship */

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function isRead(): bool
    {

        return $this->read_at != null;
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
public function Conversation()
{
    return $this->belongsTo(Conversation::class);
}
}
