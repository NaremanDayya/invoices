<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\CreditNote;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditNoteController extends Controller
{
    public function store(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->total_price,
            'reason' => 'required|string|max:500',
            'issue_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($invoice, $validated) {
            // Generate credit note number
            $creditNoteCount = $invoice->creditNotes()->count() + 1;
            $creditNoteNumber = 'CN-' . $invoice->number . '-' . str_pad($creditNoteCount, 3, '0', STR_PAD_LEFT);

            // Create credit note
            $creditNote = CreditNote::create([
                'invoice_id' => $invoice->id,
                'number' => $creditNoteNumber,
                'amount' => $validated['amount'],
                'reason' => $validated['reason'],
                'issue_date' => $validated['issue_date'],
                'description' => $validated['description'],
                'is_main' => $creditNoteCount === 1,
            ]);

            // Update invoice totals
            $invoice->update([
                'credit_notes_count' => $creditNoteCount,
                'total_credit_notes' => $invoice->creditNotes()->sum('amount'),
                'total_price' => $invoice->base_price + $invoice->tax_amount - $invoice->creditNotes()->sum('amount'),
            ]);

            // Log the action
            AuditLog::logAction('created', $creditNote, 'تم إنشاء إشعار دائن للفاتورة');
        });

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة إشعار الدائن بنجاح',
            'credit_note' => $creditNote
        ]);
    }

    public function destroy(CreditNote $creditNote)
    {
        $invoice = $creditNote->invoice;

        DB::transaction(function () use ($creditNote, $invoice) {
            $creditNote->delete();

            // Update invoice totals
            $invoice->update([
                'credit_notes_count' => $invoice->creditNotes()->count(),
                'total_credit_notes' => $invoice->creditNotes()->sum('amount'),
                'total_price' => $invoice->base_price + $invoice->tax_amount - $invoice->creditNotes()->sum('amount'),
            ]);

            AuditLog::logAction('deleted', $creditNote, 'تم حذف إشعار الدائن');
        });

        return response()->json([
            'success' => true,
            'message' => 'تم حذف إشعار الدائن بنجاح'
        ]);
    }

    public function getInvoiceCreditNotes(Invoice $invoice)
    {
        $creditNotes = $invoice->creditNotes()->orderBy('issue_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'credit_notes' => $creditNotes,
            'total_credit_notes' => $invoice->total_credit_notes
        ]);
    }
}
