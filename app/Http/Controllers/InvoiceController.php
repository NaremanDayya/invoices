<?php
// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Message;
use App\Models\Service;
use App\Models\User;
use App\Services\ChatActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    protected $chatLogger;

    public function __construct(ChatActivityLogger $chatLogger)
    {
        $this->chatLogger = $chatLogger;
    }
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'service', 'payments', 'creditNotes', 'invoiceEmployees']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('number', 'like', '%' . $search . '%')
                    ->orWhereHas('client', function($clientQuery) use ($search) {
                        $clientQuery->where('name', 'like', '%' . $search . '%')
                                    ->orWhere('phone', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('start_date')) {
            $query->where('generation_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('generation_date', '<=', $request->end_date);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('payment_status', 'paid')->count(),
            'pending' => Invoice::where('payment_status', 'pending')->count(),
            'late' => Invoice::where('payment_status', 'late')->count(),
            'cancelled' => Invoice::where('is_cancelled', true)->count(),
        ];

        $clients = Client::all();
        $services = Service::all();
        $invoiceStatuses = \App\Models\InvoiceStatus::active()->ordered()->get();
        $serviceDetailColumns = collect($invoices)
            ->pluck('service_details_data')
            ->filter()
            ->flatMap(function ($details) {
                return collect($details)->pluck('name');
            })
            ->filter()
            ->unique()
            ->values();
        return view('invoices.index', compact('invoices', 'stats', 'clients', 'services', 'invoiceStatuses','serviceDetailColumns'));
    }

    public function create()
    {
        $clients = Client::all();
        $services = Service::all();
        $invoiceNumber = '#INV-' . now()->format('Y-m-') . str_pad(Invoice::count() + 1, 3, '0', STR_PAD_LEFT);

        return view('invoices.create', compact('clients', 'services', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'number' => 'required|string|unique:invoices,number',
            'generation_date' => 'required|date',
            'last_generation_date' => 'required|date',
            'base_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_status' => 'required|string',
            'notes' => 'nullable|string',
            'service_details' => 'nullable|array',
            'service_details.*.count' => 'nullable|integer|min:0',
            'service_details.*.days' => 'nullable|integer|min:0',
            'service_details.*.value' => 'nullable|string',
            'service_details.*.name' => 'nullable|string',
            'service_details.*.has_work_days' => 'nullable',
        ]);

        // Get service details from request
        $serviceDetails = $request->input('service_details', []);

        // Calculate totals from service details
        $totalWorkforce = 0;
        $totalWorkDays = 0;
        $totalEmployeesCount = 0;
        $totalWorkDaysCount = 0;

        foreach ($serviceDetails as $detailId => $detailData) {
            if (isset($detailData['has_work_days']) && $detailData['has_work_days'] == 1) {
                $count = (int)($detailData['count'] ?? 0);
                $days = (int)($detailData['days'] ?? 0);

                $totalWorkforce += $count;
                $totalWorkDays += ($count * $days);

                // Store aggregated counts for payment validation
                $totalEmployeesCount += $count;
                $totalWorkDaysCount += $days;
            }
        }

        // Use the base_price directly from the form
        $subtotal = $validated['base_price'];
        $taxAmount = ($subtotal * $validated['tax_rate']) / 100;
        $totalAmount = $subtotal + $taxAmount;
//dd($validated['invoice_status']);
        // Prepare invoice data
        $invoiceData = [
            'number' => $validated['number'],
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'service_details_data' => $serviceDetails,

            'generation_date' => $validated['generation_date'],
            'last_generation_date' => $validated['last_generation_date'],
            'due_date' => $validated['last_generation_date'],

            // Store calculated workforce totals
            'total_workers' => $totalWorkforce,
            'total_supervisors' => 0,
            'total_managers' => 0,
            'total_users' => 0,

            'workers_days' => 0,
            'supervisors_days' => 0,
            'managers_days' => 0,
            'users_days' => 0,

            'work_days' => $totalWorkDays,
            'employees_count' => $totalEmployeesCount,
            'work_days_count' => $totalWorkDaysCount,
            'daily_rate' => 0,

            'base_price' => $subtotal,
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'total_price' => $totalAmount,

            'paid_amount' => 0,
            'amount_difference' => 0,
            'difference_type' => null,

            'payment_status' => 'pending',

            'invoice_status' => $validated['invoice_status'],
            'notes' => $validated['notes'] ?? null,

            // Prevent automatic calculations by setting these
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
        ];

        // Create the invoice
        $invoice = Invoice::create($invoiceData);

        // Log invoice creation to chat
        $this->chatLogger->logInvoiceCreated($invoice);

        return redirect()->route('invoices.index')
            ->with('success', 'تم إنشاء الفاتورة بنجاح!');
    }
    public function show(Invoice $invoice)
    {
        $invoice->load(['client', 'service', 'payments', 'creditNotes']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['client', 'service'])->findOrFail($id);
        $clients = Client::all();
        $services = Service::all();
        return view('invoices.edit', compact('invoice', 'clients', 'services'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'service_id' => 'required|exists:services,id',
            'number' => 'required|string|unique:invoices,number,' . $invoice->id,
            'generation_date' => 'required|date',
            'last_generation_date' => 'required|date',
            'base_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_status' => 'required|string',
            'custom_status' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'service_details' => 'nullable|array',
            'service_details.*.count' => 'nullable|integer|min:0',
            'service_details.*.days' => 'nullable|integer|min:0',
            'service_details.*.value' => 'nullable|string',
            'service_details.*.name' => 'nullable|string',
            'service_details.*.has_work_days' => 'nullable',
        ]);

        $finalInvoiceStatus = $validated['invoice_status'];
        if ($validated['invoice_status'] === 'other' && !empty($validated['custom_status'])) {
            $finalInvoiceStatus = $validated['custom_status'];
        }

        // Get service details from request
        $serviceDetails = $request->input('service_details', []);

        // Calculate totals from service details
        $totalWorkforce = 0;
        $totalWorkDays = 0;
        $totalEmployeesCount = 0;
        $totalWorkDaysCount = 0;

        foreach ($serviceDetails as $detailId => $detailData) {
            if (isset($detailData['has_work_days']) && $detailData['has_work_days'] == 1) {
                $count = (int)($detailData['count'] ?? 0);
                $days = (int)($detailData['days'] ?? 0);

                $totalWorkforce += $count;
                $totalWorkDays += ($count * $days);

                // Store aggregated counts for payment validation
                $totalEmployeesCount += $count;
                $totalWorkDaysCount += $days;
            }
        }

        // Use the base_price directly from the form
        $subtotal = $validated['base_price'];
        $taxAmount = ($subtotal * $validated['tax_rate']) / 100;
        $totalAmount = $subtotal + $taxAmount;

        // Track changes for logging
        $changes = [];
        if ($invoice->total_price != $totalAmount) {
            $changes['المبلغ الإجمالي'] = ['old' => number_format($invoice->total_price, 2), 'new' => number_format($totalAmount, 2)];
        }
        if ($invoice->invoice_status != $finalInvoiceStatus) {
            $changes['حالة الفاتورة'] = ['old' => $invoice->invoice_status, 'new' => $finalInvoiceStatus];
        }

        $invoice->update([
            'number' => $validated['number'],
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'service_details_data' => $serviceDetails,
            'generation_date' => $validated['generation_date'],
            'last_generation_date' => $validated['last_generation_date'],
            'due_date' => $validated['last_generation_date'],
            'total_workers' => $totalWorkforce,
            'total_supervisors' => 0,
            'total_managers' => 0,
            'total_users' => 0,
            'workers_days' => 0,
            'supervisors_days' => 0,
            'managers_days' => 0,
            'users_days' => 0,
            'work_days' => $totalWorkDays,
            'employees_count' => $totalEmployeesCount,
            'work_days_count' => $totalWorkDaysCount,
            'daily_rate' => 0,
            'base_price' => $subtotal,
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'total_price' => $totalAmount,
            'invoice_status' => $finalInvoiceStatus,
            'notes' => $validated['notes'] ?? null,
        ]);

        // Log invoice update to chat
        if (!empty($changes)) {
            $this->chatLogger->logInvoiceUpdated($invoice, $changes);
        }

        return redirect()->route('invoices.index')
            ->with('success', 'تم تحديث الفاتورة بنجاح!');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->payments()->where('status', 'completed')->exists()) {
            return back()->with('error', 'لا يمكن حذف فاتورة تحتوي على مدفوعات مكتملة');
        }

        $invoice->delete();
        return redirect()->route('invoices.index')
            ->with('success', 'تم حذف الفاتورة بنجاح!');
    }

    public function addCreditNote(Request $request)
    {
        $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'credit_amount' => 'required|numeric|min:0.01',
            'credit_note_type' => 'required|in:credit_note,indebted_poems',
            'credit_reason' => 'required|string|max:1000'
        ]);

        try {
            $invoice = Invoice::findOrFail($request->invoice_id);

            $creditNoteNumber = 'CN-' . date('Ymd') . '-' . str_pad(CreditNote::count() + 1, 4, '0', STR_PAD_LEFT);

            // Determine if this is the main credit note
            $isMain = $invoice->creditNotes()->count() === 0;

            $creditNote = CreditNote::create([
                'invoice_id' => $invoice->id,
                'number' => $creditNoteNumber,
                'amount' => $request->credit_amount,
                'reason' => $request->credit_reason,
                'issue_date' => now(),
                'is_main' => $isMain,
                'description' => $request->credit_note_type == 'credit_note' ? 'إشعار دائن' : 'قصائد مديونة',
                'is_active' => true
            ]);

            // Update invoice credit note totals
            $invoice->update([
                'credit_notes_count' => $invoice->creditNotes()->count(),
                'total_credit_notes' => $invoice->creditNotes()->sum('amount')
            ]);

            // Log credit note addition
            $this->chatLogger->logInvoiceUpdated($invoice->fresh(), [
                'إشعار دائن' => ['old' => '-', 'new' => number_format($request->credit_amount, 2) . ' ريال']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الإشعار الدائن بنجاح',
                'credit_note_number' => $creditNoteNumber
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'فشل في إضافة الإشعار الدائن: ' . $e->getMessage()
            ], 500);
        }
    }
    public function addClient(Request $request)
    {

        try {
            $validated = $request->validate([
                'name' => 'required|string|min:2|unique:clients,name',
                'email' => 'nullable|email',
                'phone' => 'nullable|string',
                'tax_number' => 'required|numeric|digits:15',
                'address' => 'nullable|string',
            ]);

            $client = Client::create($validated);

            return response()->json([
                'success' => true,
                'client' => $client,
                'message' => 'تم إضافة العميل بنجاح'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى تصحيح الأخطاء',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة العميل: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addService(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|min:2|unique:services,name',
                'description' => 'nullable|string',
            ]);

            $service = Service::create($validated);

            return response()->json([
                'success' => true,
                'service' => $service,
                'message' => 'تم إضافة الخدمة بنجاح'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'يرجى تصحيح الأخطاء',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الخدمة: ' . $e->getMessage()
            ], 500);
        }
    }
    public function chatClients()
    {
        return Client::all()
            ->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                ];
            });

    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:bank_transfer,cash,check',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $paymentAmount = $validated['amount'];
            $remainingAmount = $invoice->total_price - $invoice->paid_amount;

            // Validate payment amount
            if ($paymentAmount > $remainingAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'مبلغ الدفعة لا يمكن أن يكون أكبر من المبلغ المتبقي'
                ], 422);
            }

            // Generate payment number
            $paymentCount = $invoice->payments()->count() + 1;
            $paymentNumber = 'PAY-' . str_replace(['INV-', '#'], '', $invoice->number) . '-' . str_pad($paymentCount, 3, '0', STR_PAD_LEFT);

            // Create payment
            $payment = $invoice->payments()->create([
                'number' => $paymentNumber,
                'amount' => $paymentAmount,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
            ]);

            // Update invoice paid amount
            $newPaidAmount = $invoice->paid_amount + $paymentAmount;
            $invoice->update([
                'paid_amount' => $newPaidAmount,
            ]);

            // Update payment status
            if ($newPaidAmount >= $invoice->total_price) {
                $invoice->update(['payment_status' => 'paid']);
            } elseif ($newPaidAmount > 0) {
                $invoice->update(['payment_status' => 'partially_paid']);
            }

            // Log payment to chat
            $this->chatLogger->logInvoicePayment($invoice->fresh(), $paymentAmount, $validated['payment_method']);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدفعة بنجاح',
                'payment' => $payment,
                'invoice_status' => $invoice->payment_status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الدفعة: ' . $e->getMessage()
            ], 500);
        }
    }
}
