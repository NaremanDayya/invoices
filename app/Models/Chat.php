<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['admin_id', 'sales_rep_id', 'client_id', 'is_active'];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function salesRep()
    {
        return $this->belongsTo(User::class, 'sales_rep_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}

