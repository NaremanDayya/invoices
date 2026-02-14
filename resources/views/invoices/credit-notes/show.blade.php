@extends('layouts.master')

@section('title', 'تفاصيل الإشعار الدائن - ' . $creditNote->credit_note_number)
@section('page_title', 'تفاصيل الإشعار الدائن')
@section('page_subtitle', $creditNote->credit_note_number)

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.credit-notes.index', $invoice) }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-arrow-right me-2"></i>رجوع للقائمة
        </a>
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-file-text me-2"></i>عرض الفاتورة
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-gradient-primary text-white p-4">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>
                    معلومات الإشعار الدائن
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">رقم الإشعار</small>
                            <strong class="fs-5 text-primary">{{ $creditNote->credit_note_number }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">نوع الإشعار</small>
                            <span class="badge bg-{{ $creditNote->type === 'internal' ? 'primary' : 'success' }} fs-6">
                                {{ $creditNote->type === 'internal' ? 'داخلي (لنا)' : 'للعميل' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">تاريخ الإنشاء</small>
                            <strong>
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $creditNote->created_at->locale('ar')->translatedFormat('d F Y - h:i A') }}
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <small class="text-muted d-block">أنشئ بواسطة</small>
                            <strong>
                                <i class="bi bi-person-fill me-1"></i>
                                {{ $creditNote->creator->name ?? '-' }}
                            </strong>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="alert alert-info border-0 mb-4">
                    <h6 class="fw-bold mb-2">
                        <i class="bi bi-info-circle-fill me-2"></i>السبب
                    </h6>
                    <p class="mb-0">{{ $creditNote->reason }}</p>
                    @if($creditNote->notes)
                        <hr class="my-2">
                        <h6 class="fw-bold mb-2">ملاحظات إضافية</h6>
                        <p class="mb-0">{{ $creditNote->notes }}</p>
                    @endif
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card bg-light border-0 h-100">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-muted mb-3">
                                    <i class="bi bi-arrow-right-circle me-1"></i>
                                    القيم السابقة
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">المبلغ الأساسي</small>
                                        <strong>{{ number_format($creditNote->previous_values['base_price'] ?? 0, 0) }} ر.س</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">نسبة الضريبة</small>
                                        <strong>{{ $creditNote->previous_values['tax_rate'] ?? 0 }}%</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">مبلغ الضريبة</small>
                                        <strong>{{ number_format($creditNote->previous_values['tax_amount'] ?? 0, 0) }} ر.س</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">الإجمالي</small>
                                        <strong class="text-danger">{{ number_format($creditNote->previous_total, 0) }} ر.س</strong>
                                    </div>
                                    @if(isset($creditNote->previous_values['employees_count']))
                                        <div class="col-6">
                                            <small class="text-muted d-block">عدد الموظفين</small>
                                            <strong>{{ $creditNote->previous_values['employees_count'] }}</strong>
                                        </div>
                                    @endif
                                    @if(isset($creditNote->previous_values['work_days_count']))
                                        <div class="col-6">
                                            <small class="text-muted d-block">أيام العمل</small>
                                            <strong>{{ $creditNote->previous_values['work_days_count'] }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card bg-success bg-opacity-10 border-success border-opacity-25 h-100">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-success mb-3">
                                    <i class="bi bi-arrow-left-circle me-1"></i>
                                    القيم الجديدة
                                </h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted d-block">المبلغ الأساسي</small>
                                        <strong>{{ number_format($creditNote->new_values['base_price'] ?? 0, 0) }} ر.س</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">نسبة الضريبة</small>
                                        <strong>{{ $creditNote->new_values['tax_rate'] ?? 0 }}%</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">مبلغ الضريبة</small>
                                        <strong>{{ number_format($creditNote->new_values['tax_amount'] ?? 0, 0) }} ر.س</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted d-block">الإجمالي</small>
                                        <strong class="text-success">{{ number_format($creditNote->new_total, 0) }} ر.س</strong>
                                    </div>
                                    @if(isset($creditNote->new_values['employees_count']))
                                        <div class="col-6">
                                            <small class="text-muted d-block">عدد الموظفين</small>
                                            <strong>{{ $creditNote->new_values['employees_count'] }}</strong>
                                        </div>
                                    @endif
                                    @if(isset($creditNote->new_values['work_days_count']))
                                        <div class="col-6">
                                            <small class="text-muted d-block">أيام العمل</small>
                                            <strong>{{ $creditNote->new_values['work_days_count'] }}</strong>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light p-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-calculator me-2"></i>
                    ملخص الفروقات
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="alert alert-warning border-0 mb-0">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-arrow-down-up fs-4 me-2"></i>
                        <div>
                            <small class="text-muted d-block">فرق المبلغ الإجمالي</small>
                            <strong class="fs-4 text-warning">{{ number_format($creditNote->amount_difference, 0) }} ر.س</strong>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-1">
                            <span>المبلغ السابق:</span>
                            <strong>{{ number_format($creditNote->previous_total, 0) }} ر.س</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>المبلغ الجديد:</span>
                            <strong>{{ number_format($creditNote->new_total, 0) }} ر.س</strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>الخصم:</span>
                            <strong class="text-danger">-{{ number_format($creditNote->amount_difference, 0) }} ر.س</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light p-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-file-text me-2"></i>
                    معلومات الفاتورة
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-2">
                    <small class="text-muted d-block">رقم الفاتورة</small>
                    <strong>{{ $invoice->number }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">العميل</small>
                    <strong>{{ $invoice->client->name }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">المبلغ الحالي</small>
                    <strong class="text-success">{{ number_format($invoice->total_price, 0) }} ر.س</strong>
                </div>
                <div class="d-grid mt-3">
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-primary">
                        <i class="bi bi-eye me-2"></i>عرض الفاتورة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
