<?php

namespace App\Http\Controllers;

use App\Models\InvoiceStatus;
use Illuminate\Http\Request;

class InvoiceStatusController extends Controller
{
    public function index()
    {
        $statuses = InvoiceStatus::ordered()->get();
        return view('invoice-statuses.index', compact('statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'sometimes', // Change from 'boolean' to 'sometimes'
        ]);

        // Convert checkbox value to boolean
        $validated['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

        // Alternative cleaner approach:
        // $validated['is_active'] = $request->boolean('is_active');

        InvoiceStatus::create($validated);

        return redirect()->route('invoice-statuses.index')
            ->with('success', 'تم إضافة حالة الفاتورة بنجاح');
    }

    public function update(Request $request, InvoiceStatus $invoiceStatus)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
            'is_active' => 'sometimes', // Change from 'boolean' to 'sometimes'
        ]);

        // Convert checkbox value to boolean
        $validated['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

        // Alternative cleaner approach:
        // $validated['is_active'] = $request->boolean('is_active');

        $invoiceStatus->update($validated);

        return redirect()->route('invoice-statuses.index')
            ->with('success', 'تم تحديث حالة الفاتورة بنجاح');
    }

    public function destroy(InvoiceStatus $invoiceStatus)
    {
        if ($invoiceStatus->invoices()->count() > 0) {
            return redirect()->route('invoice-statuses.index')
                ->with('error', 'لا يمكن حذف هذه الحالة لأنها مرتبطة بفواتير');
        }

        $invoiceStatus->delete();

        return redirect()->route('invoice-statuses.index')
            ->with('success', 'تم حذف حالة الفاتورة بنجاح');
    }
}
