<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Services\ChatActivityLogger;
use App\Services\SalaryPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SalaryPaymentController extends Controller
{
    protected $paymentService;
    protected $chatLogger;

    public function __construct(SalaryPaymentService $paymentService, ChatActivityLogger $chatLogger)
    {
        $this->paymentService = $paymentService;
        $this->chatLogger = $chatLogger;
    }

    public function processPayments(Invoice $invoice, array $payments, int $userId)
    {
        DB::beginTransaction();

        try {
            $processedPayments = [];

            foreach ($payments as $paymentData) {
                $employee = InvoiceEmployee::where('invoice_id', $invoice->id)
                    ->where('id', $paymentData['employee_id'])
                    ->firstOrFail();

                // Calculate current WPS paid amount for this employee
                $currentWpsPaid = InvoicePayment::where('invoice_employee_id', $employee->id)
                    ->where('payment_mode', 'wps')
                    ->sum('amount');

                $maxWpsAllowed = ($employee->total_salary * $this->wpsMaxPercentage) / 100;
                $remainingWpsAllowed = $maxWpsAllowed - $currentWpsPaid;

                // Validate WPS payment
                if ($paymentData['payment_mode'] === 'wps') {
                    if ($paymentData['amount'] > $remainingWpsAllowed) {
                        throw new \Exception(
                            "الموظف {$employee->employee_name}: المبلغ المطلوب ({$paymentData['amount']}) يتجاوز الحد المتبقي المسموح به لـ WPS ({$remainingWpsAllowed})"
                        );
                    }
                }

                // Process the payment
                $payment = $this->createPayment($employee, $paymentData, $userId);
                $processedPayments[] = $payment;

                // Update employee's wps_paid field if you store it
                if ($paymentData['payment_mode'] === 'wps') {
                    $employee->wps_paid = ($employee->wps_paid ?? 0) + $paymentData['amount'];
                    $employee->save();
                }
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'تم معالجة الدفعات بنجاح',
                'payments' => $processedPayments
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
    public function getPaymentSummary($invoiceId)
    {
        try {
            $invoice = Invoice::with('invoiceEmployees')->findOrFail($invoiceId);

            if (!$invoice->isSalaryInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الفاتورة ليست فاتورة رواتب'
                ], 422);
            }

            $summary = $this->paymentService->getPaymentSummary($invoice);

            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEmployeePaymentDetails($employeeId)
    {
        try {
            $details = $this->paymentService->getEmployeePaymentDetails($employeeId);

            return response()->json([
                'success' => true,
                'data' => $details
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calculatePaymentBreakdown(Request $request, $employeeId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:0.01',
                'payment_mode' => 'required|in:monthly,wps'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            $breakdown = $this->paymentService->calculatePaymentBreakdown(
                $employeeId,
                $request->amount,
                $request->payment_mode
            );

            return response()->json([
                'success' => true,
                'breakdown' => $breakdown
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEmployeePaymentHistory($employeeId)
    {
        try {
            $employee = InvoiceEmployee::with(['payments' => function($query) {
                $query->with('createdBy')->orderBy('payment_date', 'desc');
            }])->findOrFail($employeeId);

            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->employee_name,
                    'total_salary' => $employee->total_salary,
                    'total_paid' => $employee->total_paid,
                    'remaining_amount' => $employee->remaining_amount,
                    'payment_status' => $employee->payment_status
                ],
                'payments' => $employee->payments
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showEmployeePayments(Invoice $invoice, InvoiceEmployee $employee)
    {
        if ($employee->invoice_id !== $invoice->id) {
            abort(404);
        }

        $employee->load(['payments' => function($query) {
            $query->with('createdBy')->orderBy('payment_date', 'desc');
        }, 'invoice.client']);

        return view('salary-invoices.employee-payments', compact('invoice', 'employee'));
    }
}
