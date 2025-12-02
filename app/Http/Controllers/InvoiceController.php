<?php
// app/Http/Controllers/InvoiceController.php

namespace App\Http\Controllers;

use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'service']);

        // Apply filters
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('number', 'like', '%' . $request->search . '%')
                    ->orWhereHas('client', function($clientQuery) use ($request) {
                        $clientQuery->where('name', 'like', '%' . $request->search . '%');
                    });
            });
        }

        if ($request->statusFilter) {
            $query->where('payment_status', $request->statusFilter);
        }

        if ($request->clientFilter) {
            $query->whereHas('client', function($clientQuery) use ($request) {
                $clientQuery->where('name', $request->clientFilter);
            });
        }

        if ($request->startDate) {
            $query->where('generation_date', '>=', $request->startDate);
        }

        if ($request->endDate) {
            $query->where('generation_date', '<=', $request->endDate);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(10);

        $stats = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('payment_status', 'paid')->count(),
            'pending' => Invoice::where('payment_status', 'pending')->count(),
            'overdue' => Invoice::where('payment_status', 'overdue')->count(),
            'late' => Invoice::where('payment_status', 'late')->count(),
        ];

        $clients = Client::all();
        $services = Service::all();

        return view('invoices.index', compact('invoices', 'stats', 'clients', 'services'));
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
            'total_workers' => 'required|integer|min:0',
            'total_supervisors' => 'required|integer|min:0',
            'total_managers' => 'required|integer|min:0',
            'total_users' => 'required|integer|min:0',
            'work_days' => 'required|integer|min:1',
            'daily_rate' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'amount_difference' => 'nullable|numeric',
            'payment_status' => 'required|string|in:pending,paid,overdue,late',
            'payment_date' => 'nullable|date',
            'invoice_status' => 'required|string',
            'custom_status' => 'nullable|string|max:255', // Add custom status validation
            'notes' => 'nullable|string',
        ]);

        // Determine the final invoice status
        $finalInvoiceStatus = $validated['invoice_status'];

        // If user selected "other" and provided custom status, use the custom status
        if ($validated['invoice_status'] === 'other' && !empty($validated['custom_status'])) {
            $finalInvoiceStatus = $validated['custom_status'];
        }

        // Define allowed statuses for validation
        $allowedStatuses = [
            'رواتب', 'عمولات', 'عمل اضافي', 'رواتب-احتضان قانوني',
            'مصاريف قانونية- احتضان قانوني', 'يوزرات', 'ملغية',
            'ملغية -احتضان قانوني', 'بروموتر', 'زيارة مستقلة'
        ];

        // Validate the final status against allowed statuses
        if (!in_array($finalInvoiceStatus, $allowedStatuses) && $validated['invoice_status'] !== 'other') {
            return redirect()->back()
                ->withInput()
                ->withErrors(['invoice_status' => 'حالة الفاتورة المحددة غير صالحة.']);
        }

        // Full workforce count
        $totalWorkforce =
            ($validated['total_workers'] ?? 0) +
            ($validated['total_supervisors'] ?? 0) +
            ($validated['total_managers'] ?? 0) +
            ($validated['total_users'] ?? 0);

        // Financial calculations
        $subtotal = $totalWorkforce * $validated['work_days'] * $validated['daily_rate'];
        $taxAmount = ($subtotal * $validated['tax_rate']) / 100;
        $totalAmount = $subtotal + $taxAmount + ($validated['amount_difference'] ?? 0);

        // Prepare invoice data (MAPPED to your DB columns)
        $invoiceData = [
            'number' => $validated['number'],
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],

            'generation_date' => $validated['generation_date'],
            'last_generation_date' => $validated['last_generation_date'],

            'due_date' => $validated['last_generation_date'], // same as before

            'total_workers' => $validated['total_workers'],
            'total_supervisors' => $validated['total_supervisors'],
            'total_managers' => $validated['total_managers'],
            'total_users' => $validated['total_users'],

            'work_days' => $validated['work_days'],
            'daily_rate' => $validated['daily_rate'],

            'base_price' => $subtotal,

            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,

            'total_price' => $totalAmount,

            'paid_amount' => $validated['payment_status'] === 'paid' ? $totalAmount : 0,

            'amount_difference' => $validated['amount_difference'] ?? 0,
            'difference_type' => null, // if you add later

            'payment_status' => $validated['payment_status'],
            'payment_date' => $validated['payment_date'],

            'invoice_status' => $finalInvoiceStatus, // Use the final determined status

            'notes' => $validated['notes'] ?? null,

            // other DB columns default to NULL automatically
        ];

        $invoice = Invoice::create($invoiceData);

        return redirect()->route('invoices.index')
            ->with('success', 'تم إنشاء الفاتورة بنجاح!');
    }
// In your InvoiceController
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

            $creditNote = CreditNote::create([
                'invoice_id' => $invoice->id,
                'number' => $creditNoteNumber,
                'amount' => $request->credit_amount,
                'reason' => $request->credit_reason,
                'issue_date' => now(),
                'is_main' => true, // or false based on your business logic
                'description' => $request->credit_note_type == 'credit_note' ? 'إشعار دائن' : 'قصائد مديونة',
                'is_active' => true
            ]);

            // Optional: Update invoice with credit note summary if needed
            $invoice->update([
                'has_credit_note' => true, // if you have this field
                'credit_note_type' => $request->credit_note_type,
                'credit_issued_at' => now(),
                'credit_issued_by' => auth()->id()
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
}
