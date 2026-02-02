<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaymentsTableSeeder extends Seeder
{
    public function run()
    {
        $invoices = Invoice::all();

        if ($invoices->isEmpty()) {
            $this->command->error('Please run InvoicesTableSeeder first!');
            return;
        }

        $payments = [];

        // Get paid invoices and create payment records
        $paidInvoices = Invoice::where('payment_status', 'paid')->get();
        
        foreach ($paidInvoices as $invoice) {
            // Single full payment
            $payments[] = [
                'invoice_id' => $invoice->id,
                'amount' => $invoice->total_price,
                'payment_date' => $invoice->payment_date ?? Carbon::now()->subDays(rand(5, 30)),
                'payment_method' => $this->getRandomPaymentMethod(),
                'reference_number' => 'PAY-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'description' => 'دفعة كاملة للفاتورة',
                'status' => 'completed',
                'created_at' => $invoice->payment_date ?? Carbon::now()->subDays(rand(5, 30)),
                'updated_at' => $invoice->payment_date ?? Carbon::now()->subDays(rand(5, 30)),
            ];
        }

        // Get partially paid invoices and create multiple payment records
        $partiallyPaidInvoices = Invoice::where('paid_amount', '>', 0)
            ->where('paid_amount', '<', \DB::raw('total_price'))
            ->get();

        foreach ($partiallyPaidInvoices as $invoice) {
            $remainingAmount = $invoice->paid_amount;
            $paymentCount = rand(2, 3);
            
            for ($i = 0; $i < $paymentCount; $i++) {
                if ($remainingAmount <= 0) break;
                
                $isLastPayment = ($i === $paymentCount - 1);
                $paymentAmount = $isLastPayment 
                    ? $remainingAmount 
                    : round($remainingAmount / ($paymentCount - $i) * rand(30, 70) / 100, 2);
                
                if ($paymentAmount > $remainingAmount) {
                    $paymentAmount = $remainingAmount;
                }
                
                $payments[] = [
                    'invoice_id' => $invoice->id,
                    'amount' => $paymentAmount,
                    'payment_date' => Carbon::now()->subDays(rand(1, 20) + ($i * 5)),
                    'payment_method' => $this->getRandomPaymentMethod(),
                    'reference_number' => 'PAY-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                    'description' => 'دفعة جزئية ' . ($i + 1) . ' من ' . $paymentCount,
                    'status' => 'completed',
                    'created_at' => Carbon::now()->subDays(rand(1, 20) + ($i * 5)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 20) + ($i * 5)),
                ];
                
                $remainingAmount -= $paymentAmount;
            }
        }

        // Add some pending/failed payments for demonstration
        $pendingInvoices = Invoice::where('payment_status', 'pending')
            ->orWhere('payment_status', 'late')
            ->limit(3)
            ->get();

        foreach ($pendingInvoices as $invoice) {
            // Add a pending payment
            $payments[] = [
                'invoice_id' => $invoice->id,
                'amount' => round($invoice->total_price * 0.5, 2),
                'payment_date' => null,
                'payment_method' => 'bank_transfer',
                'reference_number' => 'PAY-PENDING-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                'description' => 'دفعة قيد المعالجة',
                'status' => 'pending',
                'created_at' => Carbon::now()->subDays(rand(1, 5)),
                'updated_at' => Carbon::now()->subDays(rand(1, 5)),
            ];
        }

        // Create all payments
        foreach ($payments as $payment) {
            Payment::create($payment);
        }

        $this->command->info('Created ' . count($payments) . ' payment records with various scenarios.');
    }

    private function getRandomPaymentMethod()
    {
        $methods = ['bank_transfer', 'cash', 'check', 'credit_card', 'online_payment'];
        return $methods[array_rand($methods)];
    }
}
