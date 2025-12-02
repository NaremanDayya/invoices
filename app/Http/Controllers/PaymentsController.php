<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    /**
     * Display all payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['client', 'invoice'])
            ->latest();

        // Simple search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('client', function($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('invoice', function($q) use ($search) {
                        $q->where('invoice_number', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('payment_date', $request->date);
        }

        $payments = $query->paginate(20);
        $clients = Client::all();

        // Statistics
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'cancelled' => Payment::where('status', 'cancelled')->count(),
        ];

        return view('payments.index', compact('payments', 'clients', 'stats'));
    }

    /**
     * Show create payment form
     */
    public function create()
    {
        $clients = Client::all();
        $invoices = Invoice::all();
        $payments = Payment::all();
        return view('payments.create', compact('clients', 'invoices','payments'));
    }

    /**
     * Store new payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'status' => 'required|in:completed,pending,cancelled',
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        // Generate payment number
        $validated['number'] = 'PAY-' . date('Ymd') . '-' . str_pad(Payment::count() + 1, 3, '0', STR_PAD_LEFT);

        Payment::create($validated);

        return redirect()->route('payments.index')
            ->with('success', 'تم حفظ بيانات الدفع بنجاح');
    }

    /**
     * Show single payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['client', 'invoice']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Edit payment form
     */
    public function edit(Payment $payment)
    {
        $clients = Client::all();
        $invoices = Invoice::where('status', 'approved')->get();
        return view('payments.edit', compact('payment', 'clients', 'invoices'));
    }

    /**
     * Update payment
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,check',
            'status' => 'required|in:completed,pending,cancelled',
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
        ]);

        $payment->update($validated);

        return redirect()->route('payments.index')
            ->with('success', 'تم تحديث بيانات الدفع بنجاح');
    }

    /**
     * Delete payment
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();
        return redirect()->route('payments.index')
            ->with('success', 'تم حذف بيانات الدفع بنجاح');
    }

    /**
     * Confirm payment (mark as completed)
     */
    public function confirm(Payment $payment)
    {
        $payment->update(['status' => 'completed']);
        return back()->with('success', 'تم تأكيد الدفع بنجاح');
    }

    /**
     * Print payment receipt
     */
    public function print(Payment $payment)
    {
        $payment->load(['client', 'invoice']);
        return view('payments.print', compact('payment'));
    }
}
