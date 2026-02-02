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
     * Show payment details
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice.client']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Show edit payment form
     */
    public function edit(Payment $payment)
    {
        $invoices = Invoice::all();
        return view('payments.edit', compact('payment', 'invoices'));
    }

    /**
     * Store new payment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,other',
            'status' => 'required|in:completed,pending,cancelled',
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        if ($validated['amount'] > $invoice->remaining_amount) {
            return back()->withInput()->withErrors([
                'amount' => 'المبلغ المدفوع أكبر من المبلغ المتبقي (' . number_format($invoice->remaining_amount, 2) . ' ر.س)'
            ]);
        }

        $validated['number'] = 'PAY-' . date('Ymd') . '-' . str_pad(Payment::count() + 1, 4, '0', STR_PAD_LEFT);

        $payment = Payment::create($validated);

        if ($validated['status'] === 'completed') {
            $invoice->increment('paid_amount', $validated['amount']);
            
            if ($invoice->paid_amount >= $invoice->total_price) {
                $invoice->update([
                    'payment_status' => 'paid',
                    'payment_date' => $validated['payment_date']
                ]);
            } elseif ($invoice->paid_amount > 0) {
                $invoice->update(['payment_status' => 'late']);
            }
        }

        return redirect()->route('payments.index')
            ->with('success', 'تم حفظ بيانات الدفع بنجاح');
    }

    /**
     * Edit payment form (duplicate removed - using the one defined earlier)
     */
    public function editOld(Payment $payment)
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
            'invoice_id' => 'required|exists:invoices,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,check,credit_card,other',
            'status' => 'required|in:completed,pending,cancelled',
            'description' => 'nullable|string|max:500',
            'reference_number' => 'nullable|string|max:100',
            'bank_name' => 'nullable|string|max:100',
            'account_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);
        $oldAmount = $payment->amount;
        $oldStatus = $payment->status;
        $newAmount = $validated['amount'];
        $newStatus = $validated['status'];

        if ($oldStatus === 'completed') {
            $invoice->decrement('paid_amount', $oldAmount);
        }

        if ($newStatus === 'completed') {
            $availableAmount = $invoice->total_price - $invoice->paid_amount;
            if ($newAmount > $availableAmount) {
                return back()->withInput()->withErrors([
                    'amount' => 'المبلغ المدفوع أكبر من المبلغ المتبقي (' . number_format($availableAmount, 2) . ' ر.س)'
                ]);
            }
            $invoice->increment('paid_amount', $newAmount);
        }

        if ($invoice->paid_amount >= $invoice->total_price) {
            $invoice->update(['payment_status' => 'paid', 'payment_date' => $validated['payment_date']]);
        } elseif ($invoice->paid_amount > 0) {
            $invoice->update(['payment_status' => 'late']);
        } else {
            $invoice->update(['payment_status' => 'pending']);
        }

        $payment->update($validated);

        return redirect()->route('payments.index')
            ->with('success', 'تم تحديث بيانات الدفع بنجاح');
    }

    /**
     * Delete payment
     */
    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        
        if ($payment->status === 'completed') {
            $invoice->decrement('paid_amount', $payment->amount);
            
            if ($invoice->paid_amount >= $invoice->total_price) {
                $invoice->update(['payment_status' => 'paid']);
            } elseif ($invoice->paid_amount > 0) {
                $invoice->update(['payment_status' => 'late']);
            } else {
                $invoice->update(['payment_status' => 'pending']);
            }
        }
        
        $payment->delete();
        return redirect()->route('payments.index')
            ->with('success', 'تم حذف بيانات الدفع بنجاح');
    }

    /**
     * Confirm payment (mark as completed)
     */
    public function confirm(Payment $payment)
    {
        if ($payment->status === 'completed') {
            return back()->with('info', 'الدفع مكتمل بالفعل');
        }

        $invoice = $payment->invoice;
        $availableAmount = $invoice->total_price - $invoice->paid_amount;
        
        if ($payment->amount > $availableAmount) {
            return back()->with('error', 'المبلغ المدفوع أكبر من المبلغ المتبقي');
        }

        $payment->update(['status' => 'completed']);
        $invoice->increment('paid_amount', $payment->amount);
        
        if ($invoice->paid_amount >= $invoice->total_price) {
            $invoice->update(['payment_status' => 'paid', 'payment_date' => $payment->payment_date]);
        } elseif ($invoice->paid_amount > 0) {
            $invoice->update(['payment_status' => 'late']);
        }
        
        return back()->with('success', 'تم تأكيد الدفع بنجاح');
    }

    /**
     * Print payment receipt
     */
    public function print($id)
    {
        $payment = Payment::with(['client', 'invoice'])->findOrFail($id);
        return view('payments.print', compact('payment'));
    }

}
