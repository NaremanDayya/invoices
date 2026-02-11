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
        'employee_name',
        'project',
        'basic_salary',
        'bonuses',
        'monthly_deductions',
        'advance_deductions',
        'work_days',
        'work_days_count',
        'absence_days',
        'absence_days_count',
        'daily_rate',
        'total_amount',
        'absence_deduction',
        'deductions',
        'net_salary',
        'iban',
        'account_holder_name',
        'bank_name',
        'payment_method',
        'wps_percentage',
        'wps_amount',
        'payment_status',
        'payment_date',
        'paid_amount',
        'notes'
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'monthly_deductions' => 'decimal:2',
        'advance_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'daily_rate' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'absence_deduction' => 'decimal:2',
        'wps_percentage' => 'decimal:2',
        'wps_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
        'deductions' => 'array'
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

    public function calculateNetSalary()
    {
        $totalDeductions = $this->monthly_deductions + $this->advance_deductions;
        $this->net_salary = $this->basic_salary + $this->bonuses - $totalDeductions;
        return $this;
    }

    public function calculateWpsAmount()
    {
        if ($this->payment_method === 'wps' && $this->wps_percentage) {
            $this->wps_amount = ($this->net_salary * $this->wps_percentage) / 100;
        } else {
            $this->wps_amount = null;
        }
        return $this;
    }

    public function validateWpsPercentage()
    {
        if ($this->payment_method === 'wps' && $this->wps_percentage) {
            $maxWpsPercentage = Setting::where('key', 'wps_max_percentage')->value('value') ?? 70;
            
            if ($this->wps_percentage > $maxWpsPercentage) {
                throw new \Exception("WPS percentage cannot exceed {$maxWpsPercentage}%");
            }
        }
        return true;
    }

    public function markAsPaid($amount = null)
    {
        $paymentAmount = $amount ?? $this->net_salary;
        
        $this->paid_amount += $paymentAmount;
        $this->payment_date = now();
        
        if ($this->paid_amount >= $this->net_salary) {
            $this->payment_status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->payment_status = 'partially_paid';
        }
        
        $this->save();
        return $this;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->net_salary - $this->paid_amount;
    }

    public function getIsFullyPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    public function getIsPartiallyPaidAttribute()
    {
        return $this->payment_status === 'partially_paid';
    }

    public function getIsUnpaidAttribute()
    {
        return $this->payment_status === 'unpaid';
    }

    /**
     * Boot method for automatic calculations
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoiceEmployee) {
            if ($invoiceEmployee->basic_salary) {
                $invoiceEmployee->calculateNetSalary();
                $invoiceEmployee->calculateWpsAmount();
            } else {
                $invoiceEmployee->calculateTotalAmount();
            }
        });
    }
}
