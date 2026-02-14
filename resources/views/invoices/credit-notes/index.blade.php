@extends('layouts.master')

@section('title', 'الإشعارات الدائنة - فاتورة ' . $invoice->number)
@section('page_title', 'الإشعارات الدائنة')
@section('page_subtitle', 'عرض جميع الإشعارات الدائنة للفاتورة ' . $invoice->number)

@section('page_actions')
    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
        <i class="bi bi-arrow-right me-2"></i>رجوع للفاتورة
    </a>
@endsection

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-gradient-primary text-white p-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>
                    معلومات الفاتورة
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <span class="badge bg-white text-primary fs-6">{{ $invoice->number }}</span>
            </div>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-3">
                <div class="mb-3">
                    <small class="text-muted d-block">العميل</small>
                    <strong>{{ $invoice->client->name }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <small class="text-muted d-block">المبلغ الإجمالي</small>
                    <strong class="text-primary">{{ number_format($invoice->total_price, 0) }} ر.س</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <small class="text-muted d-block">عدد الإشعارات</small>
                    <strong class="text-warning">{{ $creditNotes->count() }}</strong>
                </div>
            </div>
            <div class="col-md-3">
                <div class="mb-3">
                    <small class="text-muted d-block">إجمالي الخصومات</small>
                    <strong class="text-danger">{{ number_format($creditNotes->sum('amount_difference'), 0) }} ر.س</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-light p-4">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2"></i>
            قائمة الإشعارات الدائنة
        </h5>
    </div>
    <div class="card-body p-0">
        @if($creditNotes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">رقم الإشعار</th>
                            <th class="px-4 py-3">التاريخ</th>
                            <th class="px-4 py-3">المبلغ قبل الضريبة</th>
                            <th class="px-4 py-3">مبلغ الضريبة</th>
                            <th class="px-4 py-3">الإجمالي بعد الضريبة</th>
                            <th class="px-4 py-3">قيمة الفاتورة السابقة</th>
                            <th class="px-4 py-3">قيمة الفاتورة الجديدة</th>
                            <th class="px-4 py-3">أنشئ بواسطة</th>
                            <th class="px-4 py-3 text-center">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($creditNotes as $creditNote)
                        <tr>
                            <td class="px-4 py-3">
                                <strong class="text-primary">{{ $creditNote->credit_note_number }}</strong>
                                <br>
                                <span class="badge bg-{{ $creditNote->type === 'internal' ? 'primary' : 'success' }} badge-sm">
                                    {{ $creditNote->type === 'internal' ? 'داخلي' : 'للعميل' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ $creditNote->created_at->format('Y-m-d') }}
                                <br>
                                <small class="text-muted">{{ $creditNote->created_at->format('h:i A') }}</small>
                            </td>
                            <td class="px-4 py-3">
                                <strong>{{ number_format($creditNote->previous_values['base_price'] ?? 0, 0) }}</strong> ر.س
                                <br>
                                <small class="text-muted">→ {{ number_format($creditNote->new_values['base_price'] ?? 0, 0) }} ر.س</small>
                            </td>
                            <td class="px-4 py-3">
                                <strong>{{ number_format($creditNote->previous_values['tax_amount'] ?? 0, 0) }}</strong> ر.س
                                <br>
                                <small class="text-muted">→ {{ number_format($creditNote->new_values['tax_amount'] ?? 0, 0) }} ر.س</small>
                            </td>
                            <td class="px-4 py-3">
                                <strong class="text-warning">{{ number_format($creditNote->amount_difference, 0) }} ر.س</strong>
                            </td>
                            <td class="px-4 py-3">
                                <strong class="text-danger">{{ number_format($creditNote->previous_total, 0) }} ر.س</strong>
                            </td>
                            <td class="px-4 py-3">
                                <strong class="text-success">{{ number_format($creditNote->new_total, 0) }} ر.س</strong>
                            </td>
                            <td class="px-4 py-3">
                                <i class="bi bi-person-fill me-1 text-muted"></i>
                                {{ $creditNote->creator->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('invoices.credit-notes.show', [$invoice, $creditNote]) }}" 
                                   class="btn btn-sm btn-primary" 
                                   title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i> عرض
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-end"><strong>الإجمالي:</strong></td>
                            <td class="px-4 py-3">
                                <strong class="text-warning fs-5">{{ number_format($creditNotes->sum('amount_difference'), 0) }} ر.س</strong>
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-text display-1 text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد إشعارات دائنة</h5>
                <p class="text-muted">لم يتم إنشاء أي إشعارات دائنة لهذه الفاتورة</p>
            </div>
        @endif
    </div>
</div>
@endsection
