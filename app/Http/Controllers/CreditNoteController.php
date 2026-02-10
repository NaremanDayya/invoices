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
            'type' => 'required|in:internal,client',
            'credit_amount_with_tax' => 'required|numeric|min:0.01',
            'credit_note_number' => 'nullable|string',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'new_base_price' => 'nullable|numeric|min:0',
            'new_tax_rate' => 'nullable|numeric|min:0|max:100',
            'new_employees_count' => 'nullable|integer|min:0',
            'new_work_days' => 'nullable|integer|min:0',
        ]);

        $creditNote = null;

        DB::transaction(function () use ($invoice, $validated, &$creditNote) {
            // Store previous values
            $previousValues = [
                'base_price' => $invoice->base_price,
                'tax_rate' => $invoice->tax_rate,
                'tax_amount' => $invoice->tax_amount,
                'total_price' => $invoice->total_price,
                'employees_count' => $invoice->employees_count,
                'work_days_count' => $invoice->work_days_count,
                'total_workers' => $invoice->total_workers,
                'work_days' => $invoice->work_days,
            ];

            // Calculate credit amount before tax by removing tax from the entered amount
            $creditWithTax = $validated['credit_amount_with_tax'];
            $creditBeforeTax = $creditWithTax / (1 + ($invoice->tax_rate / 100));
            
            // Calculate new values
            $newBasePrice = $validated['new_base_price'] ?? ($invoice->base_price - $creditBeforeTax);
            $newTaxRate = $validated['new_tax_rate'] ?? $invoice->tax_rate;
            $newTaxAmount = ($newBasePrice * $newTaxRate) / 100;
            $newTotalPrice = $newBasePrice + $newTaxAmount;
            
            // Credit amount after tax is the amount entered by user
            $creditAfterTax = $creditWithTax;
            
            $newValues = [
                'base_price' => $newBasePrice,
                'tax_rate' => $newTaxRate,
                'tax_amount' => $newTaxAmount,
                'total_price' => $newTotalPrice,
                'employees_count' => $validated['new_employees_count'] ?? $invoice->employees_count,
                'work_days_count' => $validated['new_work_days'] ?? $invoice->work_days_count,
                'total_workers' => $validated['new_employees_count'] ?? $invoice->total_workers,
                'work_days' => $validated['new_work_days'] ?? $invoice->work_days,
            ];

            // Validate that new total is not negative
            if ($newTotalPrice < 0) {
                throw new \Exception('المبلغ الإجمالي الجديد لا يمكن أن يكون سالباً');
            }

            // Validate credit amount doesn't exceed base price
            if ($creditBeforeTax > $invoice->base_price) {
                throw new \Exception('مبلغ الخصم لا يمكن أن يكون أكبر من المبلغ الأساسي للفاتورة');
            }

            // Generate credit note number
            $creditNoteCount = $invoice->creditNotes()->count() + 1;
            $creditNoteNumber = $validated['credit_note_number'] ?? ('CN-' . str_replace(['INV-', '#'], '', $invoice->number) . '-' . str_pad($creditNoteCount, 3, '0', STR_PAD_LEFT));

            // Create credit note
            $creditNote = CreditNote::create([
                'invoice_id' => $invoice->id,
                'created_by' => auth()->id(),
                'credit_note_number' => $creditNoteNumber,
                'type' => $validated['type'],
                'previous_values' => $previousValues,
                'new_values' => $newValues,
                'amount_difference' => $creditAfterTax,
                'previous_total' => $previousValues['total_price'],
                'new_total' => $newValues['total_price'],
                'reason' => $validated['reason'],
                'notes' => $validated['notes'] ?? null,
                'number' => $creditNoteNumber,
                'amount' => $creditAfterTax,
                'issue_date' => now(),
                'is_main' => $creditNoteCount === 1,
            ]);

            // Update invoice with new values
            $invoice->update([
                'base_price' => $newValues['base_price'],
                'tax_rate' => $newValues['tax_rate'],
                'tax_amount' => $newValues['tax_amount'],
                'total_price' => $newValues['total_price'],
                'employees_count' => $newValues['employees_count'],
                'work_days_count' => $newValues['work_days_count'],
                'total_workers' => $newValues['total_workers'],
                'work_days' => $newValues['work_days'],
            ]);

            // Log the action
            if (class_exists('App\Models\AuditLog')) {
                AuditLog::logAction('created', $creditNote, 'تم إنشاء إشعار دائن للفاتورة');
            }
        });

        return redirect()->back()->with('success', 'تم إضافة إشعار الدائن بنجاح');
    }

    public function getCreditNoteCount(Invoice $invoice)
    {
        return response()->json([
            'success' => true,
            'count' => $invoice->creditNotes()->count()
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
