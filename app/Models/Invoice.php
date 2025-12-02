<?php
// app/Models/Invoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'number',
        'client_id',
        'service_id',
        'generation_date',
        'last_generation_date',
        'due_date',
        'allowed_late_pay_days',
        'approval_date',
        'payment_date',
        'total_workers',
        'total_supervisors',
        'total_managers',
        'total_users',
        'work_days',
        'daily_rate',
        'base_price',
        'tax_rate',
        'tax_amount',
        'total_price',
        'paid_amount',
        'amount_difference',
        'difference_type',
        'payment_status',
        'invoice_status',
        'is_cancelled',
        'cancelled_at',
        'cancellation_reason',
        'issue_delay_days',
        'payment_delay_days',
        'employee_count_difference',
        'work_days_difference',
        'difference_indicator',
        'credit_notes_count',
        'total_credit_notes',
        'notes',
        'additional_data'
    ];

    protected $casts = [
        'generation_date' => 'date',
        'last_generation_date' => 'date',
        'due_date' => 'date',
        'approval_date' => 'date',
        'payment_date' => 'date',
        'cancelled_at' => 'datetime',
        'base_price' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'amount_difference' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'total_credit_notes' => 'decimal:2',
        'additional_data' => 'array',
        'is_cancelled' => 'boolean'
    ];

    /**
     * Relationships
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function creditNotes()
    {
        return $this->hasMany(CreditNote::class);
    }

    public function paymentOrders()
    {
        return $this->hasMany(PaymentOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function additionalAmounts()
    {
        return $this->hasMany(AdditionalAmount::class);
    }

    public function invoiceEmployees()
    {
        return $this->hasMany(InvoiceEmployee::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class, 'invoice_employees')
            ->using(InvoiceEmployee::class)
            ->withPivot('work_days', 'daily_rate', 'total_amount', 'absence_days', 'absence_deduction', 'deductions', 'notes')
            ->withTimestamps();
    }

    /**
     * Accessors
     */
    public function getTotalWorkforceAttribute()
    {
        return $this->total_workers + $this->total_supervisors + $this->total_managers + $this->total_users;
    }

    public function getNetAmountAttribute()
    {
        return $this->total_price - $this->total_credit_notes;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->total_price - $this->paid_amount;
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && Carbon::now()->gt($this->due_date) && !$this->isFullyPaid();
    }

    public function getIsLateAttribute()
    {
        return $this->payment_delay_days > 0;
    }

    public function getHasDelayAttribute()
    {
        return $this->issue_delay_days > 0;
    }

    public function getHasCreditNotesAttribute()
    {
        return $this->credit_notes_count > 0;
    }

    public function getDifferenceIndicatorIconAttribute()
    {
        return $this->difference_indicator === 'green_up' ? '↑' : '↓';
    }

    public function getDifferenceIndicatorColorAttribute()
    {
        return $this->difference_indicator === 'green_up' ? 'text-green-600' : 'text-red-600';
    }

    public function getRequiresHrDetailsAttribute()
    {
        return $this->service->requires_hr_details ?? false;
    }

    /**
     * Methods
     */
    public function calculateFinancials()
    {
        // Calculate base price
        $this->base_price = $this->total_workforce * $this->work_days * $this->daily_rate;

        // Calculate tax
        $this->tax_amount = ($this->base_price * $this->tax_rate) / 100;

        // Calculate total with amount difference
        $difference = $this->difference_type === 'decrease' ? -$this->amount_difference : $this->amount_difference;
        $this->total_price = $this->base_price + $this->tax_amount + $difference;

        return $this;
    }

    public function calculateDelays()
    {
        // Calculate issue delay
        if ($this->generation_date && $this->last_generation_date) {
            $this->issue_delay_days = max(0, Carbon::parse($this->generation_date)
                ->diffInDays(Carbon::parse($this->last_generation_date)));
        }

        // Calculate payment delay
        if ($this->payment_date && $this->due_date) {
            $this->payment_delay_days = max(0, Carbon::parse($this->payment_date)
                ->diffInDays(Carbon::parse($this->due_date)));
        }

        return $this;
    }

    public function updatePaymentStatus()
    {
        if ($this->is_cancelled) {
            $this->invoice_status = 'cancelled';
            $this->payment_status = 'cancelled';
        } elseif ($this->isFullyPaid()) {
            $this->payment_status = 'paid';
            $this->invoice_status = 'completed';
        } elseif ($this->isOverdue) {
            $this->payment_status = 'overdue';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'late';
        } else {
            $this->payment_status = 'pending';
        }

        return $this;
    }

    public function isFullyPaid()
    {
        return $this->paid_amount >= $this->total_price;
    }

    public function isPartiallyPaid()
    {
        return $this->paid_amount > 0 && $this->paid_amount < $this->total_price;
    }

    public function isUnpaid()
    {
        return $this->paid_amount == 0;
    }

    public function cancel($reason)
    {
        $this->update([
            'is_cancelled' => true,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'invoice_status' => 'cancelled',
            'payment_status' => 'cancelled',
        ]);

        return $this;
    }

    public function addPayment($amount, $paymentData = [])
    {
        $this->increment('paid_amount', $amount);
        $this->updatePaymentStatus();
        $this->save();

        // Create payment record
        return $this->payments()->create(array_merge([
            'amount' => $amount,
            'payment_date' => now(),
            'number' => 'PAY-' . $this->number . '-' . ($this->payments()->count() + 1)
        ], $paymentData));
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending')
            ->where('is_cancelled', false);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid')
            ->where('is_cancelled', false);
    }

    public function scopeOverdue($query)
    {
        return $query->where('payment_status', 'overdue')
            ->where('is_cancelled', false);
    }

    public function scopeLate($query)
    {
        return $query->where('payment_status', 'late')
            ->where('is_cancelled', false);
    }

    public function scopeCancelled($query)
    {
        return $query->where('is_cancelled', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_cancelled', false);
    }

    public function scopeWithDelay($query)
    {
        return $query->where('issue_delay_days', '>', 0)
            ->orWhere('payment_delay_days', '>', 0);
    }

    public function scopeWithCreditNotes($query)
    {
        return $query->where('credit_notes_count', '>', 0);
    }

    public function scopeHumanResource($query)
    {
        return $query->whereHas('service', function ($q) {
            $q->where('service_type', 'human_resource')
                ->orWhere('requires_hr_details', true);
        });
    }

    /**
     * Boot method for automatic calculations
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoice) {
            $invoice->calculateFinancials();
            $invoice->calculateDelays();
            $invoice->updatePaymentStatus();
        });
    }
}
