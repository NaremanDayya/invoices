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
        'monthly_amount',
        'wps_percentage_applied',
        'payment_status',
        'payment_date',
        'paid_amount',
        'total_salary',
        'total_paid',
        'remaining_amount',
        'salary_type',
        'last_payment_date',
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
        'monthly_amount' => 'decimal:2',
        'wps_percentage_applied' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'total_salary' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'payment_date' => 'date',
        'last_payment_date' => 'date',
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

    public function payments()
    {
        return $this->hasMany(InvoiceEmployeePayment::class);
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
        if ($this->payment_method === 'wps' && $this->wps_amount !== null) {
            $this->monthly_amount = $this->net_salary - $this->wps_amount;
            
            if ($this->net_salary > 0) {
                $this->wps_percentage_applied = ($this->wps_amount / $this->net_salary) * 100;
            } else {
                $this->wps_percentage_applied = 0;
            }
        } else {
            $this->wps_amount = 0;
            $this->monthly_amount = $this->net_salary;
            $this->wps_percentage_applied = null;
        }
        return $this;
    }

    public function validateWpsAmount()
    {
        if ($this->payment_method === 'wps') {
            if ($this->wps_amount < 0) {
                throw new \Exception('مبلغ WPS يجب أن يكون أكبر من أو يساوي صفر');
            }
            
            $maxWpsPercentage = Setting::get('wps_max_percentage', 70);
            $maxWpsAmount = ($this->net_salary * $maxWpsPercentage) / 100;
            
            if ($this->wps_amount > $maxWpsAmount) {
                throw new \Exception("مبلغ WPS لا يمكن أن يتجاوز {$maxWpsAmount} ريال (الحد الأقصى {$maxWpsPercentage}% من صافي الراتب)");
            }
            
            if ($this->wps_amount > $this->net_salary) {
                throw new \Exception('مبلغ WPS لا يمكن أن يتجاوز صافي الراتب');
            }
            
            $this->monthly_amount = $this->net_salary - $this->wps_amount;
            
            if ($this->monthly_amount < 0) {
                throw new \Exception('المبلغ الشهري يجب أن يكون أكبر من أو يساوي صفر');
            }
        }
        return true;
    }
    
    public function getMaxWpsAmountAttribute()
    {
        $maxWpsPercentage = Setting::get('wps_max_percentage', 70);
        return ($this->net_salary * $maxWpsPercentage) / 100;
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

    public function getRemainingBalanceAttribute()
    {
        return max(0, $this->total_salary - $this->total_paid);
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

    public function canReceivePayment()
    {
        return $this->remaining_amount > 0 && $this->invoice->isApproved();
    }

    public function validatePaymentAmount($amount, $paymentMode = 'monthly')
    {
        if ($amount <= 0) {
            throw new \Exception('مبلغ الدفع يجب أن يكون أكبر من صفر');
        }

        if ($amount > $this->remaining_amount) {
            throw new \Exception('مبلغ الدفع لا يمكن أن يتجاوز المبلغ المتبقي (' . number_format($this->remaining_amount, 2) . ' ريال)');
        }

        if ($paymentMode === 'wps') {
            $maxWpsPercentage = Setting::get('wps_max_percentage', 70);
            $maxWpsAmount = ($this->total_salary * $maxWpsPercentage) / 100;
            
            if ($amount > $maxWpsAmount) {
                throw new \Exception("مبلغ WPS لا يمكن أن يتجاوز {$maxWpsPercentage}% من إجمالي الراتب (الحد الأقصى: " . number_format($maxWpsAmount, 2) . " ريال)");
            }
        }

        return true;
    }

    public function recordPayment($amount, $paymentType, $paymentMode, $notes = null, $userId = null)
    {
        $this->validatePaymentAmount($amount, $paymentMode);

        $payment = $this->payments()->create([
            'invoice_id' => $this->invoice_id,
            'payment_amount' => $amount,
            'payment_type' => $paymentType,
            'payment_mode' => $paymentMode,
            'payment_date' => now(),
            'created_by' => $userId,
            'notes' => $notes
        ]);

        $this->total_paid += $amount;
        $this->remaining_amount = $this->total_salary - $this->total_paid;
        $this->last_payment_date = now();
        
        $this->updatePaymentStatus();
        $this->save();

        return $payment;
    }

    public function updatePaymentStatus()
    {
        if ($this->remaining_amount <= 0) {
            $this->payment_status = 'paid';
        } elseif ($this->total_paid > 0) {
            $this->payment_status = 'partially_paid';
        } else {
            $this->payment_status = 'unpaid';
        }
    }

    public function getMaxWpsPaymentAttribute()
    {
        $maxWpsPercentage = Setting::get('wps_max_percentage', 70);
        $maxByPercentage = ($this->total_salary * $maxWpsPercentage) / 100;
        return min($maxByPercentage, $this->remaining_amount);
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
                
                if ($invoiceEmployee->isDirty('net_salary') && !$invoiceEmployee->isDirty('total_salary')) {
                    $invoiceEmployee->total_salary = $invoiceEmployee->net_salary;
                }
                
                if (!$invoiceEmployee->isDirty('remaining_amount')) {
                    $invoiceEmployee->remaining_amount = $invoiceEmployee->total_salary - $invoiceEmployee->total_paid;
                }
            } else {
                $invoiceEmployee->calculateTotalAmount();
            }
        });
    }
}
