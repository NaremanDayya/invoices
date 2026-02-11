<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Models\Setting;
use App\Services\SalaryInvoiceImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SalaryInvoiceController extends Controller
{
    protected $importService;

    public function __construct(SalaryInvoiceImportService $importService)
    {
        $this->importService = $importService;
    }

    public function importEmployees(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240'
        ]);

        try {
            \Log::info('Salary Import Controller: Starting import', [
                'invoice_id' => $request->invoice_id,
                'file_name' => $request->file('excel_file')->getClientOriginalName()
            ]);

            $invoice = Invoice::findOrFail($request->invoice_id);

            if ($invoice->invoiceEmployees()->exists()) {
                \Log::warning('Salary Import Controller: Duplicate import attempt', [
                    'invoice_id' => $invoice->id
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'هذه الفاتورة تحتوي بالفعل على موظفين مستوردين. يرجى حذفهم أولاً.'
                ], 422);
            }

            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            \Log::info('Salary Import Controller: Calling import service', [
                'file_path' => $filePath
            ]);

            $result = $this->importService->import($filePath, $invoice->id);

            \Log::info('Salary Import Controller: Import result', [
                'success' => $result['success'],
                'message' => $result['message'] ?? 'No message'
            ]);

            if ($result['success']) {
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
            \Log::error('Salary Import Controller: Exception caught', [
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

    public function updatePaymentMethod(Request $request, $employeeId)
    {
        dd($request);
        $request->validate([
            'payment_method' => 'required|in:wps,monthly',
            'wps_percentage' => 'required_if:payment_method,wps|nullable|numeric|min:0|max:100'
        ]);

        try {
            $result = $this->importService->updateEmployeePaymentMethod(
                $employeeId,
                $request->payment_method,
                $request->wps_percentage
            );

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'employee' => $result['employee']
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

            \Log::info('Salary Import: All employees cleared', [
                'invoice_id' => $invoiceId,
                'deleted_count' => $deletedCount
            ]);

            return response()->json([
                'success' => true,
                'message' => "تم حذف {$deletedCount} موظف بنجاح. يمكنك الآن إعادة الاستيراد"
            ]);

        } catch (\Exception $e) {
            \Log::error('Salary Import: Error clearing employees', [
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
}
