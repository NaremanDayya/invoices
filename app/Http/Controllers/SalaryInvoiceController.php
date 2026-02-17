<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Models\Setting;
use App\Services\ChatActivityLogger;
use App\Services\SalaryInvoiceImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SalaryInvoiceController extends Controller
{
    protected $importService;
    protected $chatLogger;

    public function __construct(SalaryInvoiceImportService $importService, ChatActivityLogger $chatLogger)
    {
        $this->importService = $importService;
        $this->chatLogger = $chatLogger;
    }

    public function importEmployees(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            Log::info('Salary Import Controller: Starting import', [
                'invoice_id' => $request->invoice_id,
                'file_name' => $request->file('excel_file')->getClientOriginalName()
            ]);

            $invoice = Invoice::findOrFail($request->invoice_id);

            if ($invoice->invoiceEmployees()->exists()) {
                Log::warning('Salary Import Controller: Duplicate import attempt', [
                    'invoice_id' => $invoice->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الفاتورة تحتوي بالفعل على موظفين مستوردين. يرجى حذفهم أولاً.'
                ], 422);
            }

            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            Log::info('Salary Import Controller: Calling import service', [
                'file_path' => $filePath
            ]);

            $result = $this->importService->import($filePath, $invoice->id);

            Log::info('Salary Import Controller: Import result', [
                'success' => $result['success'],
                'message' => $result['message'] ?? 'No message'
            ]);

            if ($result['success']) {
                // Log employee import to chat
                $employeesCount = $result['data']['employees_count'] ?? 0;
                $summary = [
                    'total_salaries' => $result['data']['total_salaries'] ?? 0,
                    'wps_count' => $result['data']['wps_count'] ?? 0,
                    'monthly_count' => $result['data']['monthly_count'] ?? 0,
                ];
                $this->chatLogger->logEmployeesImported($invoice, $employeesCount, $summary);

                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data'] ?? []
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('Salary Import Controller: Exception caught', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استيراد الموظفين: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showEmployees($invoiceId)
    {
        $invoice = Invoice::with(['invoiceEmployees' => function($query) {
            $query->orderBy('id', 'desc');
        }])->findOrFail($invoiceId);

        if (!$invoice->isSalaryInvoice()) {
            abort(404, 'هذه الفاتورة ليست فاتورة رواتب');
        }

        $filter = request('filter', 'all');
        $search = request('search', '');
        $employees = $invoice->invoiceEmployees;

        // Apply search filter
        if (!empty($search)) {
            $employees = $employees->filter(function($emp) use ($search) {
                return stripos($emp->employee_name, $search) !== false ||
                       stripos($emp->project, $search) !== false ||
                       stripos($emp->id, $search) !== false;
            });
        }

        // Apply status/type filter
        if ($filter !== 'all') {
            $employees = $employees->filter(function($emp) use ($filter) {
                switch($filter) {
                    case 'paid':
                        return $emp->payment_status === 'paid';
                    case 'partially_paid':
                        return $emp->payment_status === 'partially_paid';
                    case 'unpaid':
                        return $emp->payment_status === 'unpaid';
                    case 'wps':
                        return $emp->salary_type === 'wps';
                    case 'monthly':
                        return $emp->salary_type === 'monthly';
                    default:
                        return true;
                }
            });
        }

        $summary = [
            'total_employees' => $invoice->invoiceEmployees->count(),
            'paid_employees' => $invoice->invoiceEmployees->where('payment_status', 'paid')->count(),
            'partially_paid_employees' => $invoice->invoiceEmployees->where('payment_status', 'partially_paid')->count(),
            'unpaid_employees' => $invoice->invoiceEmployees->where('payment_status', 'unpaid')->count(),
            'wps_employees' => $invoice->invoiceEmployees->where('salary_type', 'wps')->count(),
            'monthly_employees' => $invoice->invoiceEmployees->where('salary_type', 'monthly')->count(),
            'total_salaries' => $invoice->invoiceEmployees->sum('total_salary'),
            'total_paid' => $invoice->invoiceEmployees->sum('total_paid'),
            'total_remaining' => $invoice->invoiceEmployees->sum('remaining_amount')
        ];

        // Get all salary invoices for filtering dropdown
        $allSalaryInvoices = Invoice::where('type', 'salary_invoice')
            ->orderBy('generation_date', 'desc')
            ->get();

        return view('salary-invoices.employees', compact('invoice', 'employees', 'summary', 'filter', 'search', 'allSalaryInvoices'));
    }

    public function getEmployees($invoiceId)
    {
        try {
            $invoice = Invoice::with('invoiceEmployees')->findOrFail($invoiceId);

            if (!$invoice->isSalaryInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الفاتورة ليست فاتورة رواتب'
                ], 422);
            }

            $employees = $invoice->invoiceEmployees;

            $summary = [
                'total_employees' => $employees->count(),
                'paid_employees' => $employees->where('payment_status', 'paid')->count(),
                'unpaid_employees' => $employees->where('payment_status', 'unpaid')->count(),
                'total_salaries' => $employees->sum('basic_salary'),
                'total_deductions' => $employees->sum(function($e) {
                    return $e->monthly_deductions + $e->advance_deductions;
                }),
                'total_net_salaries' => $employees->sum('net_salary'),
                'total_paid_amount' => $employees->sum('paid_amount'),
                'remaining_unpaid' => $employees->sum('net_salary') - $employees->sum('paid_amount')
            ];

            return response()->json([
                'success' => true,
                'employees' => $employees,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updatePaymentMethod(Request $request, $employee)
    {
        Log::info('Payment Method Update: Request received', [
            'employee_id' => $employee,
            'method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'all_data' => $request->all(),
            'json_data' => $request->json()->all(),
            'raw_body' => $request->getContent()
        ]);

        try {
            $validated = $request->validate([
                'payment_method' => 'required|in:wps,monthly',
                'wps_amount' => 'required_if:payment_method,wps|nullable|numeric|min:0'
            ]);

            Log::info('Payment Method Update: Validation passed', [
                'validated' => $validated
            ]);

            $result = $this->importService->updateEmployeePaymentMethod(
                $employee,
                $request->input('payment_method'),
                $request->input('wps_amount')
            );

            Log::info('Payment Method Update: Service result', [
                'success' => $result['success'],
                'message' => $result['message'] ?? 'No message'
            ]);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'employee' => $result['employee']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 422);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('Payment Method Update: Validation failed', [
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Payment Method Update: Exception', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function paySelectedEmployees(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:invoice_employees,id'
        ]);

        try {
            $result = $this->importService->paySelectedEmployees($request->employee_ids);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'data' => $result['data']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteEmployee($employeeId)
    {
        try {
            $employee = InvoiceEmployee::findOrFail($employeeId);

            if ($employee->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف موظف تم دفع راتبه'
                ], 422);
            }

            $invoice = $employee->invoice;
            $employee->delete();

            $this->importService->recalculateInvoiceTotals($invoice);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الموظف بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearAllEmployees($invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            // Check if any employees have been paid
            $paidCount = $invoice->invoiceEmployees()->where('payment_status', 'paid')->count();
            if ($paidCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "لا يمكن حذف الموظفين. يوجد {$paidCount} موظف تم دفع رواتبهم"
                ], 422);
            }

            // Delete all employees
            $deletedCount = $invoice->invoiceEmployees()->delete();

            // Reset invoice type to regular
            $invoice->update([
                'type' => 'regular',
                'employees_count' => 0
            ]);

            Log::info('Salary Import: All employees cleared', [
                'invoice_id' => $invoiceId,
                'deleted_count' => $deletedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedCount} موظف بنجاح. يمكنك الآن إعادة الاستيراد"
            ]);

        } catch (\Exception $e) {
            Log::error('Salary Import: Error clearing employees', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteAllEmployees($invoiceId)
    {
        try {
            $invoice = Invoice::findOrFail($invoiceId);

            $paidEmployees = $invoice->invoiceEmployees()->where('payment_status', 'paid')->count();

            if ($paidEmployees > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف الموظفين. يوجد ' . $paidEmployees . ' موظف تم دفع رواتبهم'
                ], 422);
            }

            $invoice->invoiceEmployees()->delete();

            $invoice->update([
                'type' => 'regular',
                'employees_count' => 0
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف جميع الموظفين بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getWpsSettings()
    {
        try {
            $maxPercentage = Setting::get('wps_max_percentage', 70);

            return response()->json([
                'success' => true,
                'wps_max_percentage' => $maxPercentage
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateWpsSettings(Request $request)
    {
        $request->validate([
            'wps_max_percentage' => 'required|numeric|min:0|max:100'
        ]);

        try {
            Setting::set('wps_max_percentage', $request->wps_max_percentage, 'decimal', 'Maximum allowed percentage for Wage Protection System (WPS)');

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث إعدادات WPS بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadTemplate()
    {
        try {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = [
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

            foreach ($headers as $index => $header) {
                $sheet->setCellValueByColumnAndRow($index + 1, 1, $header);
            }

            $sampleData = [
                '1',
                'أحمد محمد',
                'مشروع أ',
                '5000',
                'شهري',
                '500',
                '100',
                '200',
                '30',
                '0',
                '5200',
                'SA0380000000608010167519',
                'أحمد محمد',
                'البنك الأهلي'
            ];

            foreach ($sampleData as $index => $value) {
                $sheet->setCellValueByColumnAndRow($index + 1, 2, $value);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            $fileName = 'salary_invoice_template_' . date('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), $fileName);
            $writer->save($tempFile);

            return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء القالب: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $invoiceId)
    {
        $request->validate([
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            $invoice = Invoice::findOrFail($invoiceId);

            if (!$invoice->isSalaryInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الفاتورة ليست فاتورة رواتب'
                ], 422);
            }

            if ($invoice->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة معتمدة بالفعل'
                ], 422);
            }

            if ($invoice->invoiceEmployees()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن اعتماد فاتورة بدون موظفين'
                ], 422);
            }

            $invoice->approve(auth()->id(), $request->notes);

            // Log invoice approval to chat
            $this->chatLogger->logInvoiceApproved($invoice->fresh(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'تم اعتماد الفاتورة بنجاح',
                'invoice' => $invoice->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $invoiceId)
    {
        $request->validate([
            'notes' => 'required|string|max:1000'
        ]);

        try {
            $invoice = Invoice::findOrFail($invoiceId);

            if (!$invoice->isSalaryInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الفاتورة ليست فاتورة رواتب'
                ], 422);
            }

            if ($invoice->isRejected()) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة مرفوضة بالفعل'
                ], 422);
            }

            $invoice->reject(auth()->id(), $request->notes);

            // Log invoice rejection to chat
            $this->chatLogger->logInvoiceRejected($invoice->fresh(), $request->notes);

            return response()->json([
                'success' => true,
                'message' => 'تم رفض الفاتورة',
                'invoice' => $invoice->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
    public function review(Request $request, $invoiceId)
    {
        $request->validate([
            'revision_status' => 'required|in:revision_approved,revision_rejected',
            'revision_notes' => 'required_if:revision_status,revision_rejected|string|max:1000'
        ]);

        try {
            $invoice = Invoice::findOrFail($invoiceId);

            if (!$invoice->isSalaryInvoice()) {
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الفاتورة ليست فاتورة رواتب'
                ], 422);
            }

            // Check if revision is already done
            if ($invoice->revision_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'تمت مراجعة هذه الفاتورة بالفعل'
                ], 422);
            }

            // Update revision status and notes
            $invoice->update([
                'revision_status' => $request->revision_status,
                'revision_notes' => $request->revision_notes,
                'revision_by' => auth()->id(),
                'revision_at' => now()
            ]);

            // Log invoice revision to chat
            $this->chatLogger->logInvoiceReviewed($invoice->fresh(), $request->revision_status, $request->revision_notes);

            $statusText = $request->revision_status === 'revision_approved' ? 'قبول' : 'رفض';

            return response()->json([
                'success' => true,
                'message' => "تم {$statusText} المراجعة بنجاح",
                'invoice' => $invoice->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ: ' . $e->getMessage()
            ], 500);
        }
    }
}
