<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الفواتير</title>
    <style>
        @page {
            margin: 20px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 11px;
        }
        th {
            background-color: #f0f0f0;
            padding: 10px 5px;
            border: 1px solid #ddd;
            font-weight: bold;
            text-align: center;
        }
        td {
            padding: 8px 5px;
            border: 1px solid #ddd;
            text-align: center;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .status-paid {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-late {
            color: #dc3545;
            font-weight: bold;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary-item {
            display: inline-block;
            margin: 0 15px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير الفواتير</h1>
        <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>
        <p>إجمالي الفواتير: {{ $invoices->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>العميل</th>
                <th>تاريخ الإصدار</th>
                <th>المبلغ الإجمالي</th>
                <th>المبلغ المدفوع</th>
                <th>المبلغ المتبقي</th>
                <th>حالة السداد</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
                $totalPaid = 0;
                $totalRemaining = 0;
            @endphp
            @foreach($invoices as $invoice)
                @php
                    $totalAmount += $invoice->total_price;
                    $totalPaid += $invoice->paid_amount;
                    $totalRemaining += $invoice->remaining_amount;
                    
                    $statusClass = '';
                    $statusLabel = '';
                    switch($invoice->payment_status) {
                        case 'paid':
                            $statusClass = 'status-paid';
                            $statusLabel = 'مدفوعة';
                            break;
                        case 'pending':
                            $statusClass = 'status-pending';
                            $statusLabel = 'قيد الانتظار';
                            break;
                        case 'late':
                        case 'overdue':
                            $statusClass = 'status-late';
                            $statusLabel = 'متأخرة';
                            break;
                        default:
                            $statusLabel = $invoice->payment_status;
                    }
                @endphp
                <tr>
                    <td>{{ $invoice->number }}</td>
                    <td>{{ $invoice->client->name ?? '-' }}</td>
                    <td>{{ $invoice->generation_date }}</td>
                    <td>{{ number_format($invoice->total_price, 0) }} ر.س</td>
                    <td>{{ number_format($invoice->paid_amount, 0) }} ر.س</td>
                    <td>{{ number_format($invoice->remaining_amount, 0) }} ر.س</td>
                    <td class="{{ $statusClass }}">{{ $statusLabel }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="3">الإجمالي</td>
                <td>{{ number_format($totalAmount, 0) }} ر.س</td>
                <td>{{ number_format($totalPaid, 0) }} ر.س</td>
                <td>{{ number_format($totalRemaining, 0) }} ر.س</td>
                <td>-</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير تلقائياً بواسطة نظام إدارة الفواتير</p>
        <p>{{ config('app.name') }} &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
