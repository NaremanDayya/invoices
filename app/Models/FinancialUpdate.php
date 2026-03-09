<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialUpdate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'payment_id',
        'client_id',
        'update_type',
        'title',
        'description',
        'amount',
        'update_date',
        'status',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'update_date' => 'date',
        'amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
