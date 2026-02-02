<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class InvoicesTableSeeder extends Seeder
{
    public function run()
    {
        $clients = Client::all();
        $services = Service::all();

        if ($clients->isEmpty() || $services->isEmpty()) {
            $this->command->error('Please run ClientsTableSeeder and ServicesTableSeeder first!');
            return;
        }

        $invoices = [];
        $invoiceNumber = 1;

        // Client 1: Multiple invoices with various statuses
        $client1 = $clients[0];
        $service1 = $services[0];

        // Fully paid invoice
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client1->id,
            'service_id' => $service1->id,
            'generation_date' => Carbon::now()->subMonths(3),
            'last_generation_date' => Carbon::now()->subMonths(3)->addDays(30),
            'due_date' => Carbon::now()->subMonths(3)->addDays(30),
            'grace_period_days' => 30,
            'approval_date' => Carbon::now()->subMonths(3)->addDays(2),
            'payment_date' => Carbon::now()->subMonths(3)->addDays(15),
            'total_workers' => 25,
            'total_supervisors' => 3,
            'total_managers' => 1,
            'total_users' => 0,
            'work_days' => 22,
            'daily_rate' => 150.00,
            'base_price' => 96900.00,
            'tax_rate' => 15.00,
            'tax_amount' => 14535.00,
            'total_price' => 111435.00,
            'paid_amount' => 111435.00,
            'amount_difference' => 0,
            'payment_status' => 'paid',
            'invoice_status' => 'completed',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'human_resource',
            'requires_hr_details' => true,
        ];

        // Pending invoice
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client1->id,
            'service_id' => $service1->id,
            'generation_date' => Carbon::now()->subDays(10),
            'last_generation_date' => Carbon::now()->addDays(20),
            'due_date' => Carbon::now()->addDays(20),
            'grace_period_days' => 30,
            'approval_date' => Carbon::now()->subDays(8),
            'payment_date' => null,
            'total_workers' => 30,
            'total_supervisors' => 4,
            'total_managers' => 2,
            'total_users' => 0,
            'work_days' => 21,
            'daily_rate' => 150.00,
            'base_price' => 113400.00,
            'tax_rate' => 15.00,
            'tax_amount' => 17010.00,
            'total_price' => 130410.00,
            'paid_amount' => 0,
            'amount_difference' => 0,
            'payment_status' => 'pending',
            'invoice_status' => 'issued',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'human_resource',
            'requires_hr_details' => true,
        ];

        // Client 2: Overdue invoice
        $client2 = $clients[1];
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client2->id,
            'service_id' => $services[1]->id,
            'generation_date' => Carbon::now()->subMonths(2),
            'last_generation_date' => Carbon::now()->subMonths(1)->subDays(15),
            'due_date' => Carbon::now()->subMonths(1)->subDays(15),
            'grace_period_days' => 45,
            'approval_date' => Carbon::now()->subMonths(2)->addDays(1),
            'payment_date' => null,
            'total_workers' => 20,
            'total_supervisors' => 2,
            'total_managers' => 1,
            'total_users' => 0,
            'work_days' => 20,
            'daily_rate' => 180.00,
            'base_price' => 82800.00,
            'tax_rate' => 15.00,
            'tax_amount' => 12420.00,
            'total_price' => 95220.00,
            'paid_amount' => 0,
            'amount_difference' => 0,
            'payment_status' => 'overdue',
            'invoice_status' => 'issued',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 60,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'security',
            'requires_hr_details' => false,
        ];

        // Late payment invoice
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client2->id,
            'service_id' => $services[2]->id,
            'generation_date' => Carbon::now()->subDays(40),
            'last_generation_date' => Carbon::now()->subDays(10),
            'due_date' => Carbon::now()->subDays(10),
            'grace_period_days' => 45,
            'approval_date' => Carbon::now()->subDays(38),
            'payment_date' => null,
            'total_workers' => 15,
            'total_supervisors' => 2,
            'total_managers' => 0,
            'total_users' => 0,
            'work_days' => 22,
            'daily_rate' => 120.00,
            'base_price' => 44880.00,
            'tax_rate' => 15.00,
            'tax_amount' => 6732.00,
            'total_price' => 51612.00,
            'paid_amount' => 0,
            'amount_difference' => 0,
            'payment_status' => 'late',
            'invoice_status' => 'issued',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 10,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'cleaning',
            'requires_hr_details' => false,
        ];

        // Client 3: Invoice with credit notes (will be added in CreditNotesSeeder)
        $client3 = $clients[2];
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client3->id,
            'service_id' => $services[0]->id,
            'generation_date' => Carbon::now()->subMonths(1),
            'last_generation_date' => Carbon::now()->addDays(15),
            'due_date' => Carbon::now()->addDays(15),
            'grace_period_days' => 60,
            'approval_date' => Carbon::now()->subMonths(1)->addDays(1),
            'payment_date' => null,
            'total_workers' => 40,
            'total_supervisors' => 5,
            'total_managers' => 2,
            'total_users' => 1,
            'work_days' => 21,
            'daily_rate' => 160.00,
            'base_price' => 161280.00,
            'tax_rate' => 15.00,
            'tax_amount' => 24192.00,
            'total_price' => 185472.00,
            'paid_amount' => 0,
            'amount_difference' => 0,
            'payment_status' => 'pending',
            'invoice_status' => 'issued',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'human_resource',
            'requires_hr_details' => true,
        ];

        // Client 4: Partially paid invoice
        $client4 = $clients[3];
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client4->id,
            'service_id' => $services[3]->id,
            'generation_date' => Carbon::now()->subDays(25),
            'last_generation_date' => Carbon::now()->addDays(5),
            'due_date' => Carbon::now()->addDays(5),
            'grace_period_days' => 30,
            'approval_date' => Carbon::now()->subDays(23),
            'payment_date' => null,
            'total_workers' => 10,
            'total_supervisors' => 1,
            'total_managers' => 1,
            'total_users' => 2,
            'work_days' => 20,
            'daily_rate' => 200.00,
            'base_price' => 56000.00,
            'tax_rate' => 15.00,
            'tax_amount' => 8400.00,
            'total_price' => 64400.00,
            'paid_amount' => 30000.00,
            'amount_difference' => 0,
            'payment_status' => 'pending',
            'invoice_status' => 'issued',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'consulting',
            'requires_hr_details' => false,
        ];

        // Client 5: Cancelled invoice
        $client5 = $clients[4];
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client5->id,
            'service_id' => $services[4]->id,
            'generation_date' => Carbon::now()->subMonths(2),
            'last_generation_date' => Carbon::now()->subMonths(1)->subDays(15),
            'due_date' => Carbon::now()->subMonths(1)->subDays(15),
            'grace_period_days' => 30,
            'approval_date' => null,
            'payment_date' => null,
            'total_workers' => 5,
            'total_supervisors' => 0,
            'total_managers' => 0,
            'total_users' => 3,
            'work_days' => 15,
            'daily_rate' => 250.00,
            'base_price' => 30000.00,
            'tax_rate' => 15.00,
            'tax_amount' => 4500.00,
            'total_price' => 34500.00,
            'paid_amount' => 0,
            'amount_difference' => 0,
            'payment_status' => 'pending',
            'invoice_status' => 'cancelled',
            'is_cancelled' => true,
            'cancelled_at' => Carbon::now()->subMonths(2)->addDays(5),
            'cancellation_reason' => 'تم إلغاء المشروع من قبل العميل',
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'it_services',
            'requires_hr_details' => false,
        ];

        // Client 6: Multiple fully paid invoices
        $client6 = $clients[5];
        for ($i = 0; $i < 3; $i++) {
            $invoices[] = [
                'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
                'client_id' => $client6->id,
                'service_id' => $services[$i % count($services)]->id,
                'generation_date' => Carbon::now()->subMonths(4 - $i),
                'last_generation_date' => Carbon::now()->subMonths(4 - $i)->addDays(30),
                'due_date' => Carbon::now()->subMonths(4 - $i)->addDays(30),
                'grace_period_days' => 30,
                'approval_date' => Carbon::now()->subMonths(4 - $i)->addDays(1),
                'payment_date' => Carbon::now()->subMonths(4 - $i)->addDays(20),
                'total_workers' => 18 + ($i * 2),
                'total_supervisors' => 2,
                'total_managers' => 1,
                'total_users' => 0,
                'work_days' => 21,
                'daily_rate' => 140.00,
                'base_price' => (18 + ($i * 2) + 3) * 21 * 140.00,
                'tax_rate' => 15.00,
                'tax_amount' => ((18 + ($i * 2) + 3) * 21 * 140.00) * 0.15,
                'total_price' => ((18 + ($i * 2) + 3) * 21 * 140.00) * 1.15,
                'paid_amount' => ((18 + ($i * 2) + 3) * 21 * 140.00) * 1.15,
                'amount_difference' => 0,
                'payment_status' => 'paid',
                'invoice_status' => 'completed',
                'is_cancelled' => false,
                'issue_delay_days' => 0,
                'payment_delay_days' => 0,
                'credit_notes_count' => 0,
                'total_credit_notes' => 0,
                'service_type' => 'general',
                'requires_hr_details' => false,
            ];
        }

        // Client 7: Mixed payment statuses
        $client7 = $clients[6];
        $statuses = ['paid', 'pending', 'late'];
        for ($i = 0; $i < 3; $i++) {
            $status = $statuses[$i];
            $isPaid = $status === 'paid';
            $isLate = $status === 'late';
            
            $basePrice = 75000.00;
            $taxAmount = $basePrice * 0.15;
            $totalPrice = $basePrice + $taxAmount;
            
            $invoices[] = [
                'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
                'client_id' => $client7->id,
                'service_id' => $services[5]->id,
                'generation_date' => Carbon::now()->subMonths(2 - $i),
                'last_generation_date' => Carbon::now()->subMonths(2 - $i)->addDays(30),
                'due_date' => Carbon::now()->subMonths(2 - $i)->addDays(30),
                'grace_period_days' => 15,
                'approval_date' => Carbon::now()->subMonths(2 - $i)->addDays(1),
                'payment_date' => $isPaid ? Carbon::now()->subMonths(2 - $i)->addDays(25) : null,
                'total_workers' => 22,
                'total_supervisors' => 3,
                'total_managers' => 1,
                'total_users' => 0,
                'work_days' => 20,
                'daily_rate' => 144.23,
                'base_price' => $basePrice,
                'tax_rate' => 15.00,
                'tax_amount' => $taxAmount,
                'total_price' => $totalPrice,
                'paid_amount' => $isPaid ? $totalPrice : 0,
                'amount_difference' => 0,
                'payment_status' => $status,
                'invoice_status' => $isPaid ? 'completed' : 'issued',
                'is_cancelled' => false,
                'issue_delay_days' => 0,
                'payment_delay_days' => $isLate ? 5 : 0,
                'credit_notes_count' => 0,
                'total_credit_notes' => 0,
                'service_type' => 'training',
                'requires_hr_details' => false,
            ];
        }

        // Client 8: Large invoice
        $client8 = $clients[7];
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client8->id,
            'service_id' => $services[6]->id,
            'generation_date' => Carbon::now()->subDays(15),
            'last_generation_date' => Carbon::now()->addDays(15),
            'due_date' => Carbon::now()->addDays(15),
            'grace_period_days' => 30,
            'approval_date' => Carbon::now()->subDays(13),
            'payment_date' => null,
            'total_workers' => 100,
            'total_supervisors' => 10,
            'total_managers' => 5,
            'total_users' => 2,
            'work_days' => 22,
            'daily_rate' => 155.00,
            'base_price' => 399190.00,
            'tax_rate' => 15.00,
            'tax_amount' => 59878.50,
            'total_price' => 459068.50,
            'paid_amount' => 0,
            'amount_difference' => 0,
            'payment_status' => 'pending',
            'invoice_status' => 'issued',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'construction',
            'requires_hr_details' => true,
        ];

        // Client 9: Small invoices
        $client9 = $clients[8];
        for ($i = 0; $i < 2; $i++) {
            $invoices[] = [
                'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
                'client_id' => $client9->id,
                'service_id' => $services[7]->id,
                'generation_date' => Carbon::now()->subDays(30 + ($i * 15)),
                'last_generation_date' => Carbon::now()->subDays(($i * 15)),
                'due_date' => Carbon::now()->subDays(($i * 15)),
                'grace_period_days' => 45,
                'approval_date' => Carbon::now()->subDays(28 + ($i * 15)),
                'payment_date' => $i === 0 ? Carbon::now()->subDays(5) : null,
                'total_workers' => 5,
                'total_supervisors' => 1,
                'total_managers' => 0,
                'total_users' => 0,
                'work_days' => 15,
                'daily_rate' => 110.00,
                'base_price' => 9900.00,
                'tax_rate' => 15.00,
                'tax_amount' => 1485.00,
                'total_price' => 11385.00,
                'paid_amount' => $i === 0 ? 11385.00 : 0,
                'amount_difference' => 0,
                'payment_status' => $i === 0 ? 'paid' : 'late',
                'invoice_status' => $i === 0 ? 'completed' : 'issued',
                'is_cancelled' => false,
                'issue_delay_days' => 0,
                'payment_delay_days' => $i === 1 ? 15 : 0,
                'credit_notes_count' => 0,
                'total_credit_notes' => 0,
                'service_type' => 'logistics',
                'requires_hr_details' => false,
            ];
        }

        // Client 10: Recent invoices
        $client10 = $clients[9];
        $invoices[] = [
            'number' => 'INV-' . date('Y') . '-' . str_pad($invoiceNumber++, 4, '0', STR_PAD_LEFT),
            'client_id' => $client10->id,
            'service_id' => $services[0]->id,
            'generation_date' => Carbon::now()->subDays(5),
            'last_generation_date' => Carbon::now()->addDays(25),
            'due_date' => Carbon::now()->addDays(25),
            'grace_period_days' => 30,
            'approval_date' => Carbon::now()->subDays(3),
            'payment_date' => null,
            'total_workers' => 35,
            'total_supervisors' => 4,
            'total_managers' => 2,
            'total_users' => 1,
            'work_days' => 21,
            'daily_rate' => 145.00,
            'base_price' => 127890.00,
            'tax_rate' => 15.00,
            'tax_amount' => 19183.50,
            'total_price' => 147073.50,
            'paid_amount' => 0,
            'amount_difference' => 0,
            'payment_status' => 'pending',
            'invoice_status' => 'issued',
            'is_cancelled' => false,
            'issue_delay_days' => 0,
            'payment_delay_days' => 0,
            'credit_notes_count' => 0,
            'total_credit_notes' => 0,
            'service_type' => 'human_resource',
            'requires_hr_details' => true,
        ];

        // Create all invoices
        foreach ($invoices as $invoice) {
            Invoice::create($invoice);
        }

        $this->command->info('Created ' . count($invoices) . ' invoices with various scenarios.');
    }
}
