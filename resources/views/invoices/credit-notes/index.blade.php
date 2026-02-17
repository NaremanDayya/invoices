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
                        <small class="text-muted d-block">المبلغ الأصلي قبل الضريبة</small>
                        <strong class="text-primary">{{ number_format($invoice->base_price + $creditNotes->sum('amount_difference'), 0) }} ر.س</strong>
                        <br>
                        <small class="text-muted">(قبل الخصم)</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <small class="text-muted d-block">المبلغ الحالي قبل الضريبة</small>
                        <strong class="text-success">{{ number_format($invoice->base_price, 0) }} ر.س</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <small class="text-muted d-block">عدد الإشعارات</small>
                        <strong class="text-warning">{{ $creditNotes->count() }}</strong>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-3">
                    <div class="mb-3">
                        <small class="text-muted d-block">نسبة الضريبة</small>
                        <strong>{{ $invoice->tax_rate }}%</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <small class="text-muted d-block">إجمالي الضريبة الحالية</small>
                        <strong class="text-info">{{ number_format($invoice->tax_amount, 0) }} ر.س</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <small class="text-muted d-block">الإجمالي مع الضريبة</small>
                        <strong class="text-success">{{ number_format($invoice->total_price, 0) }} ر.س</strong>
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
                            <th class="px-4 py-3">المبلغ قبل الضريبة (سابق)</th>
                            <th class="px-4 py-3">المبلغ قبل الضريبة (جديد)</th>
                            <th class="px-4 py-3">الضريبة (سابق)</th>
                            <th class="px-4 py-3">الضريبة (جديد)</th>
                            <th class="px-4 py-3">الإجمالي مع الضريبة (سابق)</th>
                            <th class="px-4 py-3">الإجمالي مع الضريبة (جديد)</th>
                            <th class="px-4 py-3">مبلغ الخصم</th>
                            <th class="px-4 py-3">تفاصيل إضافية</th>
                            <th class="px-4 py-3">السبب</th>
                            <th class="px-4 py-3 text-center">إجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($creditNotes as $creditNote)
                            @php
                                $previous = $creditNote->previous_values ?? [];
                                $new = $creditNote->new_values ?? [];
                            @endphp
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
                                    {{ \Carbon\Carbon::parse($creditNote->issue_date ?? $creditNote->created_at)->format('Y-m-d') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($creditNote->created_at)->format('h:i A') }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="fw-bold">{{ number_format($previous['base_price'] ?? $creditNote->previous_base_price, 0) }} ر.س</div>
                                    @if(isset($previous['employees_count']) || isset($previous['work_days_count']))
                                        <small class="text-muted d-block">
                                            @if(isset($previous['employees_count'])) موظفين: {{ $previous['employees_count'] }} @endif
                                            @if(isset($previous['work_days_count'])) | أيام: {{ $previous['work_days_count'] }} @endif
                                        </small>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="fw-bold text-success">{{ number_format($new['base_price'] ?? $creditNote->new_base_price, 0) }} ر.س</div>
                                    @if(isset($new['employees_count']) || isset($new['work_days_count']))
                                        <small class="text-muted d-block">
                                            @if(isset($new['employees_count'])) موظفين: {{ $new['employees_count'] }} @endif
                                            @if(isset($new['work_days_count'])) | أيام: {{ $new['work_days_count'] }} @endif
                                        </small>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ number_format($previous['tax_amount'] ?? 0, 0) }} ر.س
                                    <br>
                                    <small class="text-muted">({{ $previous['tax_rate'] ?? $invoice->tax_rate }}%)</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-info">{{ number_format($new['tax_amount'] ?? 0, 0) }} ر.س</span>
                                    <br>
                                    <small class="text-muted">({{ $new['tax_rate'] ?? $invoice->tax_rate }}%)</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <strong class="text-danger">{{ number_format($previous['total_price'] ?? $creditNote->previous_total, 0) }} ر.س</strong>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <strong class="text-success">{{ number_format($new['total_price'] ?? $creditNote->new_total, 0) }} ر.س</strong>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <strong class="text-warning fs-5">{{ number_format($creditNote->amount_difference, 0) }} ر.س</strong>
                                </td>
                                <td class="px-4 py-3">
                                    @if(isset($previous['total_workers']) || isset($previous['work_days']))
                                        <small class="d-block"><i class="bi bi-people me-1"></i>عمال: {{ $previous['total_workers'] ?? '-' }}</small>
                                        <small><i class="bi bi-calendar-week me-1"></i>أيام عمل: {{ $previous['work_days'] ?? '-' }}</small>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <small class="text-muted">{{ Str::limit($creditNote->reason ?? '-', 30) }}</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('invoices.credit-notes.show', [$invoice, $creditNote]) }}"
                                       class="btn btn-sm btn-primary"
                                       title="عرض التفاصيل">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                        <tr>
                            <td colspan="8" class="px-4 py-3 text-end"><strong>الإجمالي:</strong></td>
                            <td class="px-4 py-3">
                                <strong class="text-warning fs-5">{{ number_format($creditNotes->sum('amount_difference'), 0) }} ر.س</strong>
                            </td>
                            <td colspan="3"></td>
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
