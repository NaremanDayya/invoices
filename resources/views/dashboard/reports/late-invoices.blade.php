@extends('layouts.master')

@section('title', 'تقرير الفواتير المتأخرة')
@section('page_title', 'التقارير - الفواتير المتأخرة')
@section('page_subtitle', 'عرض الفواتير التي تجاوزت موعد الاستحقاق')

@section('page_actions')
    <button class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold" onclick="window.print()">
        <i class="bi bi-printer me-2"></i>
        <span>طباعة</span>
    </button>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">إجمالي المبالغ المتأخرة</p>
                    <h3 class="fw-bold mb-0 text-danger">{{ number_format($invoices->sum('total_price'), 2) }} ر.س</h3>
                </div>
                <div class="bg-danger-soft p-3 rounded-circle">
                    <i class="bi bi-alarm text-danger fs-3"></i>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">رقم الفاتورة</th>
                                <th class="px-4 py-3 border-0">العميل</th>
                                <th class="px-4 py-3 border-0 text-center">تاريخ الإصدار</th>
                                <th class="px-4 py-3 border-0 text-center">تاريخ الاستحقاق</th>
                                <th class="px-4 py-3 border-0 text-center">أيام التأخير</th>
                                <th class="px-4 py-3 border-0 text-center">المبلغ</th>
                                <th class="px-4 py-3 border-0 text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($invoice->due_date);
                                    $delay = $dueDate->diffInDays(now());
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 fw-bold">{{ $invoice->number }}</td>
                                    <td class="px-4 py-3">{{ $invoice->client->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center text-muted small">{{ $invoice->generation_date }}</td>
                                    <td class="px-4 py-3 text-center text-danger smallfw-bold">{{ $invoice->due_date }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-danger rounded-pill px-3">{{ $delay }} يوم</span>
                                    </td>
                                    <td class="px-4 py-3 text-center fw-bold">{{ number_format($invoice->total_price, 2) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-light text-danger border border-danger-subtle px-3">{{ $invoice->payment_status }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">لا يوجد فواتير متأخرة حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
