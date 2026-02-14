<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Services\ChatActivityLogger;
use App\Services\SalaryPaymentService;
use Illuminate\Http\Request;
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

    public function processPayments(Request $request, $invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            if (!$invoice->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن معالجة المدفوعات. الفاتورة غير معتمدة'
                ], 422);
            }

            $validator = Validator::make($request->all(), [
                'payments' => 'required|array|min:1',
                'payments.*.employee_id' => 'required|exists:invoice_employees,id',
                'payments.*.payment_type' => 'required|in:full,partial',
                'payments.*.payment_mode' => 'required|in:monthly,wps',
                'payments.*.amount' => 'required_if:payments.*.payment_type,partial|numeric|min:0.01',
                'payments.*.notes' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات غير صالحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $this->paymentService->validatePaymentRequest($request->payments);

            $result = $this->paymentService->processPayments(
                $invoice,
                $request->payments,
                auth()->id()
            );

            if ($result['success']) {
                // Log bulk employee payments to chat
                $paymentsCount = count($result['payments']);
                $totalAmount = collect($result['payments'])->sum('amount');
                $this->chatLogger->logBulkEmployeePayments($invoice, $paymentsCount, $totalAmount);

                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'payments' => $result['payments'],
                    'summary' => $this->paymentService->getPaymentSummary($invoice)
                ]);
            } else {
                return response()->json($result, 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة المدفوعات: ' . $e->getMessage()
            ], 500);
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
