@extends('layouts.master')

@section('title', 'تقرير الفواتير الملغاة')
@section('page_title', 'التقارير - الفواتير الملغاة')
@section('page_subtitle', 'عرض جميع الفواتير التي تم إلغاؤها')

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
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <p class="text-muted small mb-1">عدد الفواتير الملغاة</p>
                        <h3 class="fw-bold mb-0 text-danger">{{ $invoices->count() }}</h3>
                    </div>
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
                                <th class="px-4 py-3 border-0 text-center">التاريخ</th>
                                <th class="px-4 py-3 border-0 text-center">المبلغ الإجمالي</th>
                                <th class="px-4 py-3 border-0 text-center">السبب / الملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                                <tr>
                                    <td class="px-4 py-3 font-monospace fw-bold">{{ $invoice->number }}</td>
                                    <td class="px-4 py-3">{{ $invoice->client->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center text-muted small">{{ $invoice->updated_at->format('Y-m-d') }}</td>
                                    <td class="px-4 py-3 text-center fw-bold">{{ number_format($invoice->total_price, 0) }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-muted small">{{ $invoice->notes ?? 'لا يوجد ملاحظات' }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">لا يوجد فواتير ملغاة</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
