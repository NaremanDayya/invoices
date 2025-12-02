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
        'number',
        'amount',
        'reason',
        'issue_date',
        'is_main',
        'description',
        'is_active'
    ];

    protected $casts = [
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

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2);
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
                'credit_notes_count' => $invoice->credit_notes()->count(),
                'total_credit_notes' => $invoice->credit_notes()->sum('amount')
            ]);
        });

        static::deleted(function ($creditNote) {
            // Update invoice credit notes count and total
            $invoice = $creditNote->invoice;
            $invoice->update([
                'credit_notes_count' => $invoice->credit_notes()->count(),
                'total_credit_notes' => $invoice->credit_notes()->sum('amount')
            ]);
        });
    }
}
