<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $payments;

    public function __construct($payments)
    {
        $this->payments = $payments;
    }

    public function collection()
    {
        return $this->payments;
    }

    public function headings(): array
    {
        return [
            'رقم الدفعة',
            'رقم الفاتورة',
            'العميل',
            'تاريخ الدفع',
            'المبلغ',
            'طريقة الدفع',
            'الحالة',
            'رقم المرجع',
            'اسم البنك',
            'ملاحظات',
        ];
    }

    public function map($payment): array
    {
        $methodMap = [
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'credit_card' => 'بطاقة ائتمان',
            'other' => 'أخرى',
        ];

        $statusMap = [
            'pending' => 'قيد الانتظار',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغى',
        ];

        return [
            $payment->number,
            $payment->invoice->number ?? '',
            $payment->invoice->client->name ?? '',
            $payment->payment_date,
            number_format($payment->amount, 0),
            $methodMap[$payment->payment_method] ?? $payment->payment_method,
            $statusMap[$payment->status] ?? $payment->status,
            $payment->reference_number ?? '',
            $payment->bank_name ?? '',
            $payment->notes ?? '',
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
            'B' => 15,
            'C' => 25,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 20,
            'I' => 20,
            'J' => 30,
        ];
    }
}
