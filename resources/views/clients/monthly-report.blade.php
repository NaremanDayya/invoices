@extends('layouts.master')

@section('title', 'التقرير الشهري - ' . $client->name)
@section('page_title', 'التقرير الشهري')
@section('page_subtitle', 'تقرير شهري مفصل لفواتير العميل')

@push('styles')
<style>
    .report-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .stat-box.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-box.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-box.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-box h3 {
        font-size: 2rem;
        font-weight: 800;
        margin: 10px 0;
    }
    .stat-box p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }
    .invoice-row {
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 0;
    }
    .invoice-row:last-child {
        border-bottom: none;
    }
    .payment-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .payment-badge.paid {
        background: #def7ec;
        color: #03543f;
    }
    .payment-badge.partially_paid {
        background: #fef3c7;
        color: #92400e;
    }
    .payment-badge.pending {
        background: #fde8e8;
        color: #9b1c1c;
    }
</style>
@endpush

@section('page_actions')
    <div class="d-flex gap-2">
        <form method="GET" action="{{ route('clients.monthly-report.export', $client) }}" class="d-inline">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="format" value="pdf">
            <button type="submit" class="btn btn-danger rounded-xl px-4 py-2 fw-bold">
                <i class="bi bi-file-pdf me-2"></i>تصدير PDF
            </button>
        </form>
        <form method="GET" action="{{ route('clients.monthly-report.export', $client) }}" class="d-inline">
            <input type="hidden" name="month" value="{{ $month }}">
            <input type="hidden" name="format" value="excel">
            <button type="submit" class="btn btn-success rounded-xl px-4 py-2 fw-bold">
                <i class="bi bi-file-excel me-2"></i>تصدير Excel
            </button>
        </form>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-arrow-right me-2"></i>رجوع
        </a>
    </div>
@endsection

@section('content')
    <!-- Client & Period Info -->
    <div class="report-card">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($client->logo)
                        <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name }}" class="rounded" style="width: 60px; height: 60px; object-fit: contain;">
                    @endif
                    <div>
                        <h4 class="fw-bold mb-1">{{ $client->name }}</h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar3 me-2"></i>
                            الفترة: {{ \Carbon\Carbon::parse($period['start'])->locale('ar')->translatedFormat('d F Y') }} - 
                            {{ \Carbon\Carbon::parse($period['end'])->locale('ar')->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <form method="GET" action="{{ route('clients.monthly-report', $client) }}" class="d-inline-flex gap-2">
                    <input type="month" name="month" value="{{ $month }}" class="form-control" style="width: auto;">
                    <button type="submit" class="btn btn-primary">عرض</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box info">
                <p>عدد الفواتير</p>
                <h3>{{ $summary['total_invoices'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p>إجمالي المبلغ</p>
                <h3>{{ number_format($summary['total_invoiced'], 0) }}</h3>
                <p>ر.س</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box success">
                <p>المبلغ المدفوع</p>
                <h3>{{ number_format($summary['total_paid'], 0) }}</h3>
                <p>ر.س</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box warning">
                <p>المبلغ المتبقي</p>
                <h3>{{ number_format($summary['total_remaining'], 0) }}</h3>
                <p>ر.س</p>
            </div>
        </div>
    </div>

    <!-- Invoice Breakdown -->
    <div class="report-card">
        <h5 class="fw-bold mb-4">
            <i class="bi bi-file-text me-2"></i>
            تفاصيل الفواتير
        </h5>

        @if($invoices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">رقم الفاتورة</th>
                            <th class="text-center">التاريخ</th>
                            <th class="text-center">المبلغ الإجمالي</th>
                            <th class="text-center">المدفوع</th>
                            <th class="text-center">المتبقي</th>
                            <th class="text-center">إشعارات دائنة</th>
                            <th class="text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td class="text-center">
                                <strong class="text-primary">{{ $invoice['number'] }}</strong>
                            </td>
                            <td class="text-center">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ $invoice['date'] }}
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($invoice['total_amount'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-center">
                                <strong class="text-success">{{ number_format($invoice['paid_amount'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-center">
                                <strong class="{{ $invoice['remaining_balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($invoice['remaining_balance'], 0) }}
                                </strong> ر.س
                            </td>
                            <td class="text-center">
                                @if($invoice['credit_notes'] > 0)
                                    <strong class="text-warning">{{ number_format($invoice['credit_notes'], 0) }}</strong> ر.س
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    // Check if invoice is paid, partially paid, or pending based on amounts
                                    if ($invoice['remaining_balance'] <= 0) {
                                        $statusClass = 'paid';
                                        $statusLabel = 'مدفوعة';
                                    } elseif ($invoice['paid_amount'] > 0) {
                                        $statusClass = 'partially_paid';
                                        $statusLabel = 'مدفوعة جزئياً';
                                    } else {
                                        $statusClass = 'pending';
                                        $statusLabel = 'معلقة';
                                    }
                                @endphp
                                <span class="payment-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end">الإجماليات:</td>
                            <td class="text-end">
                                <strong class="fs-5 text-primary">{{ number_format($summary['total_invoiced'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-end">
                                <strong class="fs-5 text-success">{{ number_format($summary['total_paid'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-end">
                                <strong class="fs-5 {{ $summary['total_remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($summary['total_remaining'], 0) }}
                                </strong> ر.س
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h5 class="text-muted mt-3">لا توجد فواتير في هذا الشهر</h5>
            </div>
        @endif
    </div>
@endsection
