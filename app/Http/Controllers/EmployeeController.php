<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceEmployee;
use App\Services\ChatActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    protected $chatLogger;

    public function __construct(ChatActivityLogger $chatLogger)
    {
        $this->chatLogger = $chatLogger;
    }
    public function index(Request $request)
    {
        $invoiceId = $request->get('invoice_id');
        $search = $request->get('search', '');
        $filter = $request->get('filter', 'all');

        // Build query for invoice employees
        $query = InvoiceEmployee::with(['invoice.client', 'employee']);

        // Apply invoice filter
        if ($invoiceId) {
            $query->where('invoice_id', $invoiceId);
        }

        // Get all employees
        $allEmployees = $query->get();

        // Apply search filter
        if (!empty($search)) {
            $allEmployees = $allEmployees->filter(function($emp) use ($search) {
                return stripos($emp->employee_name, $search) !== false ||
                       stripos($emp->project ?? '', $search) !== false ||
                       stripos($emp->id, $search) !== false ||
                       stripos($emp->invoice->number ?? '', $search) !== false;
            });
        }

        // Apply status/type filter
        if ($filter !== 'all') {
            $allEmployees = $allEmployees->filter(function($emp) use ($filter) {
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

        $employees = $allEmployees;

        // Get all invoices for dropdown (salary invoices only)
        $invoices = Invoice::where('type', 'salary_invoice')
            ->orderBy('generation_date', 'desc')
            ->get();

        // Calculate stats
        $allInvoiceEmployees = InvoiceEmployee::all();
        $stats = [
            'total' => $allInvoiceEmployees->count(),
            'paid' => $allInvoiceEmployees->where('payment_status', 'paid')->count(),
            'partially_paid' => $allInvoiceEmployees->where('payment_status', 'partially_paid')->count(),
            'unpaid' => $allInvoiceEmployees->where('payment_status', 'unpaid')->count(),
            'wps' => $allInvoiceEmployees->where('employee.file_type', 'حماية أجور')->count(),
            'monthly' => $allInvoiceEmployees->where('employee.file_type', 'رواتب شهرية')->count(),
            'total_salaries' => $allInvoiceEmployees->sum('total_salary'),
            'total_paid' => $allInvoiceEmployees->sum('total_paid'),
            'total_remaining' => $allInvoiceEmployees->sum('remaining_amount'),
        ];

        $clients = Client::pluck('name', 'id');
        $selectedInvoice = $invoiceId ? Invoice::find($invoiceId) : null;

        return view('Employees.index', compact('employees', 'clients', 'invoices', 'stats', 'search', 'filter', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file_type' => 'required|string|in:رواتب شهرية,حماية أجور',
            'client_id' => 'required|exists:clients,id',
            'invoice_id' => 'required|exists:invoices,id',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'iban' => 'required|string|max:34',
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'monthly_salary' => 'required|numeric|min:0',
            'wage_salary' => 'required|numeric|min:0',
            'work_days' => 'required|integer|min:1|max:31', // أصبح required لكلا النوعين
            'is_active' => 'nullable|boolean',
        ]);

        // تحقق إضافي من أن راتب الحماية لا يتجاوز 50% من إجمالي الراتب
        $totalSalary = $validated['monthly_salary'] + $validated['wage_salary'];
        $maxWageSalary = $totalSalary * 0.5;

        if ($validated['wage_salary'] > $maxWageSalary) {
            return response()->json([
                'success' => false,
                'message' => 'راتب حماية الأجور لا يمكن أن يتجاوز 50% من إجمالي الراتب'
            ], 422);
        }

        try {
            DB::beginTransaction();

            // حساب صافي الراتب
            $net_salary = $totalSalary;

            // 1. إنشاء الموظف في جدول employees
            $employeeData = [
                'file_type' => $validated['file_type'],
                'client_id' => $validated['client_id'],
                'name' => $validated['name'],
                'phone_number' => $validated['phone_number'],
                'iban' => $validated['iban'],
                'bank_name' => $validated['bank_name'],
                'account_holder_name' => $validated['account_holder_name'],
                'monthly_salary' => $validated['monthly_salary'],
                'wage_salary' => $validated['wage_salary'],
                'net_salary' => $net_salary,
                'work_days' => $validated['work_days'], // work_days أصبح لكلا النوعين
                'is_active' => $request->boolean('is_active'),
                'account_change_count' => 0,
            ];

            $employee = Employee::create($employeeData);

            // 2. إضافة السجل في جدول invoice_employees (pivot table)
            $dailyRate = $validated['monthly_salary'] / 30;

            // حساب total_amount بناءً على أيام العمل لكلا النوعين
            $totalAmount = $dailyRate * $validated['work_days'];

            $invoiceEmployeeData = [
                'invoice_id' => $validated['invoice_id'],
                'employee_id' => $employee->id,
                'work_days' => $validated['work_days'],
                'daily_rate' => $dailyRate,
                'total_amount' => $totalAmount, // محسوب بناءً على أيام العمل
                'absence_days' => 0,
                'absence_deduction' => 0,
                'deductions' => 0,
                'notes' => 'تم إضافة الموظف عبر النموذج الرئيسي - نوع الملف: ' . $validated['file_type']
            ];

            // إدخال البيانات في جدول pivot
            DB::table('invoice_employees')->insert($invoiceEmployeeData);

            DB::commit();

            // Log employee creation to chat
            $invoice = Invoice::find($validated['invoice_id']);
            $this->chatLogger->logEmployeeCreated($employee, $invoice);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الموظف وإضافته إلى الفاتورة بنجاح',
                'employee' => $employee,
                'invoice_employee' => $invoiceEmployeeData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إنشاء الموظف: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show(Employee $employee)
    {
        return response()->json([
            'employee' => $employee->load('client')
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'file_type' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'iban' => 'required|string|max:34',
            'bank_name' => 'required|string|max:255',
            'account_holder_name' => 'required|string|max:255',
            'salary_with_insurances' => 'required|numeric|min:0',
            'employee_number' => 'required|string|unique:employees,employee_number,' . $employee->id,
            'position' => 'nullable|string|max:255',
            'gross_salary' => 'nullable|numeric|min:0',
            'net_salary' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $employee->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Employee updated successfully',
                'employee' => $employee
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting employee: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getClients()
    {
        $clients = Client::where('is_active', true)
            ->get(['id', 'name'])
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'text' => $client->name
                ];
            });

        return response()->json($clients);
    }

    public function getInvoices()
    {
        $invoices = Invoice::where('is_active', true)
            ->get(['id', 'invoice_number'])
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'text' => $invoice->invoice_number
                ];
            });

        return response()->json($invoices);
    }
}
