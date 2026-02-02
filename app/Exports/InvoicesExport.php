<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $invoices;

    public function __construct($invoices)
    {
        $this->invoices = $invoices;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        return [
            'رقم الفاتورة',
            'العميل',
            'الخدمة',
            'تاريخ الإصدار',
            'تاريخ الاستحقاق',
            'إجمالي العمالة',
            'أيام العمل',
            'المبلغ الأساسي',
            'الضريبة',
            'المبلغ الإجمالي',
            'المبلغ المدفوع',
            'المبلغ المتبقي',
            'حالة السداد',
            'حالة الفاتورة',
        ];
    }

    public function map($invoice): array
    {
        $statusMap = [
            'pending' => 'قيد الانتظار',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
            'late' => 'متأخرة (متابعة)',
            'cancelled' => 'ملغاة',
        ];

        return [
            $invoice->number,
            $invoice->client->name ?? '',
            $invoice->service->name ?? '',
            $invoice->generation_date,
            $invoice->last_generation_date,
            ($invoice->total_workers ?? 0) + ($invoice->total_supervisors ?? 0) + ($invoice->total_managers ?? 0) + ($invoice->total_users ?? 0),
            $invoice->work_days ?? 0,
            number_format($invoice->base_price, 2),
            number_format($invoice->tax_amount, 2),
            number_format($invoice->total_price, 2),
            number_format($invoice->paid_amount, 2),
            number_format($invoice->remaining_amount, 2),
            $statusMap[$invoice->payment_status] ?? $invoice->payment_status,
            $invoice->invoice_status ?? '',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 20,
            'D' => 15,
            'E' => 15,
            'F' => 12,
            'G' => 12,
            'H' => 15,
            'I' => 12,
            'J' => 15,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 20,
        ];
    }
}
