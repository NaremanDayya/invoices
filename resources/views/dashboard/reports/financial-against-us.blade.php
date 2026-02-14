@extends('layouts.master')

@section('title', 'تحصيل مالي - علينا')
@section('page_title', 'التقارير - فروقات مالية (علينا)')
@section('page_subtitle', 'عرض الفواتير التي يوجد بها فروقات سعر لصالح العميل')

@section('page_actions')
    <button class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold" onclick="window.print()">
        <i class="bi bi-printer me-2"></i>
        <span>طباعة</span>
    </button>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-muted small mb-1">إجمالي الفروقات (علينا)</p>
                <h3 class="fw-bold mb-0 text-danger">{{ number_format($invoices->sum('price_difference'), 0) }} ر.س</h3>
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
                                <th class="px-4 py-3 border-0 text-center">المبلغ الأصلي</th>
                                <th class="px-4 py-3 border-0 text-center">فرق السعر (علينا)</th>
                                <th class="px-4 py-3 border-0 text-center">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-3 fw-bold">{{ $invoice->number }}</td>
                                    <td class="px-4 py-3">{{ $invoice->client->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center text-muted">{{ number_format($invoice->total_price, 0) }}</td>
                                    <td class="px-4 py-3 text-center fw-bold text-danger">{{ number_format($invoice->price_difference, 0) }}</td>
                                    <td class="px-4 py-3 text-center text-muted small">{{ $invoice->updated_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">لا يوجد فروقات سعر حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
