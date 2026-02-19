<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SalaryPaymentService
{
    public function processPayments(Invoice $invoice, array $employeePayments, $userId)
    {
        if (!$invoice->isApproved()) {
            throw new \Exception('لا يمكن معالجة المدفوعات. الفاتورة غير معتمدة');
        }

        if (!$invoice->isSalaryInvoice()) {
            throw new \Exception('هذه الفاتورة ليست فاتورة رواتب');
        }

        DB::beginTransaction();

        try {
            $processedPayments = [];
            $errors = [];

            foreach ($employeePayments as $paymentData) {
                try {
                    $payment = $this->processEmployeePayment(
                        $paymentData['employee_id'],
                        $paymentData['amount'],
                        $paymentData['payment_type'],
                        $paymentData['payment_mode'],
                        $paymentData['notes'] ?? null,
                        $userId
                    );

                    $processedPayments[] = $payment;
                } catch (\Exception $e) {
                    $errors[] = [
                        'employee_id' => $paymentData['employee_id'],
                        'error' => $e->getMessage()
                    ];
                }
            }

            if (!empty($errors)) {
                DB::rollBack();
                return [
                    'success' => false,
                    'message' => 'فشلت بعض المدفوعات',
                    'errors' => $errors
                ];
            }

            DB::commit();

            Log::info('Salary payments processed successfully', [
                'invoice_id' => $invoice->id,
                'payments_count' => count($processedPayments),
                'processed_by' => $userId
            ]);

            return [
                'success' => true,
                'message' => 'تم معالجة المدفوعات بنجاح',
                'payments' => $processedPayments
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Failed to process salary payments', [
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    public function processEmployeePayment($employeeId, $amount, $paymentType, $paymentMode, $notes = null, $userId = null)
    {
        $employee = InvoiceEmployee::findOrFail($employeeId);

        if (!$employee->canReceivePayment()) {
            throw new \Exception('الموظف ' . $employee->employee_name . ' لا يمكنه استلام دفعات في الوقت الحالي');
        }

        if ($paymentType === 'full') {
            $amount = $employee->remaining_amount;
        }

        $employee->validatePaymentAmount($amount, $paymentMode);

        $payment = $employee->recordPayment($amount, $paymentType, $paymentMode, $notes, $userId);

        return $payment;
    }

    public function validatePaymentRequest(array $employeePayments)
    {
        $errors = [];

        foreach ($employeePayments as $index => $paymentData) {
            if (!isset($paymentData['employee_id'])) {
                $errors[] = "معرف الموظف مفقود في الصف {$index}";
            }

            if (!isset($paymentData['payment_type']) || !in_array($paymentData['payment_type'], ['full', 'partial'])) {
                $errors[] = "نوع الدفع غير صالح في الصف {$index}";
            }

            if (!isset($paymentData['payment_mode']) || !in_array($paymentData['payment_mode'], ['monthly', 'wps'])) {
                $errors[] = "وضع الدفع غير صالح في الصف {$index}";
            }

            if ($paymentData['payment_type'] === 'partial') {
                if (!isset($paymentData['amount']) || $paymentData['amount'] <= 0) {
                    $errors[] = "مبلغ الدفع الجزئي غير صالح في الصف {$index}";
                }
            }
        }

        if (!empty($errors)) {
            throw new \Exception('أخطاء في التحقق من صحة البيانات: ' . implode(', ', $errors));
        }

        return true;
    }

    public function getPaymentSummary(Invoice $invoice)
    {
        $employees = $invoice->invoiceEmployees;

        return [
            'total_employees' => $employees->count(),
            'unpaid_employees' => $employees->where('payment_status', 'unpaid')->count(),
            'partially_paid_employees' => $employees->where('payment_status', 'partially_paid')->count(),
            'paid_employees' => $employees->where('payment_status', 'paid')->count(),
            'total_salaries' => $employees->sum('total_salary'),
            'total_paid' => $employees->sum('total_paid'),
            'total_remaining' => $employees->sum('remaining_amount'),
            'wps_max_percentage' => Setting::get('wps_max_percentage', 70)
        ];
    }

    public function getEmployeePaymentDetails($employeeId)
    {
        $employee = InvoiceEmployee::with(['payments' => function($query) {
            $query->orderBy('payment_date', 'desc');
        }])->findOrFail($employeeId);

        return [
            'employee' => $employee,
            'payments' => $employee->payments,
            'can_receive_payment' => $employee->canReceivePayment(),
            'max_wps_payment' => $employee->max_wps_payment,
            'remaining_amount' => $employee->remaining_amount
        ];
    }

    public function calculatePaymentBreakdown($employeeId, $amount, $paymentMode)
    {
        $employee = InvoiceEmployee::findOrFail($employeeId);

        $breakdown = [
            'employee_name' => $employee->employee_name,
            'total_salary' => $employee->total_salary,
            'total_paid' => $employee->total_paid,
            'remaining_before' => $employee->remaining_amount,
            'payment_amount' => $amount,
            'remaining_after' => $employee->remaining_amount - $amount,
            'payment_mode' => $paymentMode
        ];

        if ($paymentMode === 'wps') {
            $netSalary = $employee->net_salary ?? $employee->total_salary;
            $remainingWpsAllowed = $employee->wps_accepted_amount ?? 0;
            
            $breakdown['wps_percentage'] = $netSalary > 0 ? ($amount / $netSalary) * 100 : 0;
            $breakdown['wps_max_percentage'] = Setting::get('wps_max_percentage', 70);
            $breakdown['wps_total_allowed'] = $employee->wps_amount ?? 0;
            $breakdown['wps_remaining_allowed'] = $remainingWpsAllowed;
            $breakdown['within_wps_limit'] = $amount <= $remainingWpsAllowed;
        }

        return $breakdown;
    }
}
