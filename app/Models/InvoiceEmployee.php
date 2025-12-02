<?php
// app/Models/InvoiceEmployee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class InvoiceEmployee extends Pivot
{
    protected $table = 'invoice_employees';

    protected $fillable = [
        'invoice_id',
        'employee_id',
        'work_days',
        'daily_rate',
        'total_amount',
        'absence_days',
        'absence_deduction',
        'deductions',
        'notes'
    ];


    /**
     * Relationships
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Accessors
     */
    public function getActualWorkDaysAttribute()
    {
        return $this->work_days - $this->absence_days;
    }

    public function getGrossAmountAttribute()
    {
        return $this->work_days * $this->daily_rate;
    }

    public function getTotalDeductionsAmountAttribute()
    {
        $deductions = $this->deductions ?? [];
        return $this->absence_deduction + array_sum(array_column($deductions, 'amount'));
    }

    /**
     * Methods
     */
    public function calculateTotalAmount()
    {
        $grossAmount = $this->work_days * $this->daily_rate;
        $this->total_amount = $grossAmount - $this->absence_deduction - $this->total_deductions_amount;
        return $this;
    }

    /**
     * Boot method for automatic calculations
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoiceEmployee) {
            $invoiceEmployee->calculateTotalAmount();
        });
    }
}
