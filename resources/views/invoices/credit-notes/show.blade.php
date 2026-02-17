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
    @php
        $previous = $creditNote->previous_values ?? [];
        $new = $creditNote->new_values ?? [];

        // Calculate totals if not present
        $previousBasePrice = $previous['base_price'] ?? $creditNote->previous_base_price;
        $previousTaxRate = $previous['tax_rate'] ?? $invoice->tax_rate;
        $previousTaxAmount = $previous['tax_amount'] ?? (($previousBasePrice * $previousTaxRate) / 100);
        $previousTotal = $previous['total_price'] ?? $creditNote->previous_total;

        $newBasePrice = $new['base_price'] ?? $creditNote->new_base_price;
        $newTaxRate = $new['tax_rate'] ?? $invoice->tax_rate;
        $newTaxAmount = $new['tax_amount'] ?? (($newBasePrice * $newTaxRate) / 100);
        $newTotal = $new['total_price'] ?? $creditNote->new_total;
    @endphp

    <div class="row">
        <div class="col-lg-8">
            <!-- Credit Note Information Card -->
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
                                    {{ \Carbon\Carbon::parse($creditNote->created_at)->locale('ar')->translatedFormat('d F Y - h:i A') }}
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

                    <!-- Detailed Comparison Cards -->
                    <div class="row g-4">
                        <!-- Previous Values Card -->
                        <div class="col-md-6">
                            <div class="card bg-light border-0 h-100">
                                <div class="card-header bg-transparent border-0 pt-3 px-3">
                                    <h6 class="fw-bold text-muted mb-0">
                                        <i class="bi bi-arrow-right-circle me-1"></i>
                                        القيم السابقة
                                    </h6>
                                </div>
                                <div class="card-body p-3 pt-0">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td>عدد الموظفين:</td>
                                            <td class="fw-bold">{{ $previous['employees_count'] ?? $invoice->employees_count }}</td>
                                        </tr>
                                        <tr>
                                            <td>أيام العمل:</td>
                                            <td class="fw-bold">{{ $previous['work_days_count'] ?? $invoice->work_days_count }}</td>
                                        </tr>
                                        <tr>
                                            <td>إجمالي العمال:</td>
                                            <td class="fw-bold">{{ $previous['total_workers'] ?? $invoice->total_workers }}</td>
                                        </tr>
                                        <tr>
                                            <td>إجمالي أيام العمل:</td>
                                            <td class="fw-bold">{{ $previous['work_days'] ?? $invoice->work_days }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="pt-2">المبلغ قبل الضريبة:</td>
                                            <td class="fw-bold text-primary pt-2">{{ number_format($previousBasePrice, 0) }} ر.س</td>
                                        </tr>
                                        <tr>
                                            <td>نسبة الضريبة:</td>
                                            <td class="fw-bold">{{ $previousTaxRate }}%</td>
                                        </tr>
                                        <tr>
                                            <td>مبلغ الضريبة:</td>
                                            <td class="fw-bold text-info">{{ number_format($previousTaxAmount, 0) }} ر.س</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="pt-2">الإجمالي مع الضريبة:</td>
                                            <td class="fw-bold text-danger fs-5 pt-2">{{ number_format($previousTotal, 0) }} ر.س</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- New Values Card -->
                        <div class="col-md-6">
                            <div class="card bg-success bg-opacity-10 border-success border-opacity-25 h-100">
                                <div class="card-header bg-transparent border-0 pt-3 px-3">
                                    <h6 class="fw-bold text-success mb-0">
                                        <i class="bi bi-arrow-left-circle me-1"></i>
                                        القيم الجديدة
                                    </h6>
                                </div>
                                <div class="card-body p-3 pt-0">
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td>عدد الموظفين:</td>
                                            <td class="fw-bold">{{ $new['employees_count'] ?? $invoice->employees_count }}</td>
                                        </tr>
                                        <tr>
                                            <td>أيام العمل:</td>
                                            <td class="fw-bold">{{ $new['work_days_count'] ?? $invoice->work_days_count }}</td>
                                        </tr>
                                        <tr>
                                            <td>إجمالي العمال:</td>
                                            <td class="fw-bold">{{ $new['total_workers'] ?? $invoice->total_workers }}</td>
                                        </tr>
                                        <tr>
                                            <td>إجمالي أيام العمل:</td>
                                            <td class="fw-bold">{{ $new['work_days'] ?? $invoice->work_days }}</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="pt-2">المبلغ قبل الضريبة:</td>
                                            <td class="fw-bold text-primary pt-2">{{ number_format($newBasePrice, 0) }} ر.س</td>
                                        </tr>
                                        <tr>
                                            <td>نسبة الضريبة:</td>
                                            <td class="fw-bold">{{ $newTaxRate }}%</td>
                                        </tr>
                                        <tr>
                                            <td>مبلغ الضريبة:</td>
                                            <td class="fw-bold text-info">{{ number_format($newTaxAmount, 0) }} ر.س</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="pt-2">الإجمالي مع الضريبة:</td>
                                            <td class="fw-bold text-success fs-5 pt-2">{{ number_format($newTotal, 0) }} ر.س</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Summary Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light p-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-calculator me-2"></i>
                        ملخص الفروقات
                    </h6>
                </div>
                <div class="card-body p-3">
                    <div class="alert alert-warning border-0 mb-0">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-arrow-down-up fs-4 me-2"></i>
                            <div>
                                <small class="text-muted d-block">فرق المبلغ قبل الضريبة</small>
                                <strong class="fs-5 text-warning">{{ number_format($previousBasePrice - $newBasePrice, 0) }} ر.س</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <i class="bi bi-percent fs-4 me-2"></i>
                            <div>
                                <small class="text-muted d-block">فرق مبلغ الضريبة</small>
                                <strong class="fs-5 text-info">{{ number_format($previousTaxAmount - $newTaxAmount, 0) }} ر.س</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash-stack fs-4 me-2"></i>
                            <div>
                                <small class="text-muted d-block">فرق المبلغ الإجمالي</small>
                                <strong class="fs-4 text-warning">{{ number_format($creditNote->amount_difference, 0) }} ر.س</strong>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span>المبلغ السابق:</span>
                                <strong>{{ number_format($previousTotal, 0) }} ر.س</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span>المبلغ الجديد:</span>
                                <strong>{{ number_format($newTotal, 0) }} ر.س</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>الخصم:</span>
                                <strong class="text-danger">-{{ number_format($creditNote->amount_difference, 0) }} ر.س</strong>
                            </div>
                        </div>

                        @if(isset($previous['employees_count']) && isset($new['employees_count']) && $previous['employees_count'] != $new['employees_count'])
                            <hr class="my-2">
                            <div class="small">
                                <div class="d-flex justify-content-between text-danger">
                                    <span>فرق عدد الموظفين:</span>
                                    <strong>{{ $previous['employees_count'] - $new['employees_count'] }}</strong>
                                </div>
                            </div>
                        @endif

                        @if(isset($previous['work_days_count']) && isset($new['work_days_count']) && $previous['work_days_count'] != $new['work_days_count'])
                            <div class="small">
                                <div class="d-flex justify-content-between text-danger">
                                    <span>فرق أيام العمل:</span>
                                    <strong>{{ $previous['work_days_count'] - $new['work_days_count'] }}</strong>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Information Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light p-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-file-text me-2"></i>
                        معلومات الفاتورة الحالية
                    </h6>
                </div>
                <div class="card-body p-3">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td>رقم الفاتورة:</td>
                            <td class="fw-bold text-primary">{{ $invoice->number }}</td>
                        </tr>
                        <tr>
                            <td>العميل:</td>
                            <td class="fw-bold">{{ $invoice->client->name }}</td>
                        </tr>
                        <tr>
                            <td>عدد الموظفين:</td>
                            <td class="fw-bold">{{ $invoice->employees_count }}</td>
                        </tr>
                        <tr>
                            <td>أيام العمل:</td>
                            <td class="fw-bold">{{ $invoice->work_days_count }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="pt-2">المبلغ قبل الضريبة:</td>
                            <td class="fw-bold text-primary pt-2">{{ number_format($invoice->base_price, 0) }} ر.س</td>
                        </tr>
                        <tr>
                            <td>الضريبة:</td>
                            <td class="fw-bold text-info">{{ number_format($invoice->tax_amount, 0) }} ر.س ({{ $invoice->tax_rate }}%)</td>
                        </tr>
                        <tr class="border-top">
                            <td class="pt-2"><strong>الإجمالي:</strong></td>
                            <td class="fw-bold text-success fs-6 pt-2"><strong>{{ number_format($invoice->total_price, 0) }} ر.س</strong></td>
                        </tr>
                    </table>

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
