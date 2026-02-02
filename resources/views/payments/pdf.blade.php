<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير الدفعات</title>
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
        .status-completed {
            color: #28a745;
            font-weight: bold;
        }
        .status-pending {
            color: #ffc107;
            font-weight: bold;
        }
        .status-cancelled {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>تقرير الدفعات</h1>
        <p>تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>
        <p>إجمالي الدفعات: {{ $payments->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>رقم الدفعة</th>
                <th>رقم الفاتورة</th>
                <th>العميل</th>
                <th>تاريخ الدفع</th>
                <th>المبلغ</th>
                <th>طريقة الدفع</th>
                <th>الحالة</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
            @endphp
            @foreach($payments as $payment)
                @php
                    $totalAmount += $payment->amount;
                    
                    $methodMap = [
                        'cash' => 'نقدي',
                        'bank_transfer' => 'تحويل بنكي',
                        'check' => 'شيك',
                        'credit_card' => 'بطاقة ائتمان',
                        'other' => 'أخرى',
                    ];
                    
                    $statusClass = '';
                    $statusLabel = '';
                    switch($payment->status) {
                        case 'completed':
                            $statusClass = 'status-completed';
                            $statusLabel = 'مكتمل';
                            break;
                        case 'pending':
                            $statusClass = 'status-pending';
                            $statusLabel = 'قيد الانتظار';
                            break;
                        case 'cancelled':
                            $statusClass = 'status-cancelled';
                            $statusLabel = 'ملغى';
                            break;
                        default:
                            $statusLabel = $payment->status;
                    }
                @endphp
                <tr>
                    <td>{{ $payment->number }}</td>
                    <td>{{ $payment->invoice->number ?? '-' }}</td>
                    <td>{{ $payment->invoice->client->name ?? '-' }}</td>
                    <td>{{ $payment->payment_date }}</td>
                    <td>{{ number_format($payment->amount, 2) }} ر.س</td>
                    <td>{{ $methodMap[$payment->payment_method] ?? $payment->payment_method }}</td>
                    <td class="{{ $statusClass }}">{{ $statusLabel }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="4">الإجمالي</td>
                <td>{{ number_format($totalAmount, 2) }} ر.س</td>
                <td colspan="2">-</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>تم إنشاء هذا التقرير تلقائياً بواسطة نظام إدارة الدفعات</p>
        <p>{{ config('app.name') }} &copy; {{ date('Y') }}</p>
    </div>
</body>
</html>
