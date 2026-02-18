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
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" colspan="2">الإشعار</th>
                            <th class="px-4 py-3 text-center" colspan="3">البيانات السابقة</th>
                            <th class="px-4 py-3 text-center bg-warning bg-opacity-10" colspan="1">قيمة الخصم</th>
                            <th class="px-4 py-3 text-center" colspan="3">البيانات الجديدة</th>
                            <th class="px-4 py-3 text-center">السبب</th>
                            <th class="px-4 py-3 text-center"></th>
                        </tr>
                        <tr>
                            <th class="px-4 py-2">رقم الإشعار</th>
                            <th class="px-4 py-2">التاريخ</th>

                            <!-- Previous Data -->
                            <th class="px-4 py-2 text-center">المبلغ قبل الضريبة</th>
                            <th class="px-4 py-2 text-center">الضريبة</th>
                            <th class="px-4 py-2 text-center">الإجمالي</th>

                            <!-- Credit Amount -->
                            <th class="px-4 py-2 text-center bg-warning bg-opacity-10">مبلغ الخصم</th>

                            <!-- New Data -->
                            <th class="px-4 py-2 text-center">المبلغ قبل الضريبة</th>
                            <th class="px-4 py-2 text-center">الضريبة</th>
                            <th class="px-4 py-2 text-center">الإجمالي</th>

                            <th class="px-4 py-2 text-center">السبب</th>
                            <th class="px-4 py-2 text-center"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($creditNotes as $creditNote)
                            @php
                                $previous = $creditNote->previous_values ?? [];
                                $new = $creditNote->new_values ?? [];

                                $previousBase = $previous['base_price'] ?? $creditNote->previous_base_price;
                                $previousTax = $previous['tax_amount'] ?? 0;
                                $previousTotal = $previous['total_price'] ?? $creditNote->previous_total;

                                $newBase = $new['base_price'] ?? $creditNote->new_base_price;
                                $newTax = $new['tax_amount'] ?? 0;
                                $newTotal = $new['total_price'] ?? $creditNote->new_total;

                                $taxRate = $invoice->tax_rate;
                            @endphp
                            <tr>
                                <!-- Credit Note Info -->
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong class="text-primary d-block">{{ $creditNote->credit_note_number }}</strong>
                                            <span class="badge bg-{{ $creditNote->type === 'internal' ? 'primary' : 'success' }} badge-sm mt-1">
                                                    {{ $creditNote->type === 'internal' ? 'داخلي' : 'للعميل' }}
                                                </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar3 text-muted ms-2"></i>
                                        <div>
                                            <div>{{ \Carbon\Carbon::parse($creditNote->issue_date ?? $creditNote->created_at)->format('Y-m-d') }}</div>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($creditNote->created_at)->format('h:i A') }}</small>
                                        </div>
                                    </div>
                                </td>

                                <!-- Previous Data -->
                                <td class="px-4 py-3 text-center">
                                    <div class="fw-bold">{{ number_format($previousBase, 0) }} <small>ر.س</small></div>
                                    @if(isset($previous['employees_count']) || isset($previous['work_days_count']))
                                        <div class="mt-1">
                                            @if(isset($previous['employees_count']))
                                                <span class="badge bg-light text-dark me-1" title="عدد الموظفين">
                                                        <i class="bi bi-people-fill me-1"></i>{{ $previous['employees_count'] }}
                                                    </span>
                                            @endif
                                            @if(isset($previous['work_days_count']))
                                                <span class="badge bg-light text-dark" title="أيام العمل">
                                                        <i class="bi bi-calendar-check me-1"></i>{{ $previous['work_days_count'] }}
                                                    </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div>{{ number_format($previousTax, 0) }} <small>ر.س</small></div>
                                    <small class="text-muted">({{ $previous['tax_rate'] ?? $taxRate }}%)</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="fw-bold text-danger">{{ number_format($previousTotal, 0) }} <small>ر.س</small></span>
                                </td>

                                <!-- Credit Amount -->
                                <td class="px-4 py-3 text-center bg-warning bg-opacity-10">
                                    <div class="fw-bold fs-5 text-warning">{{ number_format($creditNote->amount_difference, 0) }} <small>ر.س</small></div>
                                    <small class="text-muted">خصم</small>
                                </td>

                                <!-- New Data -->
                                <td class="px-4 py-3 text-center">
                                    <div class="fw-bold text-success">{{ number_format($newBase, 0) }} <small>ر.س</small></div>
                                    @if(isset($new['employees_count']) || isset($new['work_days_count']))
                                        <div class="mt-1">
                                            @if(isset($new['employees_count']))
                                                <span class="badge bg-light text-dark me-1" title="عدد الموظفين">
                                                        <i class="bi bi-people-fill me-1"></i>{{ $new['employees_count'] }}
                                                    </span>
                                            @endif
                                            @if(isset($new['work_days_count']))
                                                <span class="badge bg-light text-dark" title="أيام العمل">
                                                        <i class="bi bi-calendar-check me-1"></i>{{ $new['work_days_count'] }}
                                                    </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="text-info">{{ number_format($newTax, 0) }} <small>ر.س</small></div>
                                    <small class="text-muted">({{ $new['tax_rate'] ?? $taxRate }}%)</small>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="fw-bold text-success">{{ number_format($newTotal, 0) }} <small>ر.س</small></span>
                                </td>

                                <!-- Reason & Actions -->
                                <td class="px-4 py-3">
                                        <span class="text-muted" title="{{ $creditNote->reason ?? '-' }}">
                                            {{ Str::limit($creditNote->reason ?? '-', 20) }}
                                        </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('invoices.credit-notes.show', [$invoice, $creditNote]) }}"
                                       class="btn btn-sm btn-outline-primary rounded-circle"
                                       title="عرض التفاصيل">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                        <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-end fw-bold">إجمالي الخصومات:</td>
                            <td class="px-4 py-3 text-center bg-warning bg-opacity-10">
                                <span class="fw-bold fs-5 text-warning">{{ number_format($creditNotes->sum('amount_difference'), 0) }} ر.س</span>
                            </td>
                            <td colspan="5"></td>
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
