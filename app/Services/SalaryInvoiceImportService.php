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

            $invoice->update([
                'type' => 'salary_invoice',
                'base_price' => $totalSalaries,
                'total_price' => $totalNetSalaries,
                'employees_count' => count($employees)
            ]);

            DB::commit();

            Log::info('Salary Import: Success', [
                'invoice_id' => $invoiceId,
                'imported' => count($employees)
            ]);

            return [
                'success' => true,
                'message' => 'تم استيراد ' . count($employees) . ' موظف بنجاح',
                'data' => [
                    'employees_count' => count($employees),
                    'total_salaries' => $totalSalaries,
                    'total_deductions' => $totalDeductions,
                    'total_net_salaries' => $totalNetSalaries
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
                } else {
                    $employeeData[$field] = $value;
                }
            }
        }

        $employeeData['payment_method'] = 'monthly';
        $employeeData['payment_status'] = 'unpaid';
        $employeeData['paid_amount'] = 0;

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

    public function updateEmployeePaymentMethod($employeeId, $paymentMethod, $wpsPercentage = null)
    {
        try {
            $employee = InvoiceEmployee::findOrFail($employeeId);

            if ($paymentMethod === 'wps') {
                if (!$wpsPercentage) {
                    throw new \Exception('يجب تحديد نسبة WPS');
                }

                $maxWpsPercentage = Setting::get('wps_max_percentage', 70);

                if ($wpsPercentage > $maxWpsPercentage) {
                    throw new \Exception("نسبة WPS لا يمكن أن تتجاوز {$maxWpsPercentage}%");
                }

                $employee->payment_method = 'wps';
                $employee->wps_percentage = $wpsPercentage;
                $employee->wps_amount = ($employee->net_salary * $wpsPercentage) / 100;
            } else {
                $employee->payment_method = 'monthly';
                $employee->wps_percentage = null;
                $employee->wps_amount = null;
            }

            $employee->save();

            return [
                'success' => true,
                'message' => 'تم تحديث طريقة الدفع بنجاح',
                'employee' => $employee
            ];

        } catch (\Exception $e) {
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
