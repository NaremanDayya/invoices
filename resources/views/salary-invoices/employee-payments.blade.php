@extends('layouts.master')

@section('title', 'سجل دفعات الموظف - ' . $employee->employee_name)
@section('page_title', 'سجل دفعات الموظف')
@section('page_subtitle', $employee->employee_name)

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('salary-invoices.employees.index', $invoice) }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-arrow-right me-2"></i>رجوع لقائمة الموظفين
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
                    <i class="bi bi-person-fill me-2"></i>
                    معلومات الموظف
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">اسم الموظف</small>
                            <strong class="fs-5">{{ $employee->employee_name }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">المشروع</small>
                            <strong>{{ $employee->project ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">رقم الحساب (IBAN)</small>
                            <strong dir="ltr">{{ $employee->iban ?? '-' }}</strong>
                        </div>
                    </div>
                    @if($employee->account_holder_name)
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">اسم صاحب الحساب</small>
                            <strong>{{ $employee->account_holder_name }}</strong>
                        </div>
                    </div>
                    @endif
                    @if($employee->bank_name)
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">اسم البنك</small>
                            <strong>{{ $employee->bank_name }}</strong>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">نوع الراتب</small>
                            @if($employee->payment_method === 'wps' || $employee->salary_type === 'wps')
                                <span class="badge bg-purple-600 text-white">نظام حماية الأجور (WPS)</span>
                            @else
                                <span class="badge bg-blue-600 text-white">راتب شهري عادي</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light p-4">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-cash-stack me-2"></i>
                    سجل الدفعات
                </h5>
            </div>
            <div class="card-body p-0">
                @if($employee->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">تاريخ الدفع</th>
                                    <th class="px-4 py-3">نوع الدفع</th>
                                    <th class="px-4 py-3">مبلغ الدفع</th>
                                    <th class="px-4 py-3">طريقة الدفع</th>
                                    <th class="px-4 py-3">تم بواسطة</th>
                                    <th class="px-4 py-3">ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->payments as $payment)
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                                        {{ $payment->payment_date->format('Y-m-d') }}
                                        <br>
                                        <small class="text-muted">{{ $payment->payment_date->format('h:i A') }}</small>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($payment->payment_type === 'full')
                                            <span class="badge bg-success">دفع كامل</span>
                                        @else
                                            <span class="badge bg-warning text-dark">دفع جزئي</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong class="text-success fs-5">{{ number_format($payment->payment_amount, 2) }} ر.س</strong>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($payment->payment_mode === 'wps')
                                            <span class="badge bg-purple-600 text-white">
                                                <i class="bi bi-credit-card me-1"></i>WPS
                                            </span>
                                        @else
                                            <span class="badge bg-blue-600 text-white">
                                                <i class="bi bi-cash me-1"></i>شهري
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-person-fill me-1 text-muted"></i>
                                        {{ $payment->createdBy->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($payment->notes)
                                            <small class="text-muted">{{ $payment->notes }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-end"><strong>إجمالي المدفوع:</strong></td>
                                    <td class="px-4 py-3">
                                        <strong class="text-success fs-5">{{ number_format($employee->payments->sum('payment_amount'), 2) }} ر.س</strong>
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-cash-stack display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد دفعات مسجلة</h5>
                        <p class="text-muted">لم يتم تسجيل أي دفعات لهذا الموظف بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light p-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-calculator me-2"></i>
                    الملخص المالي
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">إجمالي الراتب</small>
                    <strong class="fs-4 text-primary">{{ number_format($employee->total_salary ?? $employee->net_salary, 2) }} ر.س</strong>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">المبلغ المدفوع</small>
                    <strong class="fs-4 text-success">{{ number_format($employee->total_paid ?? 0, 2) }} ر.س</strong>
                    <div class="progress mt-2" style="height: 8px;">
                        @php
                            $totalSalary = $employee->total_salary ?? $employee->net_salary;
                            $percentage = $totalSalary > 0 ? ($employee->total_paid / $totalSalary) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($percentage, 1) }}% مدفوع</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">المبلغ المتبقي</small>
                    <strong class="fs-4 {{ $employee->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($employee->remaining_amount ?? ($employee->total_salary - $employee->total_paid), 2) }} ر.س
                    </strong>
                </div>
                <div class="alert alert-{{ $employee->payment_status === 'paid' ? 'success' : ($employee->payment_status === 'partially_paid' ? 'warning' : 'danger') }} border-0 mb-0">
                    <strong>حالة الدفع:</strong>
                    @if($employee->payment_status === 'paid')
                        <span class="badge bg-success">مدفوع بالكامل</span>
                    @elseif($employee->payment_status === 'partially_paid')
                        <span class="badge bg-warning text-dark">مدفوع جزئياً</span>
                    @else
                        <span class="badge bg-danger">غير مدفوع</span>
                    @endif
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
                    <small class="text-muted d-block">تاريخ الإصدار</small>
                    <strong>{{ $invoice->generation_date->format('Y-m-d') }}</strong>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-primary">
                        <i class="bi bi-eye me-2"></i>عرض الفاتورة
                    </a>
                    <a href="{{ route('salary-invoices.employees.index', $invoice) }}" class="btn btn-outline-primary">
                        <i class="bi bi-people me-2"></i>قائمة الموظفين
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-purple-600 {
        background-color: #7c3aed !important;
    }
    .bg-blue-600 {
        background-color: #2563eb !important;
    }
</style>
@endpush
