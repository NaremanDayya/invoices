<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_employee_payment_id',
        'invoice_employee_id',
        'status',
        'reason',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->belongsTo(InvoiceEmployeePayment::class, 'invoice_employee_payment_id');
    }

    public function invoiceEmployee()
    {
        return $this->belongsTo(InvoiceEmployee::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isReturned()
    {
        return $this->status === 'returned';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }
}
