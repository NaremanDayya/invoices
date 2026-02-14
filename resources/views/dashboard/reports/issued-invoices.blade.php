@extends('layouts.master')

@section('title', 'تقرير الفواتير الصادرة')
@section('page_title', 'التقارير - الفواتير الصادرة')
@section('page_subtitle', 'عرض جميع الفواتير الصادرة والنشطة')

@section('page_actions')
    <a href="{{ route('dashboard.reports.issued-invoices') }}" class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold" onclick="window.print()">
        <i class="bi bi-printer me-2"></i>
        <span>طباعة التقرير</span>
    </a>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Summary Cards -->
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                        <p class="text-muted small mb-1">إجمالي مبلغ الفواتير</p>
                        <h3 class="fw-bold mb-0 text-primary-accent">{{ number_format($invoices->sum('total_price'), 0) }} ر.س</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                        <p class="text-muted small mb-1">عدد الفواتير</p>
                        <h3 class="fw-bold mb-0">{{ $invoices->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th class="px-4 py-3 border-0">رقم الفاتورة</th>
                                <th class="px-4 py-3 border-0">العميل</th>
                                <th class="px-4 py-3 border-0 text-center">التاريخ</th>
                                <th class="px-4 py-3 border-0 text-center">المبلغ</th>
                                <th class="px-4 py-3 border-0 text-center">الضريبة</th>
                                <th class="px-4 py-3 border-0 text-center">الإجمالي</th>
                                <th class="px-4 py-3 border-0 text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="fw-bold text-dark font-monospace">{{ $invoice->number }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold">{{ $invoice->client->name ?? '—' }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-center text-muted small">
                                        {{ $invoice->generation_date ? \Carbon\Carbon::parse($invoice->generation_date)->format('Y-m-d') : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ number_format($invoice->base_price, 0) }}</td>
                                    <td class="px-4 py-3 text-center text-muted small">{{ number_format($invoice->tax_amount, 0) }}</td>
                                    <td class="px-4 py-3 text-center fw-bold text-success">{{ number_format($invoice->total_price, 0) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge rounded-pill bg-{{ $invoice->payment_status == 'paid' ? 'success' : 'warning' }} px-3">
                                            {{ $invoice->payment_status == 'paid' ? 'مدفوعة' : 'معلقة' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">لا يوجد بيانات لعرضها</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
