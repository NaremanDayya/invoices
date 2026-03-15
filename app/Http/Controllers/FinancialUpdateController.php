<?php

namespace App\Http\Controllers;

use App\Models\FinancialUpdate;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinancialUpdateController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialUpdate::with(['invoice', 'payment', 'client', 'creator']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('update_type', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('update_type', $request->type);
        }

        // Filter by client
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Filter by invoice
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('update_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->where('update_date', '<=', $request->end_date);
        }

        $updates = $query->orderBy('update_date', 'desc')->paginate(20);
        $clients = Client::orderBy('name')->get();

        return view('financial-updates.index', compact('updates', 'clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'payment_id' => 'nullable|exists:payments,id',
            'client_id' => 'nullable|exists:clients,id',
            'update_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'update_date' => 'required|date',
            'status' => 'nullable|string|in:active,archived',
            'metadata' => 'nullable|array'
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = $validated['status'] ?? 'active';

        $update = FinancialUpdate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة التحديث المالي بنجاح',
            'update' => $update->load(['invoice', 'payment', 'client', 'creator'])
        ]);
    }

    public function update(Request $request, FinancialUpdate $financialUpdate)
    {
        $validated = $request->validate([
            'invoice_id' => 'nullable|exists:invoices,id',
            'payment_id' => 'nullable|exists:payments,id',
            'client_id' => 'nullable|exists:clients,id',
            'update_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'update_date' => 'required|date',
            'status' => 'nullable|string|in:active,archived',
            'metadata' => 'nullable|array'
        ]);

        $financialUpdate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث البيانات بنجاح',
            'update' => $financialUpdate->load(['invoice', 'payment', 'client', 'creator'])
        ]);
    }

    public function destroy(FinancialUpdate $financialUpdate)
    {
        $financialUpdate->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف التحديث المالي بنجاح'
        ]);
    }

    public function getByInvoice($invoiceId)
    {
        $updates = FinancialUpdate::where('invoice_id', $invoiceId)
            ->with(['creator'])
            ->orderBy('update_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'updates' => $updates
        ]);
    }

    public function getByPayment($paymentId)
    {
        $updates = FinancialUpdate::where('payment_id', $paymentId)
            ->with(['creator'])
            ->orderBy('update_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'updates' => $updates
        ]);
    }
}
