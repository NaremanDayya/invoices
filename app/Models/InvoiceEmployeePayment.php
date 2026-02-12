<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvoiceEmployeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_employee_id',
        'invoice_id',
        'payment_amount',
        'payment_type',
        'payment_mode',
        'payment_date',
        'created_by',
        'notes'
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function invoiceEmployee()
    {
        return $this->belongsTo(InvoiceEmployee::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isFullPayment()
    {
        return $this->payment_type === 'full';
    }

    public function isPartialPayment()
    {
        return $this->payment_type === 'partial';
    }

    public function isWpsPayment()
    {
        return $this->payment_mode === 'wps';
    }

    public function isMonthlyPayment()
    {
        return $this->payment_mode === 'monthly';
    }
}
