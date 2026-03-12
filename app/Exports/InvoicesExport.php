<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use App\Helpers\NumberHelper;

class InvoicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $invoices;
    protected $visibleColumns;

    public function __construct($invoices, $visibleColumns = null)
    {
        $this->invoices = $invoices;
        $this->visibleColumns = $visibleColumns ?? $this->getDefaultColumns();
    }

    protected function getDefaultColumns()
    {
        return [
            'number' => 'رقم الفاتورة',
            'client' => 'العميل',
            'service' => 'الخدمة',
            'generation_date' => 'تاريخ الإصدار',
            'due_date' => 'تاريخ الاستحقاق',
            'employees_count' => 'إجمالي العمالة',
            'work_days' => 'أيام العمل',
            'base_price' => 'المبلغ الأساسي',
            'tax_amount' => 'الضريبة',
            'total_price' => 'المبلغ الإجمالي',
            'paid_amount' => 'المبلغ المدفوع',
            'remaining_amount' => 'المبلغ المتبقي',
            'payment_status' => 'حالة السداد',
            'invoice_status' => 'حالة الفاتورة',
        ];
    }

    protected function isColumnVisible($key)
    {
        if (is_array($this->visibleColumns)) {
            return in_array($key, $this->visibleColumns);
        }
        return true;
    }

    public function collection()
    {
        return $this->invoices;
    }

    public function headings(): array
    {
        $allHeadings = $this->getDefaultColumns();
        $headings = [];

        foreach ($allHeadings as $key => $label) {
            if ($this->isColumnVisible($key)) {
                $headings[] = $label;
            }
        }

        return $headings;
    }

    public function map($invoice): array
    {
        $statusMap = [
            'pending' => 'قيد الانتظار',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
            'late' => 'متأخرة (متابعة)',
            'cancelled' => 'ملغاة',
            'partially_paid' => 'مدفوعة جزئياً',
        ];

        $allData = [
            'number' => $invoice->number,
            'client' => $invoice->client->name ?? '',
            'service' => $invoice->service->name ?? '',
            'generation_date' => $invoice->generation_date,
            'due_date' => $invoice->last_generation_date,
            'employees_count' => NumberHelper::toInteger(($invoice->total_workers ?? 0) + ($invoice->total_supervisors ?? 0) + ($invoice->total_managers ?? 0) + ($invoice->total_users ?? 0)),
            'work_days' => NumberHelper::toInteger($invoice->work_days ?? 0),
            'base_price' => number_format(NumberHelper::toInteger($invoice->base_price), 0),
            'tax_amount' => number_format(NumberHelper::toInteger($invoice->tax_amount), 0),
            'total_price' => number_format(NumberHelper::toInteger($invoice->total_price), 0),
            'paid_amount' => number_format(NumberHelper::toInteger($invoice->paid_amount), 0),
            'remaining_amount' => number_format(NumberHelper::toInteger($invoice->remaining_amount), 0),
            'payment_status' => $statusMap[$invoice->payment_status] ?? $invoice->payment_status,
            'invoice_status' => $invoice->invoice_status ?? '',
        ];

        $row = [];
        foreach ($allData as $key => $value) {
            if ($this->isColumnVisible($key)) {
                $row[] = $value;
            }
        }

        return $row;
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
