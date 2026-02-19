<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalaryInvoiceImportService
{
    protected $expectedHeaders = [
        'ID',
        'اسم الموظف',
        'المشروع',
        'الراتب الأساسي',
        'نوع الراتب',
        'المكافآت',
        'خصومات الشهر',
        'خصومات السلف',
        'أيام العمل',
        'أيام الغياب',
        'صافي الراتب',
        'رقم الآيبان',
        'اسم صاحب الحساب',
        'البنك'
    ];

    protected $headerMapping = [
        'ID' => 'id',
        'اسم الموظف' => 'employee_name',
        'المشروع' => 'project',
        'الراتب الأساسي' => 'basic_salary',
        'نوع الراتب' => 'salary_type',
        'المكافآت' => 'bonuses',
        'خصومات الشهر' => 'monthly_deductions',
        'خصومات السلف' => 'advance_deductions',
        'أيام العمل' => 'work_days_count',
        'أيام الغياب' => 'absence_days_count',
        'صافي الراتب' => 'net_salary',
        'رقم الآيبان' => 'iban',
        'اسم صاحب الحساب' => 'account_holder_name',
        'البنك' => 'bank_name'
    ];

    public function import($filePath, $invoiceId)
    {
        try {
            DB::beginTransaction();

            $invoice = Invoice::findOrFail($invoiceId);

            if ($invoice->invoiceEmployees()->exists()) {
                Log::warning('Salary Import: Duplicate import attempt', ['invoice_id' => $invoiceId]);
                throw new \Exception('هذه الفاتورة تحتوي بالفعل على موظفين مستوردين. يرجى حذفهم أولاً.');
            }

            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            if (empty($rows)) {
                Log::warning('Salary Import: Empty file', ['invoice_id' => $invoiceId]);
                throw new \Exception('الملف فارغ');
            }

            $headers = array_map('trim', $rows[0]);
            $this->validateHeaders($headers);

            $employees = [];
            $totalSalaries = 0;
            $totalDeductions = 0;
            $totalNetSalaries = 0;

            for ($i = 1; $i < count($rows); $i++) {
                $row = $rows[$i];

                if ($this->isEmptyRow($row)) {
                    continue;
                }

                $employeeData = $this->mapRowToEmployee($headers, $row, $invoice->id);

                try {
                    $this->validateEmployeeData($employeeData);
                } catch (\Exception $e) {
                    Log::warning('Salary Import: Row validation failed', [
                        'row' => $i + 1,
                        'error' => $e->getMessage()
                    ]);
                    continue;
                }

                $employee = InvoiceEmployee::create($employeeData);

                $employees[] = $employee;
                $totalSalaries += $employee->basic_salary;
                $totalDeductions += ($employee->monthly_deductions + $employee->advance_deductions);
                $totalNetSalaries += $employee->net_salary;
            }

            if (empty($employees)) {
                Log::error('Salary Import: No employees imported', [
                    'invoice_id' => $invoiceId
                ]);
                throw new \Exception('لم يتم العثور على بيانات موظفين صالحة في الملف');
            }

            // Validate total work days against invoice work days
            $totalEmployeeWorkDays = 0;
            foreach ($employees as $employee) {
                $totalEmployeeWorkDays += ($employee->work_days_count ?? 0);
            }

            $invoiceWorkDays = $invoice->work_days ?? 0;

            Log::info('Salary Import: Work days validation', [
                'invoice_id' => $invoiceId,
                'invoice_work_days' => $invoiceWorkDays,
                'total_employee_work_days' => $totalEmployeeWorkDays,
            ]);

            if ($invoiceWorkDays > 0 && $totalEmployeeWorkDays > $invoiceWorkDays) {
                throw new \Exception("إجمالي أيام عمل الموظفين ({$totalEmployeeWorkDays}) يتجاوز أيام عمل الفاتورة ({$invoiceWorkDays}). الحد الأقصى المسموح هو {$invoiceWorkDays} يوم عمل.");
            }

            // Calculate extra paid days (remaining days that can be used next month)
            $extraPaidDays = ($invoiceWorkDays > 0) ? max(0, $invoiceWorkDays - $totalEmployeeWorkDays) : 0;

            $invoice->update([
                'type' => 'salary_invoice',
                'base_price' => $totalSalaries,
                'total_price' => $totalNetSalaries,
                'employees_count' => count($employees),
                'work_days_difference' => $extraPaidDays,
            ]);

            DB::commit();

            Log::info('Salary Import: Success', [
                'invoice_id' => $invoiceId,
                'imported' => count($employees),
                'extra_paid_days' => $extraPaidDays,
            ]);

            $message = 'تم استيراد ' . count($employees) . ' موظف بنجاح';
            if ($extraPaidDays > 0) {
                $message .= ". يوجد {$extraPaidDays} يوم عمل إضافي مدفوع يمكن ترحيله للشهر القادم.";
            }

            return [
                'success' => true,
                'message' => $message,
                'data' => [
                    'employees_count' => count($employees),
                    'total_salaries' => $totalSalaries,
                    'total_deductions' => $totalDeductions,
                    'total_net_salaries' => $totalNetSalaries,
                    'invoice_work_days' => $invoiceWorkDays,
                    'total_employee_work_days' => $totalEmployeeWorkDays,
                    'extra_paid_days' => $extraPaidDays,
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Salary Import: Critical error', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'فشل الاستيراد: ' . $e->getMessage()
            ];
        }
    }

    protected function validateHeaders($headers)
    {
        $missingHeaders = array_diff($this->expectedHeaders, $headers);

        if (!empty($missingHeaders)) {
            throw new \Exception('الملف يفتقد العناوين التالية: ' . implode(', ', $missingHeaders));
        }
    }

    protected function mapRowToEmployee($headers, $row, $invoiceId)
    {
        $employeeData = ['invoice_id' => $invoiceId];

        foreach ($headers as $index => $header) {
            $header = trim($header);

            if (isset($this->headerMapping[$header])) {
                $field = $this->headerMapping[$header];
                $value = isset($row[$index]) ? trim($row[$index]) : null;

                if (in_array($field, ['basic_salary', 'bonuses', 'monthly_deductions', 'advance_deductions', 'net_salary'])) {
                    $employeeData[$field] = $this->parseDecimal($value);
                } elseif (in_array($field, ['work_days_count', 'absence_days_count'])) {
                    $employeeData[$field] = $this->parseInt($value);
                } elseif ($field === 'salary_type') {
                    $employeeData[$field] = $this->parseSalaryType($value);
                } else {
                    $employeeData[$field] = $value;
                }
            }
        }

        $netSalary = $employeeData['net_salary'] ?? 0;
        $salaryType = $employeeData['salary_type'] ?? 'monthly';

        $employeeData['total_salary'] = $netSalary;
        $employeeData['paid_amount'] = 0;
        $employeeData['total_paid'] = 0;
        $employeeData['remaining_amount'] = $netSalary;
        $employeeData['payment_method'] = $salaryType;
        $employeeData['payment_status'] = 'unpaid';
        
        if ($salaryType === 'wps') {
            $wpsMaxPercentage = Setting::get('wps_max_percentage', 70);
            $maxWpsAmount = ($netSalary * $wpsMaxPercentage) / 100;
            $employeeData['wps_amount'] = $maxWpsAmount;
            $employeeData['wps_accepted_amount'] = $maxWpsAmount;
            $employeeData['monthly_amount'] = $netSalary - $maxWpsAmount;
            $employeeData['wps_percentage_applied'] = $wpsMaxPercentage;
        } else {
            $employeeData['wps_amount'] = 0;
            $employeeData['wps_accepted_amount'] = 0;
            $employeeData['monthly_amount'] = $netSalary;
            $employeeData['wps_percentage_applied'] = null;
        }

        return $employeeData;
    }

    protected function validateEmployeeData($data)
    {
        $validator = Validator::make($data, [
            'employee_name' => 'required|string|max:255',
            'basic_salary' => 'required|numeric|min:0',
            'net_salary' => 'required|numeric|min:0',
            'work_days_count' => 'required|integer|min:0',
            'absence_days_count' => 'required|integer|min:0',
            'iban' => 'nullable|string|regex:/^SA[0-9]{22}$/',
        ]);

        if ($validator->fails()) {
            throw new \Exception('بيانات الموظف ' . ($data['employee_name'] ?? 'غير معروف') . ' غير صالحة: ' . implode(', ', $validator->errors()->all()));
        }
    }

    protected function isEmptyRow($row)
    {
        return empty(array_filter($row, function($value) {
            return !is_null($value) && trim($value) !== '';
        }));
    }

    protected function parseDecimal($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        $value = str_replace(',', '', $value);
        return (float) $value;
    }

    protected function parseInt($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }

        return (int) $value;
    }

    protected function parseSalaryType($value)
    {
        if (is_null($value) || $value === '') {
            return 'monthly';
        }

        $value = trim($value);
        
        if ($value === 'شهري' || strtolower($value) === 'monthly') {
            return 'monthly';
        }
        
        if ($value === 'حماية أجور' || $value === 'WPS' || strtolower($value) === 'wps') {
            return 'wps';
        }

        return 'monthly';
    }

    public function updateEmployeePaymentMethod($employeeId, $paymentMethod, $wpsAmount = null)
    {
        try {
            DB::beginTransaction();
            
            $employee = InvoiceEmployee::findOrFail($employeeId);

            if ($paymentMethod === 'wps') {
                if ($wpsAmount === null || $wpsAmount === '') {
                    throw new \Exception('يجب تحديد مبلغ WPS');
                }

                $wpsAmount = (float) $wpsAmount;
                
                $employee->payment_method = 'wps';
                $employee->wps_amount = $wpsAmount;
                
                $employee->validateWpsAmount();
                $employee->calculateWpsAmount();

                // Set wps_accepted_amount minus any already-paid WPS
                $alreadyPaidWps = $employee->payments()->where('payment_mode', 'wps')->sum('payment_amount');
                $employee->wps_accepted_amount = max(0, $wpsAmount - $alreadyPaidWps);
            } else {
                $employee->payment_method = 'monthly';
                $employee->wps_amount = 0;
                $employee->wps_accepted_amount = 0;
                $employee->monthly_amount = $employee->net_salary;
                $employee->wps_percentage_applied = null;
            }

            $employee->save();
            
            DB::commit();

            return [
                'success' => true,
                'message' => 'تم تحديث طريقة الدفع بنجاح',
                'employee' => $employee->fresh()
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function paySelectedEmployees($employeeIds, $paymentData = [])
    {
        try {
            DB::beginTransaction();

            $employees = InvoiceEmployee::whereIn('id', $employeeIds)
                ->where('payment_status', '!=', 'paid')
                ->get();

            if ($employees->isEmpty()) {
                throw new \Exception('لم يتم العثور على موظفين غير مدفوعين');
            }

            $totalPaid = 0;
            $paidEmployees = [];

            foreach ($employees as $employee) {
                if ($employee->payment_status === 'paid') {
                    continue;
                }

                $paymentAmount = $employee->remaining_amount;
                $employee->markAsPaid($paymentAmount);

                $totalPaid += $paymentAmount;
                $paidEmployees[] = $employee;
            }

            $invoice = $employees->first()->invoice;
            $invoice->increment('paid_amount', $totalPaid);

            $totalEmployees = $invoice->invoiceEmployees()->count();
            $paidEmployeesCount = $invoice->invoiceEmployees()->where('payment_status', 'paid')->count();

            if ($paidEmployeesCount >= $totalEmployees) {
                $invoice->update(['payment_status' => 'paid']);
            } elseif ($paidEmployeesCount > 0) {
                $invoice->update(['payment_status' => 'partially_paid']);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'تم دفع رواتب ' . count($paidEmployees) . ' موظف بنجاح',
                'data' => [
                    'paid_employees_count' => count($paidEmployees),
                    'total_paid' => $totalPaid
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => 'فشل الدفع: ' . $e->getMessage()
            ];
        }
    }
}
