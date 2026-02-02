<?php

namespace Database\Seeders;

use App\Models\CreditNote;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CreditNotesTableSeeder extends Seeder
{
    public function run()
    {
        $invoices = Invoice::all();

        if ($invoices->isEmpty()) {
            $this->command->error('Please run InvoicesTableSeeder first!');
            return;
        }

        $creditNotes = [];
        $creditNoteNumber = 1;

        // Select specific invoices to add credit notes
        // We'll add credit notes to invoices from Client 3 (index 2) and some others
        $invoicesForCreditNotes = Invoice::whereIn('client_id', [3, 4, 8])
            ->where('is_cancelled', false)
            ->limit(5)
            ->get();

        foreach ($invoicesForCreditNotes as $index => $invoice) {
            // Determine number of credit notes for this invoice (1-3)
            $creditNoteCount = rand(1, 3);
            
            for ($i = 0; $i < $creditNoteCount; $i++) {
                $isMain = ($i === 0); // First credit note is the main one
                
                // Calculate credit amount (between 5% and 20% of invoice total)
                $creditAmount = round($invoice->total_price * (rand(5, 20) / 100), 2);
                
                // Ensure we don't exceed the invoice total
                $currentTotalCredits = $invoice->creditNotes()->sum('amount');
                if ($currentTotalCredits + $creditAmount > $invoice->total_price * 0.5) {
                    $creditAmount = round(($invoice->total_price * 0.5) - $currentTotalCredits, 2);
                }
                
                if ($creditAmount <= 0) continue;
                
                $creditNotes[] = [
                    'invoice_id' => $invoice->id,
                    'number' => 'CN-' . date('Ymd') . '-' . str_pad($creditNoteNumber++, 4, '0', STR_PAD_LEFT),
                    'amount' => $creditAmount,
                    'reason' => $this->getRandomCreditNoteReason(),
                    'issue_date' => Carbon::now()->subDays(rand(1, 30)),
                    'is_main' => $isMain,
                    'description' => $this->getRandomCreditNoteDescription(),
                    'is_active' => true,
                    'created_at' => Carbon::now()->subDays(rand(1, 30)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 30)),
                ];
            }
        }

        // Add specific scenarios
        
        // Scenario 1: Large credit note
        $largeInvoice = Invoice::where('total_price', '>', 100000)
            ->where('is_cancelled', false)
            ->whereDoesntHave('creditNotes')
            ->first();
            
        if ($largeInvoice) {
            $creditNotes[] = [
                'invoice_id' => $largeInvoice->id,
                'number' => 'CN-' . date('Ymd') . '-' . str_pad($creditNoteNumber++, 4, '0', STR_PAD_LEFT),
                'amount' => 25000.00,
                'reason' => 'خصم تعاقدي متفق عليه مسبقاً',
                'issue_date' => Carbon::now()->subDays(10),
                'is_main' => true,
                'description' => 'إشعار دائن - خصم على المشروع الكبير',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ];
        }

        // Scenario 2: Multiple small credit notes on same invoice
        $mediumInvoice = Invoice::whereBetween('total_price', [50000, 100000])
            ->where('is_cancelled', false)
            ->whereDoesntHave('creditNotes')
            ->first();
            
        if ($mediumInvoice) {
            for ($i = 0; $i < 3; $i++) {
                $creditNotes[] = [
                    'invoice_id' => $mediumInvoice->id,
                    'number' => 'CN-' . date('Ymd') . '-' . str_pad($creditNoteNumber++, 4, '0', STR_PAD_LEFT),
                    'amount' => rand(1000, 3000),
                    'reason' => 'تعديل على عدد أيام العمل - المرحلة ' . ($i + 1),
                    'issue_date' => Carbon::now()->subDays(20 - ($i * 5)),
                    'is_main' => ($i === 0),
                    'description' => 'إشعار دائن - تعديل جزئي',
                    'is_active' => true,
                    'created_at' => Carbon::now()->subDays(20 - ($i * 5)),
                    'updated_at' => Carbon::now()->subDays(20 - ($i * 5)),
                ];
            }
        }

        // Scenario 3: Credit note for quality issues
        $qualityInvoice = Invoice::where('payment_status', 'pending')
            ->where('is_cancelled', false)
            ->whereDoesntHave('creditNotes')
            ->first();
            
        if ($qualityInvoice) {
            $creditNotes[] = [
                'invoice_id' => $qualityInvoice->id,
                'number' => 'CN-' . date('Ymd') . '-' . str_pad($creditNoteNumber++, 4, '0', STR_PAD_LEFT),
                'amount' => round($qualityInvoice->total_price * 0.10, 2),
                'reason' => 'خصم بسبب عدم مطابقة المواصفات المتفق عليها',
                'issue_date' => Carbon::now()->subDays(7),
                'is_main' => true,
                'description' => 'إشعار دائن - تعويض عن جودة الخدمة',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(7),
                'updated_at' => Carbon::now()->subDays(7),
            ];
        }

        // Scenario 4: Inactive credit note (cancelled)
        $cancelledCreditInvoice = Invoice::where('payment_status', '!=', 'cancelled')
            ->where('is_cancelled', false)
            ->whereDoesntHave('creditNotes')
            ->skip(1)
            ->first();
            
        if ($cancelledCreditInvoice) {
            $creditNotes[] = [
                'invoice_id' => $cancelledCreditInvoice->id,
                'number' => 'CN-' . date('Ymd') . '-' . str_pad($creditNoteNumber++, 4, '0', STR_PAD_LEFT),
                'amount' => 5000.00,
                'reason' => 'إشعار دائن ملغي - تم التراجع عن الخصم',
                'issue_date' => Carbon::now()->subDays(15),
                'is_main' => true,
                'description' => 'إشعار دائن ملغي',
                'is_active' => false,
                'created_at' => Carbon::now()->subDays(15),
                'updated_at' => Carbon::now()->subDays(10),
                'deleted_at' => Carbon::now()->subDays(10),
            ];
        }

        // Scenario 5: Recent credit notes
        $recentInvoices = Invoice::where('payment_status', 'pending')
            ->where('is_cancelled', false)
            ->whereDoesntHave('creditNotes')
            ->limit(2)
            ->get();
            
        foreach ($recentInvoices as $invoice) {
            $creditNotes[] = [
                'invoice_id' => $invoice->id,
                'number' => 'CN-' . date('Ymd') . '-' . str_pad($creditNoteNumber++, 4, '0', STR_PAD_LEFT),
                'amount' => round($invoice->total_price * 0.08, 2),
                'reason' => 'خصم ترويجي للعميل المميز',
                'issue_date' => Carbon::now()->subDays(rand(1, 5)),
                'is_main' => true,
                'description' => 'إشعار دائن - خصم ترويجي',
                'is_active' => true,
                'created_at' => Carbon::now()->subDays(rand(1, 5)),
                'updated_at' => Carbon::now()->subDays(rand(1, 5)),
            ];
        }

        // Create all credit notes
        foreach ($creditNotes as $creditNote) {
            CreditNote::create($creditNote);
        }

        $this->command->info('Created ' . count($creditNotes) . ' credit notes with various scenarios.');
    }

    private function getRandomCreditNoteReason()
    {
        $reasons = [
            'تعديل على عدد الموظفين الفعلي',
            'خصم متفق عليه تعاقدياً',
            'تعويض عن تأخير في تقديم الخدمة',
            'خصم على الكمية',
            'تسوية فروقات في الفاتورة',
            'خصم ترويجي',
            'تعديل على أيام العمل الفعلية',
            'خصم بسبب شكوى العميل',
            'تسوية نهاية العقد',
            'خصم الولاء للعميل',
        ];
        
        return $reasons[array_rand($reasons)];
    }

    private function getRandomCreditNoteDescription()
    {
        $descriptions = [
            'إشعار دائن',
            'قصائد مديونة',
            'إشعار دائن - تعديل الفاتورة',
            'إشعار دائن - خصم تعاقدي',
            'إشعار دائن - تسوية',
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
}
