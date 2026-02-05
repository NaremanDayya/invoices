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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['client', 'service', 'payments', 'creditNotes']);

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

        return view('invoices.index', compact('invoices', 'stats', 'clients', 'services', 'invoiceStatuses'));
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
        dd($request);
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

        // Determine the final invoice status
        $finalInvoiceStatus = $validated['invoice_status'];

        // Get service details from request
        $serviceDetails = $request->input('service_details', []);

        // Calculate totals from service details
        $totalWorkforce = 0;
        $totalWorkDays = 0;

        foreach ($serviceDetails as $detailId => $detailData) {
            if (isset($detailData['has_work_days']) && $detailData['has_work_days'] == 1) {
                $count = (int)($detailData['count'] ?? 0);
                $days = (int)($detailData['days'] ?? 0);

                $totalWorkforce += $count;
                $totalWorkDays += ($count * $days);
            }
        }

        // Use the base_price directly from the form
        $subtotal = $validated['base_price'];
        $taxAmount = ($subtotal * $validated['tax_rate']) / 100;
        $totalAmount = $subtotal + $taxAmount;

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
            'daily_rate' => 0,

            'base_price' => $subtotal,
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'total_price' => $totalAmount,

            'paid_amount' => 0,
            'amount_difference' => 0,
            'difference_type' => null,

            'payment_status' => 'pending',
            'payment_date' => null,

            'invoice_status' => $finalInvoiceStatus,
            'notes' => $validated['notes'] ?? null,

            // Prevent automatic calculations by setting these
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
        ];

        // Remove the dd() line and create the invoice
        $invoice = Invoice::create($invoiceData);

        // Continue with your conversation and message creation...
        $authenticatedUserId = Auth::id();

        $conversation = $invoice->conversation()
            ->where(function ($query) use ($authenticatedUserId) {
                $query->where('sender_id', $authenticatedUserId)
                    ->orWhere('receiver_id', $authenticatedUserId);
            })->first();

        if (!$conversation) {
            $adminUserId = User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })->value('id');
            $conversation = Conversation::create([
                'sender_id' => $authenticatedUserId,
                'receiver_id' => $adminUserId,
                'client_id' => $invoice->client->id,
                'invoice_id' => $invoice->id,
            ]);
        }

        $message = "فاتورة خاصة بالعميل {$invoice->client->name}، بقيمة: {$totalAmount}";
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $authenticatedUserId,
            'receiver_id' => $conversation->sender_id === $authenticatedUserId ? $conversation->receiver_id : $conversation->sender_id,
            'message' => $message,
        ]);

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
            'total_workers' => 'nullable|integer|min:0',
            'total_supervisors' => 'nullable|integer|min:0',
            'total_managers' => 'nullable|integer|min:0',
            'total_users' => 'nullable|integer|min:0',
            'workers_days' => 'nullable|integer|min:0',
            'supervisors_days' => 'nullable|integer|min:0',
            'managers_days' => 'nullable|integer|min:0',
            'users_days' => 'nullable|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'invoice_status' => 'required|string',
            'custom_status' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $finalInvoiceStatus = $validated['invoice_status'];
        if ($validated['invoice_status'] === 'other' && !empty($validated['custom_status'])) {
            $finalInvoiceStatus = $validated['custom_status'];
        }

        $totalWorkforce =
            ($validated['total_workers'] ?? 0) +
            ($validated['total_supervisors'] ?? 0) +
            ($validated['total_managers'] ?? 0) +
            ($validated['total_users'] ?? 0);

        $workersDays = $validated['workers_days'] ?? $validated['work_days'];
        $supervisorsDays = $validated['supervisors_days'] ?? $validated['work_days'];
        $managersDays = $validated['managers_days'] ?? $validated['work_days'];
        $usersDays = $validated['users_days'] ?? $validated['work_days'];

        $totalManDays =
            ($validated['total_workers'] * $workersDays) +
            ($validated['total_supervisors'] * $supervisorsDays) +
            ($validated['total_managers'] * $managersDays) +
            ($validated['total_users'] * $usersDays);

        $subtotal = $totalManDays * $validated['daily_rate'];
        $taxAmount = ($subtotal * $validated['tax_rate']) / 100;
        $totalAmount = $subtotal + $taxAmount + ($validated['amount_difference'] ?? 0);

        $invoice->update([
            'number' => $validated['number'],
            'client_id' => $validated['client_id'],
            'service_id' => $validated['service_id'],
            'generation_date' => $validated['generation_date'],
            'last_generation_date' => $validated['last_generation_date'],
            'due_date' => $validated['last_generation_date'],
            'total_workers' => $validated['total_workers'],
            'total_supervisors' => $validated['total_supervisors'],
            'total_managers' => $validated['total_managers'],
            'total_users' => $validated['total_users'],
            'workers_days' => $workersDays,
            'supervisors_days' => $supervisorsDays,
            'managers_days' => $managersDays,
            'users_days' => $usersDays,
            'work_days' => $totalManDays,
            'daily_rate' => 0,
            'base_price' => $subtotal,
            'tax_rate' => $validated['tax_rate'],
            'tax_amount' => $taxAmount,
            'total_price' => $totalAmount,
            'amount_difference' => 0,
            'invoice_status' => $finalInvoiceStatus,
            'notes' => $validated['notes'] ?? null,
        ]);

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
}
