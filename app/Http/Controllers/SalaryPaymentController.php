<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Models\InvoiceEmployeePayment;
use App\Models\PaymentStatus;
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

    public function processPayments(Request $request, Invoice $invoice)
    {
        try {
            $validator = Validator::make($request->all(), [
                'payments' => 'required|array|min:1',
                'payments.*.employee_id' => 'required|integer',
                'payments.*.payment_type' => 'required|in:full,partial',
                'payments.*.payment_mode' => 'required|in:monthly,wps',
                'payments.*.amount' => 'required|numeric|min:0.01',
                'payments.*.notes' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'خطأ في البيانات المدخلة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $result = $this->paymentService->processPayments(
                $invoice,
                $request->input('payments'),
                auth()->id()
            );

            if ($result['success']) {
                return response()->json($result);
            }

            return response()->json($result, 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الدفع',
                'error_details' => $e->getMessage()
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
            $query->with(['createdBy', 'latestStatus'])->orderBy('payment_date', 'desc');
        }, 'invoice.client']);

        // Calculate late days: days since invoice generation_date, minus the accepted delay
        $acceptedDelayDays = (int) \App\Models\Setting::get('accepted_salary_delay_days', 0);
        $generationDate    = $invoice->generation_date
            ? \Carbon\Carbon::parse($invoice->generation_date)->startOfDay()
            : null;

        $lateDays = 0;
        $isLate   = false;
        if ($generationDate) {
            $deadline = $generationDate->copy()->addDays($acceptedDelayDays);
            if ($employee->payment_status !== 'paid') {
                $lateDays = max(0, $deadline->diffInDays(now(), false));
                $isLate   = $lateDays > 0;
            } elseif ($employee->last_payment_date) {
                $lastPay  = \Carbon\Carbon::parse($employee->last_payment_date)->startOfDay();
                $lateDays = max(0, $deadline->diffInDays($lastPay, false));
                $isLate   = $lateDays > 0;
            }
        }

        return view('salary-invoices.employee-payments', compact(
            'invoice', 'employee', 'lateDays', 'isLate', 'acceptedDelayDays'
        ));
    }

    public function updateSalaryPayStatus(Request $request, Invoice $invoice)
    {
        $validator = Validator::make($request->all(), [
            'employee_ids'    => 'required|array|min:1',
            'employee_ids.*'  => 'required|integer|exists:invoice_employees,id',
            'salary_pay_status' => 'required|in:full_paid,partial_paid,pended',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $newStatus    = $request->input('salary_pay_status');
            $employeeIds  = $request->input('employee_ids');

            $employees = InvoiceEmployee::whereIn('id', $employeeIds)
                ->where('invoice_id', $invoice->id)
                ->get();

            if ($employees->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على موظفين مرتبطين بهذه الفاتورة'
                ], 404);
            }

            // Track previously pended employees that are being changed to paid
            $previouslyPended = $employees->filter(fn($e) => $e->salary_pay_status === 'pended')->count();

            foreach ($employees as $employee) {
                $employee->salary_pay_status = $newStatus;
                $employee->save();
            }

            // Log to chat — pass context about previously-pended transition
            $this->chatLogger->logSalaryPayStatus(
                $invoice,
                $newStatus,
                $employees->count(),
                $previouslyPended
            );

            DB::commit();

            $labels = [
                'full_paid'    => 'مدفوع بالكامل',
                'partial_paid' => 'مدفوع جزئياً',
                'pended'       => 'معلق',
            ];

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة صرف الراتب إلى: ' . ($labels[$newStatus] ?? $newStatus),
                'updated_count' => $employees->count(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحديث: ' . $e->getMessage()
            ], 500);
        }
    }

    public function returnPayment(Request $request, InvoiceEmployeePayment $payment)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'status' => 'required|in:returned,cancelled',
        ]);

        try {
            DB::beginTransaction();

            $employee = $payment->invoiceEmployee;

            if (!$employee) {
                return response()->json(['success' => false, 'message' => 'الموظف غير موجود'], 404);
            }

            $existingStatus = PaymentStatus::where('invoice_employee_payment_id', $payment->id)
                ->whereIn('status', ['returned', 'cancelled'])
                ->first();

            if ($existingStatus) {
                return response()->json(['success' => false, 'message' => 'هذه الدفعة تم إرجاعها أو إلغاؤها مسبقاً'], 422);
            }

            PaymentStatus::create([
                'invoice_employee_payment_id' => $payment->id,
                'invoice_employee_id'         => $employee->id,
                'status'                      => $validated['status'],
                'reason'                      => $validated['reason'],
                'created_by'                  => auth()->id(),
            ]);

            $employee->total_paid     = max(0, $employee->total_paid - $payment->payment_amount);
            $employee->remaining_amount = $employee->total_salary - $employee->total_paid;
            $employee->updatePaymentStatus();
            $employee->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'returned' ? 'تم إرجاع الدفعة وإضافة المبلغ للرصيد' : 'تم إلغاء الدفعة وإضافة المبلغ للرصيد',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()], 500);
        }
    }
}
