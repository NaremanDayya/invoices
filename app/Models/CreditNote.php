<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CreditNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'created_by',
        'credit_note_number',
        'type',
        'previous_values',
        'new_values',
        'amount_difference',
        'previous_total',
        'new_total',
        'reason',
        'notes',
        'number',
        'amount',
        'issue_date',
        'is_main',
        'description',
        'is_active'
    ];

    protected $casts = [
        'previous_values' => 'array',
        'new_values' => 'array',
        'amount_difference' => 'decimal:2',
        'previous_total' => 'decimal:2',
        'new_total' => 'decimal:2',
        'amount' => 'decimal:2',
        'issue_date' => 'date',
        'is_main' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Relationships
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 0);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMain($query)
    {
        return $query->where('is_main', true);
    }

    public function scopeInternal($query)
    {
        return $query->where('type', 'internal');
    }

    public function scopeClient($query)
    {
        return $query->where('type', 'client');
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($creditNote) {
            // Update invoice credit notes count and total
            $invoice = $creditNote->invoice;
            $invoice->update([
                'credit_notes_count' => $invoice->creditNotes()->count(),
                'total_credit_notes' => $invoice->creditNotes()->sum('amount')
            ]);
        });

        static::deleted(function ($creditNote) {
            // Update invoice credit notes count and total
            $invoice = $creditNote->invoice;
            $invoice->update([
                'credit_notes_count' => $invoice->creditNotes()->count(),
                'total_credit_notes' => $invoice->creditNotes()->sum('amount')
            ]);
        });
    }
}
