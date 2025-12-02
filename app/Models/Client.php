<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];


    /**
     * Relationships
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Accessors
     */
    public function getActiveInvoicesCountAttribute()
    {
        return $this->invoices()->where('is_cancelled', false)->count();
    }

    public function getTotalPaidAmountAttribute()
    {
        return $this->invoices()->where('payment_status', 'paid')->sum('paid_amount');
    }

    public function getTotalPendingAmountAttribute()
    {
        return $this->invoices()->where('payment_status', '!=', 'paid')->sum('total_price');
    }

    public function getHasOverdueInvoicesAttribute()
    {
        return $this->invoices()->where('payment_status', 'overdue')->exists();
    }

    /**
     * Scopes
     */


    public function scopeWithOverdueInvoices($query)
    {
        return $query->whereHas('invoices', function ($q) {
            $q->where('payment_status', 'overdue');
        });
    }

    public function scopeWithPendingInvoices($query)
    {
        return $query->whereHas('invoices', function ($q) {
            $q->whereIn('payment_status', ['pending', 'late']);
        });
    }
}
