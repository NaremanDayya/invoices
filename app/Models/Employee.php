<?php
// app/Models/Employee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'national_id',
        'employee_number',
        'wage_salary',
        'monthly_salary',
        'net_salary',
        'bank_name',
        'bank_account_number',
        'account_holder_name',
        'account_change_count',
        'is_active',
        'file_type',
        'work_days',
        'client_id',
        'phone_number',
        'iban',
        'salary_with_insurances',
    ];

    protected $casts = [
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'account_change_count' => 'integer'
    ];

    /**
     * Relationships
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_employees')
            ->using(InvoiceEmployee::class)
            ->withPivot('work_days', 'daily_rate', 'total_amount', 'absence_days', 'absence_deduction', 'deductions', 'notes')
            ->withTimestamps();
    }

    public function invoiceEmployees()
    {
        return $this->hasMany(InvoiceEmployee::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function additionalAmounts()
    {
        return $this->hasMany(AdditionalAmount::class);
    }

    /**
     * Accessors
     */
    public function getCurrentNetSalaryAttribute()
    {
        return $this->net_salary > 0 ? $this->net_salary : $this->gross_salary;
    }

    public function getHasAccountMismatchAttribute()
    {
        return $this->account_holder_name && $this->name && $this->account_holder_name !== $this->name;
    }

    public function getIsSalaryProtectedAttribute()
    {
        return $this->net_salary >= ($this->gross_salary * 0.5);
    }

    public function getTotalEarningsAttribute()
    {
        return $this->invoiceEmployees()->sum('total_amount');
    }

    public function getTotalDeductionsAttribute()
    {
        return $this->deductions()->sum('amount');
    }

    /**
     * Methods
     */
    public function incrementAccountChangeCount()
    {
        $this->increment('account_change_count');
        return $this;
    }

    public function updateAccount($accountData)
    {
        $this->update(array_merge($accountData, [
            'account_change_count' => $this->account_change_count + 1
        ]));

        return $this;
    }
    public function client()
    {
        return $this->belongsTo(Client::class);
    }
    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeWithAccountMismatch($query)
    {
        return $query->whereNotNull('account_holder_name')
            ->whereNotNull('name')
            ->whereRaw('account_holder_name != name');
    }

    public function scopeWithMultipleAccountChanges($query, $minChanges = 2)
    {
        return $query->where('account_change_count', '>=', $minChanges);
    }

    public function scopeByPosition($query, $position)
    {
        return $query->where('position', $position);
    }
}
