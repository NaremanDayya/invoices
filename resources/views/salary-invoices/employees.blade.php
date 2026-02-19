@extends('layouts.master')

@section('title', 'موظفي فاتورة الرواتب #' . $invoice->number)

@section('content')
<style>
.emp-hero{background:linear-gradient(135deg,#1e4a46 0%,#2d6a65 60%,#326462 100%);border-radius:16px;padding:24px 28px 20px;margin-bottom:20px;position:relative;overflow:hidden;}
.emp-hero::before{content:'';position:absolute;top:-50px;left:-50px;width:200px;height:200px;background:rgba(255,255,255,.04);border-radius:50%;}
.emp-hero::after{content:'';position:absolute;bottom:-60px;right:-30px;width:220px;height:220px;background:rgba(251,189,8,.05);border-radius:50%;}
.hero-title{color:#fff;font-size:1.4rem;font-weight:700;margin-bottom:3px;}
.hero-sub{color:rgba(255,255,255,.65);font-size:.82rem;}
.hero-sub a{color:rgba(255,255,255,.55);text-decoration:none;}
.hero-sub a:hover{color:#fbbd08;}
.hero-meta{display:flex;gap:20px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid rgba(255,255,255,.12);}
.hm-label{font-size:10px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.4px;}
.hm-value{font-size:13px;font-weight:700;color:#fff;margin-top:1px;}
.sbadge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;}
.stat-card{border:none!important;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);transition:transform .15s,box-shadow .15s;}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.1);}
.stat-icon{width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.stat-label{font-size:11px;color:#6c757d;margin-bottom:2px;}
.stat-value{font-size:22px;font-weight:800;line-height:1.1;}
.fin-bar{background:linear-gradient(135deg,#1e4a46,#326462);border-radius:12px;padding:14px 24px;display:flex;justify-content:space-around;align-items:center;margin-bottom:18px;}
.fin-item{text-align:center;}
.fin-label{font-size:10px;color:rgba(255,255,255,.6);}
.fin-value{font-size:17px;font-weight:800;margin-top:2px;}
.fin-sep{width:1px;height:36px;background:rgba(255,255,255,.15);}
.toolbar-card{border:none!important;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);margin-bottom:18px;}
.filter-tabs .btn{border-radius:20px!important;font-size:12px;padding:4px 13px;}
.emp-table thead th{background:linear-gradient(135deg,#1e4a46,#326462);color:#fff;font-size:11px;font-weight:600;padding:10px 9px;white-space:nowrap;border:none;}
.emp-table thead th:first-child{border-radius:0 8px 0 0;}
.emp-table thead th:last-child{border-radius:8px 0 0 0;}
.emp-table tbody tr:hover{background:#f0fdf4!important;}
.emp-table td{font-size:12.5px;padding:8px 9px;vertical-align:middle;border-bottom:1px solid #f1f5f9;}
</style>

<div class="container-fluid px-4 py-3">

    {{-- HERO HEADER --}}
    <div class="emp-hero">
        <div class="d-flex justify-content-between align-items-start gap-3">
            <div style="flex:1;min-width:0;">
                <div class="hero-title">
                    <i class="bi bi-people-fill me-2" style="color:#fbbd08;"></i>كشف رواتب الموظفين &mdash; فاتورة #{{ $invoice->number }}
                </div>
                <div class="hero-sub">
                    <a href="{{ route('invoices.index') }}">الفواتير</a>
                    <span style="margin:0 5px;opacity:.4;">/</span>
                    <a href="{{ route('invoices.show', $invoice->id) }}">فاتورة #{{ $invoice->number }}</a>
                    <span style="margin:0 5px;opacity:.4;">/</span>
                    <span>الموظفين</span>
                </div>
                <div class="hero-meta">
                    <div><div class="hm-label">العميل</div><div class="hm-value">{{ $invoice->client->name ?? '-' }}</div></div>
                    <div><div class="hm-label">تاريخ الفاتورة</div><div class="hm-value">{{ $invoice->generation_date }}</div></div>
                    <div>
                        <div class="hm-label">حالة الاعتماد</div>
                        <div class="hm-value">
                            @if($invoice->approval_status === 'approved')
                                <span class="sbadge" style="background:rgba(25,135,84,.25);color:#6ee7b7;"><i class="bi bi-check-circle-fill"></i>معتمدة</span>
                            @elseif($invoice->approval_status === 'rejected')
                                <span class="sbadge" style="background:rgba(220,53,69,.25);color:#fca5a5;"><i class="bi bi-x-circle-fill"></i>مرفوضة</span>
                            @else
                                <span class="sbadge" style="background:rgba(255,193,7,.2);color:#fde68a;"><i class="bi bi-clock-fill"></i>قيد الانتظار</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="hm-label">حالة المراجعة</div>
                        <div class="hm-value">
                            @if($invoice->revision_status === 'revision_approved')
                                <span class="sbadge" style="background:rgba(25,135,84,.25);color:#6ee7b7;"><i class="bi bi-patch-check-fill"></i>معتمدة</span>
                            @elseif($invoice->revision_status === 'revision_rejected')
                                <span class="sbadge" style="background:rgba(220,53,69,.25);color:#fca5a5;"><i class="bi bi-patch-exclamation-fill"></i>مرفوضة</span>
                            @else
                                <span class="sbadge" style="background:rgba(13,202,240,.15);color:#a5f3fc;"><i class="bi bi-hourglass-split"></i>قيد المراجعة</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap justify-content-end" style="flex-shrink:0;">
                <button type="button" class="btn btn-light btn-sm" onclick="exportSalaryPDF()">
                    <i class="bi bi-file-earmark-pdf me-1" style="color:#dc3545;"></i>PDF
                </button>
{{--                @permission('preview_invoice_employees')--}}
                @if($invoice->revision_status == 'pending')
                    <button type="button" class="btn btn-warning btn-sm" onclick="openRevisionModal()">
                        <i class="bi bi-pencil-square me-1"></i>مراجعة
                    </button>
                @endif
{{--                @endpermission--}}
{{--                @can('approve_invoice_employees')--}}
                @if($invoice->approval_status !== 'approved')
                    <button type="button" class="btn btn-success btn-sm" onclick="approveInvoice()">
                        <i class="bi bi-check-circle me-1"></i>اعتماد
                    </button>
                @endif
{{--                @endcan--}}
                @can('review_invoice')
                    @if($invoice->revision_status === 'pending')
                        <button type="button" class="btn btn-info btn-sm" onclick="openRevisionModal()">
                            <i class="bi bi-pencil-square me-1"></i>إتمام المراجعة
                        </button>
                    @endif
                @endcan
                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-light btn-sm">
                    <i class="bi bi-arrow-right me-1"></i>رجوع
                </a>
            </div>
        </div>
    </div>

    {{-- Revision notes alert --}}
    @if($invoice->revision_notes && $invoice->revision_status !== 'pending')
        <div class="alert alert-{{ $invoice->revision_status === 'revision_approved' ? 'success' : 'danger' }} d-flex align-items-center gap-2 mb-4 rounded-3">
            <i class="bi bi-{{ $invoice->revision_status === 'revision_approved' ? 'check-circle-fill' : 'exclamation-triangle-fill' }} fs-5"></i>
            <div><strong>ملاحظات المراجعة:</strong> {{ $invoice->revision_notes }}</div>
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md">
            <div class="stat-card card h-100"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-label">إجمالي الموظفين</div><div class="stat-value text-primary">{{ $summary['total_employees'] }}</div></div>
                    <div class="stat-icon" style="background:#eff6ff;"><i class="bi bi-people-fill text-primary"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card h-100"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-label">مدفوع بالكامل</div><div class="stat-value text-success">{{ $summary['paid_employees'] }}</div></div>
                    <div class="stat-icon" style="background:#f0fdf4;"><i class="bi bi-check-circle-fill text-success"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card h-100"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-label">مدفوع جزئياً</div><div class="stat-value text-warning">{{ $summary['partially_paid_employees'] }}</div></div>
                    <div class="stat-icon" style="background:#fffbeb;"><i class="bi bi-hourglass-split text-warning"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card h-100"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-label">غير مدفوع</div><div class="stat-value text-danger">{{ $summary['unpaid_employees'] }}</div></div>
                    <div class="stat-icon" style="background:#fef2f2;"><i class="bi bi-x-circle-fill text-danger"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card h-100"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-label">WPS</div><div class="stat-value text-info">{{ $summary['wps_employees'] }}</div></div>
                    <div class="stat-icon" style="background:#ecfeff;"><i class="bi bi-bank text-info"></i></div>
                </div>
            </div></div>
        </div>
        <div class="col-6 col-md">
            <div class="stat-card card h-100"><div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="stat-label">شهري</div><div class="stat-value" style="color:#475569;">{{ $summary['monthly_employees'] }}</div></div>
                    <div class="stat-icon" style="background:#f8fafc;"><i class="bi bi-calendar-month" style="color:#475569;"></i></div>
                </div>
            </div></div>
        </div>
    </div>

    {{-- FINANCIAL BAR --}}
    <div class="fin-bar">
        <div class="fin-item">
            <div class="fin-label">إجمالي الرواتب</div>
            <div class="fin-value" style="color:#fbbd08;">{{ number_format($summary['total_salaries'], 0) }} ر.س</div>
        </div>
        <div class="fin-sep"></div>
        <div class="fin-item">
            <div class="fin-label">المبلغ المدفوع</div>
            <div class="fin-value" style="color:#6ee7b7;">{{ number_format($summary['total_paid'], 0) }} ر.س</div>
        </div>
        <div class="fin-sep"></div>
        <div class="fin-item">
            <div class="fin-label">المبلغ المتبقي</div>
            <div class="fin-value" style="color:#fca5a5;">{{ number_format($summary['total_remaining'], 0) }} ر.س</div>
        </div>
    </div>

    {{-- TOOLBAR: search + invoice select + filters + batch --}}
    <div class="toolbar-card card">
        <div class="card-body">
            <form method="GET" action="{{ route('salary-invoices.employees.index', $invoice->id) }}" class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-4">
                        <select class="form-select form-select-sm" name="invoice_id" onchange="if(this.value) window.location.href='/salary-invoices/'+this.value+'/employees'">
                            @foreach($allSalaryInvoices as $inv)
                                <option value="{{ $inv->id }}" {{ $inv->id == $invoice->id ? 'selected' : '' }}>
                                    فاتورة #{{ $inv->number }} — {{ $inv->generation_date }} — {{ $inv->client->name ?? 'بدون عميل' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control" name="search" value="{{ $search }}" placeholder="ابحث بالاسم، المشروع، أو ID">
                            <button type="submit" class="btn btn-primary">بحث</button>
                            <a href="{{ route('salary-invoices.employees.index', $invoice->id) }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        @if($invoice->approval_status === 'approved')
                            <button type="button" class="btn btn-success btn-sm" id="processBatchBtn"
                                    data-bs-toggle="modal" data-bs-target="#batchPaymentModal" disabled>
                                <i class="bi bi-cash-coin me-1"></i>معالجة الدفعات (<span id="selectedCount">0</span>)
                            </button>
                        @endif
                    </div>
                </div>
            </form>
            <div class="filter-tabs d-flex flex-wrap gap-2">
                <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'filter'=>'all','search'=>$search]) }}"
                   class="btn btn-sm {{ $filter==='all' ? 'btn-primary' : 'btn-outline-primary' }}">الكل ({{ $summary['total_employees'] }})</a>
                <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'filter'=>'unpaid','search'=>$search]) }}"
                   class="btn btn-sm {{ $filter==='unpaid' ? 'btn-danger' : 'btn-outline-danger' }}">غير مدفوع ({{ $summary['unpaid_employees'] }})</a>
                <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'filter'=>'partially_paid','search'=>$search]) }}"
                   class="btn btn-sm {{ $filter==='partially_paid' ? 'btn-warning' : 'btn-outline-warning' }}">مدفوع جزئياً ({{ $summary['partially_paid_employees'] }})</a>
                <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'filter'=>'paid','search'=>$search]) }}"
                   class="btn btn-sm {{ $filter==='paid' ? 'btn-success' : 'btn-outline-success' }}">مدفوع ({{ $summary['paid_employees'] }})</a>
                <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'filter'=>'wps','search'=>$search]) }}"
                   class="btn btn-sm {{ $filter==='wps' ? 'btn-info' : 'btn-outline-info' }}">WPS ({{ $summary['wps_employees'] }})</a>
                <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'filter'=>'monthly','search'=>$search]) }}"
                   class="btn btn-sm {{ $filter==='monthly' ? 'btn-secondary' : 'btn-outline-secondary' }}">شهري ({{ $summary['monthly_employees'] }})</a>
            </div>
        </div>
    </div>

    {{-- EMPLOYEES TABLE --}}
    <div class="card" style="border:none;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.06);">
        <div class="card-body p-0">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle emp-table mb-0">
                        <thead>
                            <tr>
                                @if($invoice->approval_status === 'approved')
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAll">
                                    </th>
                                @endif
                                <th>ID</th>
                                <th>اسم الموظف</th>
                                <th>المشروع</th>
                                <th>أيام العمل</th>
                                <th>الراتب الإجمالي</th>
                                <th>المكافآت</th>
                                <th>السلف</th>
                                <th>خصومات الشهر</th>
                                <th>خصومات أخرى</th>
                                <th>صافي الراتب</th>
                                <th>المدفوع</th>
                                <th>المتبقي</th>
                                <th>نوع الراتب</th>
                                <th>حالة الدفع</th>
                                <th>آخر دفعة</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    @if($invoice->approval_status === 'approved')
                                        <td>
                                            <input type="checkbox"
                                                   class="form-check-input employee-checkbox"
                                                   value="{{ $employee->id }}"
                                                   data-employee-name="{{ $employee->employee_name }}"
                                                   data-total-salary="{{ $employee->total_salary ?? $employee->net_salary }}"
                                                   data-total-paid="{{ $employee->total_paid ?? 0 }}"
                                                   data-remaining="{{ $employee->remaining_amount ?? $employee->net_salary }}"
                                                   data-salary-type="{{ $employee->salary_type ?? 'monthly' }}"
                                                   data-wps-paid="{{ $employee->wps_paid ?? 0 }}"
                                                   {{ $employee->payment_status === 'paid' ? 'disabled' : '' }}>
                                        </td>
                                    @endif
                                    <td>{{ $employee->id }}</td>
                                    <td class="fw-bold">{{ $employee->employee_name }}</td>
                                    <td>{{ $employee->project ?? '-' }}</td>
                                    <td class="text-center">{{ $employee->work_days_count ?? $employee->work_days ?? '-' }}</td>
                                    <td class="text-primary fw-bold">{{ number_format($employee->basic_salary ?? 0, 2) }} ر.س</td>
                                    <td class="text-success fw-bold">{{ number_format($employee->bonuses ?? 0, 2) }} ر.س</td>
                                    <td class="text-danger fw-bold">{{ number_format($employee->advance_deductions ?? 0, 2) }} ر.س</td>
                                    <td class="text-warning fw-bold">{{ number_format($employee->monthly_deductions ?? 0, 2) }} ر.س</td>
                                    <td class="text-danger fw-bold">{{ number_format(($employee->deductions ?? 0), 2) }} ر.س</td>
                                    <td class="text-success fw-bold fs-6">{{ number_format($employee->net_salary ?? $employee->total_salary ?? 0, 2) }} ر.س</td>
                                    <td class="text-info fw-bold">{{ number_format($employee->total_paid ?? 0, 2) }} ر.س</td>
                                    <td class="text-danger fw-bold">{{ number_format($employee->remaining_amount ?? $employee->net_salary, 2) }} ر.س</td>
                                    <td>
                                        @if(($employee->salary_type ?? 'monthly') === 'wps')
                                            <span class="badge bg-info">WPS</span>
                                        @else
                                            <span class="badge bg-secondary">شهري</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($employee->payment_status === 'paid')
                                            <span class="badge bg-success">مدفوع</span>
                                        @elseif($employee->payment_status === 'partially_paid')
                                            <span class="badge bg-warning">مدفوع جزئياً</span>
                                        @else
                                            <span class="badge bg-danger">غير مدفوع</span>
                                        @endif
                                    </td>
                                    <td>{{ $employee->last_payment_date ? \Carbon\Carbon::parse($employee->last_payment_date)->format('Y-m-d') : '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            @if($invoice->approval_status === 'approved' && $employee->payment_status !== 'paid')
                                                <button type="button"
                                                        class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#paymentModal"
                                                        data-employee-id="{{ $employee->id }}"
                                                        data-employee-name="{{ $employee->employee_name }}"
                                                        data-total-salary="{{ $employee->total_salary ?? $employee->net_salary }}"
                                                        data-total-paid="{{ $employee->total_paid ?? 0 }}"
                                                        data-remaining="{{ $employee->remaining_amount ?? $employee->net_salary }}"
                                                        data-salary-type="{{ $employee->salary_type ?? 'monthly' }}"
                                                        title="معالجة الدفع">
                                                    <i class="bi bi-cash-coin"></i>
                                                </button>
                                            @endif
                                            <a href="{{ route('salary-invoices.employees.payments', [$invoice->id, $employee->id]) }}"
                                               class="btn btn-sm btn-info"
                                               title="سجل الدفعات">
                                                <i class="bi bi-clock-history"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-muted mt-3">لا يوجد موظفين {{ $filter !== 'all' ? 'في هذا التصنيف' : '' }}</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Batch Payment Modal (Bootstrap 5) -->
<div class="modal fade" id="batchPaymentModal" tabindex="-1" aria-labelledby="batchPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="batchPaymentModalLabel">
                    <i class="bi bi-cash-stack me-2"></i>معالجة دفعات متعددة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>تم تحديد <span id="batchSelectedCount">0</span> موظف</strong>
                </div>

                <div id="batchEmployeesList" class="mb-3">
                    <!-- Employees will be dynamically added here -->
                </div>

                <div class="alert alert-warning" id="batchWpsWarning" style="display: none;">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>تحذير WPS:</strong> الحد الأقصى المسموح به هو <span id="batchWpsMaxPercentage">70</span>% من إجمالي الراتب
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>إلغاء
                </button>
                <button type="button" class="btn btn-success" id="submitBatchPaymentBtn">
                    <i class="bi bi-check-circle me-2"></i>تأكيد معالجة الدفعات
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="paymentModalLabel">
                    <i class="bi bi-cash-coin me-2"></i>معالجة دفع الموظف
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="paymentForm">
                <input type="hidden" id="employeeId" name="employee_id">

                <div class="modal-body">
                    <!-- Employee Card -->
                    <div id="singleEmployeeCard"></div>

                    <!-- WPS Warning -->
                    <div class="alert alert-warning mt-3" id="wpsWarning" style="display: none;">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>تحذير WPS:</strong> الحد الأقصى المسموح به هو <span id="wpsMaxPercentage">70</span>% من إجمالي الراتب
                    </div>

                    <!-- Notes -->
                    <div class="mb-3 mt-3">
                        <label class="form-label fw-bold">ملاحظات (اختياري)</label>
                        <textarea class="form-control" id="paymentNotes" name="notes" rows="3" placeholder="أضف ملاحظات إن وجدت"></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-success" id="submitPaymentBtn">
                        <i class="bi bi-check-circle me-2"></i>تأكيد الدفع
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Revision Modal -->
<div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="revisionModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>إتمام المراجعة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="revisionForm">
                <div class="modal-body">
                    <div class="mb-4">
                        <label class="form-label fw-bold">نتيجة المراجعة</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="revision_status" id="revisionApproved" value="revision_approved" checked>
                                <label class="form-check-label" for="revisionApproved">
                                    <span class="badge bg-success">موافقة</span>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="revision_status" id="revisionRejected" value="revision_rejected">
                                <label class="form-check-label" for="revisionRejected">
                                    <span class="badge bg-danger">رفض</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="revisionNotes" class="form-label fw-bold">ملاحظات المراجعة</label>
                        <textarea class="form-control" id="revisionNotes" name="revision_notes" rows="4" placeholder="أضف ملاحظاتك حول المراجعة..."></textarea>
                        <small class="text-muted">هذه الملاحظات ستكون ظاهرة في الفاتورة</small>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-2"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitRevisionBtn">
                        <i class="bi bi-check-circle me-2"></i>تأكيد المراجعة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let wpsMaxPercentage = 70;
let currentEmployeeData = {};

// Load WPS settings
async function loadWpsSettings() {
    try {
        const response = await fetch('/salary-invoices/wps-settings');
        const data = await response.json();
        if (data.success) {
            wpsMaxPercentage = data.wps_max_percentage;
            document.getElementById('wpsMaxPercentage').textContent = wpsMaxPercentage;
        }
    } catch (error) {
        console.error('Error loading WPS settings:', error);
    }
}

// Initialize modal when shown
document.getElementById('paymentModal').addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    currentEmployeeData = {
        id: button.getAttribute('data-employee-id'),
        name: button.getAttribute('data-employee-name'),
        totalSalary: parseFloat(button.getAttribute('data-total-salary')),
        totalPaid: parseFloat(button.getAttribute('data-total-paid')),
        remaining: parseFloat(button.getAttribute('data-remaining')),
        salaryType: button.getAttribute('data-salary-type')
    };

    // Populate modal
    document.getElementById('employeeId').value = currentEmployeeData.id;
    document.getElementById('wpsMaxPercentage').textContent = wpsMaxPercentage;

    // Reset form
    document.getElementById('paymentForm').reset();
    document.getElementById('employeeId').value = currentEmployeeData.id;
    document.getElementById('wpsWarning').style.display = 'none';

    renderSingleEmployeeCard();
});
// Open Revision Modal
function openRevisionModal() {
    const modal = new bootstrap.Modal(document.getElementById('revisionModal'));
    modal.show();
}

// Handle Revision Form Submission
document.getElementById('revisionForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();

    const revisionStatus = document.querySelector('input[name="revision_status"]:checked').value;
    const revisionNotes = document.getElementById('revisionNotes').value;

    // Validate notes for rejection
    if (revisionStatus === 'revision_rejected' && !revisionNotes.trim()) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ',
            text: 'يرجى إدخال ملاحظات المراجعة عند الرفض',
            confirmButtonText: 'حسناً'
        });
        return;
    }

    const submitBtn = document.getElementById('submitRevisionBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';

    try {
        const response = await fetch('{{ route("salary-invoices.revision", $invoice->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                revision_status: revisionStatus,
                revision_notes: revisionNotes
            })
        });

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            // Handle HTML response (error page)
            const html = await response.text();
            console.error('Received HTML instead of JSON:', html.substring(0, 200));
            throw new Error('استجابة غير متوقعة من الخادم. قد تكون الجلسة منتهية أو حدث خطأ في الخادم.');
        }

        const data = await response.json();

        if (response.ok && data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('revisionModal'));
            if (modal) modal.hide();

            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message,
                confirmButtonText: 'حسناً'
            }).then(() => {
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    location.reload();
                }
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'فشل في إتمام المراجعة',
                text: data.message || 'حدث خطأ أثناء المراجعة',
                confirmButtonText: 'حسناً'
            });
        }
    } catch (error) {
        console.error('Revision Error:', error);

        Swal.fire({
            icon: 'error',
            title: 'خطأ في الاتصال',
            html: `
                <p>${error.message || 'حدث خطأ أثناء المراجعة'}</p>
                <div class="text-start small mt-3 p-3 bg-light rounded">
                    <strong>تأكد من:</strong><br>
                    • أنك مسجل الدخول<br>
                    • أن لديك صلاحية المراجعة<br>
                    • أن الفاتورة لا تزال في حالة انتظار المراجعة
                </div>
            `,
            confirmButtonText: 'حسناً'
        });
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
// Render single employee card with payment options
function renderSingleEmployeeCard() {
    const container = document.getElementById('singleEmployeeCard');
    const emp = currentEmployeeData;
    const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
    const maxWpsForRemaining = Math.min(maxWpsAmount, emp.remaining);

    container.innerHTML = `
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="mb-1 fw-bold">${emp.name}</h6>
                        <small class="text-muted">
                            <span class="badge bg-${emp.salaryType === 'wps' ? 'info' : 'secondary'}">${emp.salaryType === 'wps' ? 'WPS' : 'شهري'}</span>
                        </small>
                        <div class="mt-2 small">
                            <div><strong>الإجمالي:</strong> ${formatNumber(emp.totalSalary)} ريال</div>
                            <div class="text-success"><strong>المدفوع:</strong> ${formatNumber(emp.totalPaid)} ريال</div>
                            <div class="text-danger"><strong>المتبقي:</strong> ${formatNumber(emp.remaining)} ريال</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">نوع الدفع</label>
                        <select class="form-select" id="singlePaymentType">
                            <option value="full">دفع كامل (${formatNumber(emp.remaining)} ريال)</option>
                            <option value="partial">دفع جزئي</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">وضع الدفع</label>
                        <select class="form-select" id="singlePaymentMode">
                            <option value="monthly">شهري</option>
                            <option value="wps">WPS</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="singleAmountSection" style="display: none;">
                        <label class="form-label small fw-bold">المبلغ</label>
                        <input type="number"
                               class="form-control"
                               id="singlePaymentAmount"
                               step="0.01"
                               min="0.01"
                               max="${emp.remaining}"
                               placeholder="أدخل المبلغ">
                        <small class="text-muted">الحد الأقصى: ${formatNumber(emp.remaining)}</small>
                        <div id="singleWpsLimit" style="display: none; margin-top: 4px;">
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                WPS: ${formatNumber(maxWpsForRemaining)} ريال
                            </small>
                        </div>
                        <div class="invalid-feedback" id="singleAmountError"></div>
                    </div>
                </div>
            </div>
        </div>
    `;

    attachSingleEmployeeEventListeners();
}

// Attach event listeners to single employee payment controls
function attachSingleEmployeeEventListeners() {
    // Payment type change
    document.getElementById('singlePaymentType').addEventListener('change', function() {
        const amountSection = document.getElementById('singleAmountSection');
        if (this.value === 'partial') {
            amountSection.style.display = 'block';
            document.getElementById('singlePaymentAmount').required = true;
        } else {
            amountSection.style.display = 'none';
            document.getElementById('singlePaymentAmount').required = false;
        }
    });

    // Payment mode change
    document.getElementById('singlePaymentMode').addEventListener('change', function() {
        const wpsLimit = document.getElementById('singleWpsLimit');
        const wpsWarning = document.getElementById('wpsWarning');

        if (this.value === 'wps') {
            wpsLimit.style.display = 'block';
            wpsWarning.style.display = 'block';
        } else {
            wpsLimit.style.display = 'none';
            wpsWarning.style.display = 'none';
        }
    });

    // Amount validation
    document.getElementById('singlePaymentAmount').addEventListener('input', function() {
        const amount = parseFloat(this.value);
        const paymentMode = document.getElementById('singlePaymentMode').value;
        const errorDiv = document.getElementById('singleAmountError');

        if (isNaN(amount) || amount <= 0) {
            this.classList.add('is-invalid');
            errorDiv.textContent = 'المبلغ يجب أن يكون أكبر من صفر';
            return;
        }

        if (amount > currentEmployeeData.remaining) {
            this.classList.add('is-invalid');
            errorDiv.textContent = 'المبلغ يتجاوز المبلغ المتبقي';
            return;
        }

        if (paymentMode === 'wps') {
            const maxWpsAmount = (currentEmployeeData.totalSalary * wpsMaxPercentage) / 100;
            const actualMax = Math.min(maxWpsAmount, currentEmployeeData.remaining);

            if (amount > actualMax) {
                this.classList.add('is-invalid');
                errorDiv.textContent = `المبلغ يتجاوز الحد الأقصى لـ WPS (${formatNumber(actualMax)} ريال)`;
                return;
            }
        }

        this.classList.remove('is-invalid');
        errorDiv.textContent = '';
    });
}

// Form submission
document.getElementById('paymentForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const paymentType = document.getElementById('singlePaymentType').value;
    const paymentMode = document.getElementById('singlePaymentMode').value;
    const notes = document.getElementById('paymentNotes').value;

    let amount = currentEmployeeData.remaining;
    if (paymentType === 'partial') {
        amount = parseFloat(document.getElementById('singlePaymentAmount').value);

        if (isNaN(amount) || amount <= 0 || amount > currentEmployeeData.remaining) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'المبلغ المدخل غير صحيح',
                confirmButtonText: 'حسناً'
            });
            return;
        }

        if (paymentMode === 'wps') {
            const maxWpsAmount = (currentEmployeeData.totalSalary * wpsMaxPercentage) / 100;
            const actualMax = Math.min(maxWpsAmount, currentEmployeeData.remaining);

            if (amount > actualMax) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: `المبلغ يتجاوز الحد الأقصى لـ WPS (${formatNumber(actualMax)} ريال)`,
                    confirmButtonText: 'حسناً'
                });
                return;
            }
        }
    }

    const submitBtn = document.getElementById('submitPaymentBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';

    try {
        const response = await fetch('{{ route("salary-payments.process", $invoice->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                payments: [{
                    employee_id: currentEmployeeData.id,
                    payment_type: paymentType,
                    payment_mode: paymentMode,
                    amount: amount,
                    notes: notes
                }]
            })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                text: data.message,
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } else {
            // Show detailed error message
            let errorMessage = data.message || 'حدث خطأ أثناء معالجة الدفع';

            // If there are validation errors, show them
            if (data.errors) {
                errorMessage += '\n\n';
                if (Array.isArray(data.errors)) {
                    // Payment service errors: [{employee_id, error}]
                    data.errors.forEach(err => {
                        errorMessage += (err.error || JSON.stringify(err)) + '\n';
                    });
                } else {
                    // Laravel validation errors: {field: [messages]}
                    Object.values(data.errors).forEach(msgs => {
                        errorMessage += (Array.isArray(msgs) ? msgs.join('\n') : msgs) + '\n';
                    });
                }
            }

            console.error('Payment Error:', data);

            Swal.fire({
                icon: 'error',
                title: 'فشل في معالجة الدفع',
                html: errorMessage.replace(/\n/g, '<br>'),
                confirmButtonText: 'حسناً',
                width: '600px'
            });
        }
    } catch (error) {
        console.error('Payment Exception:', error);

        Swal.fire({
            icon: 'error',
            title: 'خطأ في الاتصال',
            html: `
                <p>حدث خطأ أثناء معالجة الدفع</p>
                <div class="text-start small mt-3 p-3 bg-light rounded">
                    <strong>تفاصيل الخطأ:</strong><br>
                    ${error.message || 'خطأ غير معروف'}
                </div>
            `,
            confirmButtonText: 'حسناً',
            width: '600px'
        });
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>تأكيد الدفع';
    }
});

// Select all checkbox
document.getElementById('selectAll')?.addEventListener('change', function() {
    document.querySelectorAll('.employee-checkbox:not(:disabled)').forEach(cb => {
        cb.checked = this.checked;
    });
    updateSelectedCount();
});

// Update selected count
document.querySelectorAll('.employee-checkbox').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const count = document.querySelectorAll('.employee-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = count;
    const batchBtn = document.getElementById('processBatchBtn');
    if (batchBtn) {
        batchBtn.disabled = count === 0;
    }
}

// Batch Payment Modal - Initialize when shown
let batchEmployeesData = [];

document.getElementById('batchPaymentModal')?.addEventListener('show.bs.modal', function(event) {
    const checkboxes = document.querySelectorAll('.employee-checkbox:checked');
    batchEmployeesData = [];

    checkboxes.forEach(checkbox => {
        batchEmployeesData.push({
            id: checkbox.value,
            name: checkbox.dataset.employeeName,
            totalSalary: parseFloat(checkbox.dataset.totalSalary),
            totalPaid: parseFloat(checkbox.dataset.totalPaid),
            remaining: parseFloat(checkbox.dataset.remaining),
            salaryType: checkbox.dataset.salaryType,
            wpsPaid: parseFloat(checkbox.dataset.wpsPaid || 0)
        });
    });

    document.getElementById('batchSelectedCount').textContent = batchEmployeesData.length;
    document.getElementById('batchWpsMaxPercentage').textContent = wpsMaxPercentage;

    renderBatchEmployees();
});

// Render batch employees list with payment options
function renderBatchEmployees() {
    const container = document.getElementById('batchEmployeesList');
    container.innerHTML = '';

    batchEmployeesData.forEach((emp, index) => {
        const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
        const remainingWpsAllowance = maxWpsAmount - emp.wpsPaid;
        const maxWpsForRemaining = Math.min(remainingWpsAllowance, emp.remaining);

        const card = document.createElement('div');
        card.className = 'card mb-3';
        card.innerHTML = `
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="mb-1 fw-bold">${emp.name}</h6>
                        <small class="text-muted">
                            <span class="badge bg-${emp.salaryType === 'wps' ? 'info' : 'secondary'}">${emp.salaryType === 'wps' ? 'WPS' : 'شهري'}</span>
                        </small>
                        <div class="mt-2 small">
                            <div><strong>الإجمالي:</strong> ${formatNumber(emp.totalSalary)} ريال</div>
                            <div class="text-danger"><strong>المتبقي:</strong> ${formatNumber(emp.remaining)} ريال</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">نوع الدفع</label>
                        <select class="form-select form-select-sm batch-payment-type" data-index="${index}">
                            <option value="full">دفع كامل (${formatNumber(emp.remaining)} ريال)</option>
                            <option value="partial">دفع جزئي</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-bold">وضع الدفع</label>
                        <select class="form-select form-select-sm batch-payment-mode" data-index="${index}">
                            <option value="monthly">شهري</option>
                            <option value="wps">WPS</option>
                        </select>
                    </div>
                    <div class="col-md-3 batch-amount-section-${index}" style="display: none;">
                        <label class="form-label small fw-bold">المبلغ</label>
                        <input type="number"
                               class="form-control form-control-sm batch-payment-amount"
                               data-index="${index}"
                               step="0.01"
                               min="0.01"
                               max="${emp.remaining}"
                               placeholder="أدخل المبلغ">
                        <small class="text-muted">الحد الأقصى: ${formatNumber(emp.remaining)}</small>
                        <div class="batch-wps-limit-${index}" style="display: none; margin-top: 4px;">
                            <small class="text-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                WPS: ${formatNumber(maxWpsForRemaining)} ريال
                            </small>
                        </div>
                        <div class="invalid-feedback batch-amount-error-${index}"></div>
                    </div>
                    <div class="col-md-1 text-end">
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeBatchEmployee(${index})">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(card);
    });

    attachBatchEventListeners();
}

// Attach event listeners to batch payment controls
function attachBatchEventListeners() {
    // Payment type change
    document.querySelectorAll('.batch-payment-type').forEach(select => {
        select.addEventListener('change', function() {
            const index = this.dataset.index;
            const amountSection = document.querySelector(`.batch-amount-section-${index}`);

            if (this.value === 'partial') {
                amountSection.style.display = 'block';
            } else {
                amountSection.style.display = 'none';
            }
        });
    });

    // Payment mode change
    document.querySelectorAll('.batch-payment-mode').forEach(select => {
        select.addEventListener('change', function() {
            const index = this.dataset.index;
            const wpsLimit = document.querySelector(`.batch-wps-limit-${index}`);

            if (this.value === 'wps') {
                wpsLimit.style.display = 'block';
                document.getElementById('batchWpsWarning').style.display = 'block';
            } else {
                wpsLimit.style.display = 'none';

                // Check if any other employee has WPS mode
                const hasWps = Array.from(document.querySelectorAll('.batch-payment-mode'))
                    .some(s => s.value === 'wps');
                if (!hasWps) {
                    document.getElementById('batchWpsWarning').style.display = 'none';
                }
            }
        });
    });

    // Amount validation
    document.querySelectorAll('.batch-payment-amount').forEach(input => {
        input.addEventListener('input', function() {
            const index = this.dataset.index;
            const emp = batchEmployeesData[index];
            const paymentMode = document.querySelector(`.batch-payment-mode[data-index="${index}"]`).value;
            const amount = parseFloat(this.value);
            const errorDiv = document.querySelector(`.batch-amount-error-${index}`);

            if (isNaN(amount) || amount <= 0) {
                this.classList.add('is-invalid');
                errorDiv.textContent = 'المبلغ يجب أن يكون أكبر من صفر';
                return;
            }

            if (amount > emp.remaining) {
                this.classList.add('is-invalid');
                errorDiv.textContent = 'المبلغ يتجاوز المبلغ المتبقي';
                return;
            }

            if (paymentMode === 'wps') {
                const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
                const remainingWpsAllowance = maxWpsAmount - emp.wpsPaid;
                const maxWpsForRemaining = Math.min(remainingWpsAllowance, emp.remaining);

                if (amount > maxWpsForRemaining) {
                    this.classList.add('is-invalid');
                    errorDiv.textContent = `يتجاوز حد WPS المتبقي (${formatNumber(maxWpsForRemaining)} ريال)`;
                    return;
                }
            }

            this.classList.remove('is-invalid');
            errorDiv.textContent = '';
        });
    });
}

// Remove employee from batch
function removeBatchEmployee(index) {
    batchEmployeesData.splice(index, 1);
    document.getElementById('batchSelectedCount').textContent = batchEmployeesData.length;

    if (batchEmployeesData.length === 0) {
        const modal = bootstrap.Modal.getInstance(document.getElementById('batchPaymentModal'));
        modal.hide();
        return;
    }

    renderBatchEmployees();
}

// Submit batch payments
document.getElementById('submitBatchPaymentBtn')?.addEventListener('click', async function() {
    const payments = [];
    let hasErrors = false;

    batchEmployeesData.forEach((emp, index) => {
        const paymentType = document.querySelector(`.batch-payment-type[data-index="${index}"]`).value;
        const paymentMode = document.querySelector(`.batch-payment-mode[data-index="${index}"]`).value;

        let amount = emp.remaining;

        if (paymentType === 'partial') {
            const amountInput = document.querySelector(`.batch-payment-amount[data-index="${index}"]`);
            amount = parseFloat(amountInput.value);

            if (!amount || amount <= 0) {
                hasErrors = true;
                amountInput.classList.add('is-invalid');
                return;
            }

            if (amount > emp.remaining) {
                hasErrors = true;
                amountInput.classList.add('is-invalid');
                return;
            }

            if (paymentMode === 'wps') {
                const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
                const maxWpsForRemaining = Math.min(maxWpsAmount, emp.remaining);

                if (amount > maxWpsForRemaining) {
                    hasErrors = true;
                    amountInput.classList.add('is-invalid');
                    return;
                }
            }
        }

        payments.push({
            employee_id: emp.id,
            payment_type: paymentType,
            payment_mode: paymentMode,
            amount: amount,
            notes: `دفع ${paymentType === 'full' ? 'كامل' : 'جزئي'} - ${paymentMode === 'wps' ? 'WPS' : 'شهري'}`
        });
    });

    if (hasErrors) {
        Swal.fire({
            icon: 'error',
            title: 'خطأ في البيانات',
            text: 'يرجى التحقق من المبالغ المدخلة',
            confirmButtonText: 'حسناً'
        });
        return;
    }

    // Confirm before processing
    const result = await Swal.fire({
        title: 'تأكيد معالجة الدفعات',
        html: `
            <p>هل أنت متأكد من معالجة <strong>${payments.length}</strong> دفعة؟</p>
            <div class="text-start mt-3">
                <strong>الإجمالي:</strong> ${formatNumber(payments.reduce((sum, p) => sum + p.amount, 0))} ريال
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، معالجة',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#198754'
    });

    if (!result.isConfirmed) return;

    // Show loading
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>جاري المعالجة...';

    try {
        const response = await fetch('{{ route("salary-payments.process", $invoice->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ payments })
        });

        const data = await response.json();

        if (response.ok && data.success) {
            Swal.fire({
                icon: 'success',
                title: 'نجح!',
                html: `
                    <p>${data.message}</p>
                    <div class="mt-3">
                        <strong>تم معالجة ${payments.length} دفعة بنجاح</strong>
                    </div>
                `,
                confirmButtonText: 'حسناً'
            }).then(() => {
                location.reload();
            });
        } else {
            // Show detailed error message
            let errorMessage = data.message || 'حدث خطأ أثناء معالجة الدفعات';

            // If there are errors, show them
            if (data.errors) {
                errorMessage += '<br><br><div class="text-start small bg-light p-3 rounded">';

                // Check if errors is an array (from payment service)
                if (Array.isArray(data.errors)) {
                    errorMessage += '<strong>تفاصيل الأخطاء:</strong><br>';
                    data.errors.forEach((error, index) => {
                        if (typeof error === 'object' && error.employee_id && error.error) {
                            errorMessage += `<div class="mt-2">`;
                            errorMessage += `<strong>موظف #${error.employee_id}:</strong> ${error.error}`;
                            errorMessage += `</div>`;
                        } else {
                            errorMessage += `<div class="mt-2">${JSON.stringify(error)}</div>`;
                        }
                    });
                }
                // Check if errors is an object (validation errors)
                else if (typeof data.errors === 'object') {
                    errorMessage += '<strong>أخطاء التحقق:</strong><br>';
                    Object.keys(data.errors).forEach(key => {
                        const errorValue = data.errors[key];
                        let errorText;

                        if (Array.isArray(errorValue)) {
                            errorText = errorValue.join(', ');
                        } else if (typeof errorValue === 'object' && errorValue !== null) {
                            errorText = JSON.stringify(errorValue);
                        } else {
                            errorText = String(errorValue);
                        }

                        errorMessage += `<div class="mt-2"><strong>${key}:</strong> ${errorText}</div>`;
                    });
                }

                errorMessage += '</div>';
            }

            // Log full error details for debugging
            console.error('Batch Payment Error:', data);

            Swal.fire({
                icon: 'error',
                title: 'فشل في معالجة الدفعات',
                html: errorMessage,
                confirmButtonText: 'حسناً',
                width: '600px'
            });
        }
    } catch (error) {
        console.error('Batch Payment Exception:', error);

        Swal.fire({
            icon: 'error',
            title: 'خطأ في الاتصال',
            html: `
                <p>حدث خطأ أثناء معالجة الدفعات</p>
                <div class="text-start small mt-3 p-3 bg-light rounded">
                    <strong>تفاصيل الخطأ:</strong><br>
                    ${error.message || 'خطأ غير معروف'}
                </div>
            `,
            confirmButtonText: 'حسناً',
            width: '600px'
        });
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-check-circle me-2"></i>تأكيد معالجة الدفعات';
    }
});

// Approve invoice
async function approveInvoice() {
    const result = await Swal.fire({
        title: 'اعتماد الفاتورة',
        text: 'هل أنت متأكد من اعتماد هذه الفاتورة؟',
        icon: 'question',
        input: 'textarea',
        inputPlaceholder: 'ملاحظات الاعتماد (اختياري)',
        showCancelButton: true,
        confirmButtonText: 'نعم، اعتماد',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#198754'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch('{{ route("salary-invoices.approve", $invoice->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    notes: result.value
                })
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تم الاعتماد',
                    text: data.message,
                    confirmButtonText: 'حسناً'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'فشل',
                    text: data.message,
                    confirmButtonText: 'حسناً'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ',
                text: 'حدث خطأ أثناء اعتماد الفاتورة',
                confirmButtonText: 'حسناً'
            });
        }
    }
}

// Utility function
function formatNumber(num) {
    return new Intl.NumberFormat('ar-SA', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(num);
}

// Load WPS settings on page load
document.addEventListener('DOMContentLoaded', loadWpsSettings);
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
function exportSalaryPDF() {
    const invoiceNumber   = '{{ $invoice->number }}';
    const invoiceDate     = '{{ $invoice->generation_date ?? now()->format("Y-m-d") }}';
    const clientName      = '{{ $invoice->client->name ?? "" }}';
    const clientLogo      = '{{ $invoice->client->logo ? asset("storage/" . $invoice->client->logo) : "" }}';
    const companyLogo     = '{{ asset("assets/img/logo.png") }}';
    const totalEmployees  = '{{ $summary["total_employees"] }}';
    const totalSalaries   = '{{ number_format($summary["total_salaries"], 2) }}';
    const totalPaid       = '{{ number_format($summary["total_paid"], 2) }}';
    const totalRemaining  = '{{ number_format($summary["total_remaining"], 2) }}';
    const paidCount       = '{{ $summary["paid_employees"] }}';
    const partialCount    = '{{ $summary["partially_paid_employees"] }}';
    const unpaidCount     = '{{ $summary["unpaid_employees"] }}';
    const wpsCount        = '{{ $summary["wps_employees"] }}';
    const monthlyCount    = '{{ $summary["monthly_employees"] }}';
    const approvalStatus  = '{{ $invoice->approval_status }}';
    const revisionStatus  = '{{ $invoice->revision_status }}';
    const today           = new Date().toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });

    // Build rows from the live table
    const tdStyle = 'padding:6px 8px;border-bottom:1px solid #e9ecef;font-size:11px;vertical-align:middle;';
    const sourceRows = document.querySelectorAll('table tbody tr');
    let tableRows = '';
    sourceRows.forEach((row, i) => {
        const cells = row.querySelectorAll('td');
        if (!cells.length) return;
        // Skip checkbox cell if present (first cell with input)
        const offset = cells[0].querySelector('input[type="checkbox"]') ? 1 : 0;
        const bg = i % 2 === 0 ? '#ffffff' : '#f8fafb';

        const id           = cells[offset]?.innerText.trim()     || '-';
        const name         = cells[offset+1]?.innerText.trim()   || '-';
        const project      = cells[offset+2]?.innerText.trim()   || '-';
        const workDays     = cells[offset+3]?.innerText.trim()   || '-';
        const basicSalary  = cells[offset+4]?.innerText.trim()   || '-';
        const bonuses      = cells[offset+5]?.innerText.trim()   || '-';
        const advances     = cells[offset+6]?.innerText.trim()   || '-';
        const monthlyDed   = cells[offset+7]?.innerText.trim()   || '-';
        const otherDed     = cells[offset+8]?.innerText.trim()   || '-';
        const netSalary    = cells[offset+9]?.innerText.trim()   || '-';
        const paid         = cells[offset+10]?.innerText.trim()  || '-';
        const remaining    = cells[offset+11]?.innerText.trim()  || '-';
        const salaryType   = cells[offset+12]?.innerText.trim()  || '-';
        const payStatus    = cells[offset+13]?.innerText.trim()  || '-';
        const lastPayment  = cells[offset+14]?.innerText.trim()  || '-';

        let statusColor = '#6c757d';
        if (payStatus.includes('مدفوع') && !payStatus.includes('جزئ')) statusColor = '#198754';
        else if (payStatus.includes('جزئ')) statusColor = '#fd7e14';
        else if (payStatus.includes('غير')) statusColor = '#dc3545';

        let typeColor = salaryType.includes('WPS') ? '#0dcaf0' : '#6c757d';

        tableRows += `
        <tr style="background:${bg};">
            <td style="${tdStyle}text-align:center;color:#6c757d;font-size:11px;">${id}</td>
            <td style="${tdStyle}font-weight:700;color:#1e4a46;">${name}</td>
            <td style="${tdStyle}color:#555;">${project}</td>
            <td style="${tdStyle}text-align:center;">${workDays}</td>
            <td style="${tdStyle}text-align:left;color:#0d6efd;font-weight:600;">${basicSalary}</td>
            <td style="${tdStyle}text-align:left;color:#198754;">${bonuses}</td>
            <td style="${tdStyle}text-align:left;color:#dc3545;">${advances}</td>
            <td style="${tdStyle}text-align:left;color:#fd7e14;">${monthlyDed}</td>
            <td style="${tdStyle}text-align:left;color:#dc3545;">${otherDed}</td>
            <td style="${tdStyle}text-align:left;font-weight:700;color:#198754;font-size:12px;">${netSalary}</td>
            <td style="${tdStyle}text-align:left;color:#0dcaf0;font-weight:600;">${paid}</td>
            <td style="${tdStyle}text-align:left;color:#dc3545;font-weight:600;">${remaining}</td>
            <td style="${tdStyle}text-align:center;"><span style="background:${typeColor};color:#fff;padding:2px 7px;border-radius:10px;font-size:10px;">${salaryType}</span></td>
            <td style="${tdStyle}text-align:center;"><span style="background:${statusColor};color:#fff;padding:2px 7px;border-radius:10px;font-size:10px;">${payStatus}</span></td>
            <td style="${tdStyle}text-align:center;font-size:11px;color:#888;">${lastPayment}</td>
        </tr>`;
    });

    const approvalBadge = approvalStatus === 'approved'
        ? `<span style="background:#198754;color:#fff;padding:3px 10px;border-radius:12px;font-size:11px;">معتمدة</span>`
        : approvalStatus === 'rejected'
        ? `<span style="background:#dc3545;color:#fff;padding:3px 10px;border-radius:12px;font-size:11px;">مرفوضة</span>`
        : `<span style="background:#ffc107;color:#000;padding:3px 10px;border-radius:12px;font-size:11px;">قيد الانتظار</span>`;

    const revisionBadge = revisionStatus === 'revision_approved'
        ? `<span style="background:#198754;color:#fff;padding:3px 10px;border-radius:12px;font-size:11px;">مراجعة معتمدة</span>`
        : revisionStatus === 'revision_rejected'
        ? `<span style="background:#dc3545;color:#fff;padding:3px 10px;border-radius:12px;font-size:11px;">مراجعة مرفوضة</span>`
        : `<span style="background:#0dcaf0;color:#000;padding:3px 10px;border-radius:12px;font-size:11px;">قيد المراجعة</span>`;

    const clientLogoHtml = clientLogo
        ? `<img src="${clientLogo}" style="height:50px;max-width:120px;object-fit:contain;" onerror="this.style.display='none'" />`
        : `<div style="width:50px;height:50px;background:#e9ecef;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:10px;color:#888;">${clientName.charAt(0)}</div>`;

    const html = `
    <!DOCTYPE html>
    <html dir="rtl" lang="ar">
    <head>
        <meta charset="UTF-8">
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Arial', 'Tahoma', sans-serif;
                direction: rtl;
                color: #2d3748;
                background: #fff;
                font-size: 12px;
            }
            .page { padding: 0; }

            /* ── Header ── */
            .pdf-header {
                background: linear-gradient(135deg, #1e4a46 0%, #2d6a65 60%, #326462 100%);
                color: #fff;
                padding: 20px 28px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .pdf-header .logos { display: flex; align-items: center; gap: 16px; }
            .pdf-header .company-logo img { height: 52px; object-fit: contain; filter: brightness(0) invert(1); }
            .pdf-header .divider { width: 1px; height: 50px; background: rgba(255,255,255,0.3); }
            .pdf-header .client-logo { display: flex; align-items: center; gap: 8px; }
            .pdf-header .client-logo-label { font-size: 10px; opacity: 0.8; }
            .pdf-header .title-block { text-align: left; }
            .pdf-header h1 { font-size: 18px; font-weight: 700; letter-spacing: 0.5px; }
            .pdf-header .subtitle { font-size: 11px; opacity: 0.85; margin-top: 4px; }
            .pdf-header .accent-bar {
                height: 3px;
                background: linear-gradient(90deg, #fbbd08, #f59e0b);
                margin-top: 14px;
                border-radius: 2px;
            }

            /* ── Meta strip ── */
            .meta-strip {
                background: #f8fafb;
                border-bottom: 2px solid #e2e8f0;
                padding: 10px 28px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
            }
            .meta-item { display: flex; flex-direction: column; align-items: center; }
            .meta-item .label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; }
            .meta-item .value { font-size: 12px; font-weight: 700; color: #1e4a46; margin-top: 2px; }

            /* ── Summary cards ── */
            .summary-section { padding: 14px 28px 10px; }
            .summary-title { font-size: 11px; font-weight: 700; color: #1e4a46; margin-bottom: 8px; border-right: 3px solid #fbbd08; padding-right: 8px; }
            .summary-grid { display: flex; gap: 10px; }
            .summary-card {
                flex: 1;
                border-radius: 8px;
                padding: 10px 12px;
                text-align: center;
                border: 1px solid;
            }
            .summary-card .s-label { font-size: 9px; color: #666; margin-bottom: 4px; }
            .summary-card .s-value { font-size: 15px; font-weight: 800; }
            .card-blue  { border-color: #bfdbfe; background: #eff6ff; }
            .card-blue  .s-value { color: #1d4ed8; }
            .card-green { border-color: #bbf7d0; background: #f0fdf4; }
            .card-green .s-value { color: #15803d; }
            .card-red   { border-color: #fecaca; background: #fef2f2; }
            .card-red   .s-value { color: #b91c1c; }
            .card-amber { border-color: #fde68a; background: #fffbeb; }
            .card-amber .s-value { color: #b45309; }
            .card-cyan  { border-color: #a5f3fc; background: #ecfeff; }
            .card-cyan  .s-value { color: #0e7490; }

            /* ── Financial bar ── */
            .financial-bar {
                margin: 0 28px 12px;
                background: linear-gradient(135deg, #1e4a46, #326462);
                border-radius: 10px;
                padding: 12px 20px;
                display: flex;
                justify-content: space-around;
                color: #fff;
            }
            .fin-item { text-align: center; }
            .fin-item .f-label { font-size: 9px; opacity: 0.8; }
            .fin-item .f-value { font-size: 14px; font-weight: 800; margin-top: 2px; }
            .fin-item .f-value.gold { color: #fbbd08; }
            .fin-item .f-value.green { color: #6ee7b7; }
            .fin-item .f-value.red   { color: #fca5a5; }
            .fin-divider { width: 1px; background: rgba(255,255,255,0.2); }

            /* ── Table ── */
            .table-section { padding: 0 28px 14px; }
            .table-title { font-size: 11px; font-weight: 700; color: #1e4a46; margin-bottom: 8px; border-right: 3px solid #fbbd08; padding-right: 8px; }
            table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
            thead tr {
                background: linear-gradient(135deg, #1e4a46, #326462);
                color: #fff;
            }
            thead th {
                padding: 8px 7px;
                text-align: right;
                font-weight: 600;
                font-size: 10px;
                white-space: nowrap;
                border: none;
            }
            thead th:first-child { border-radius: 0 6px 0 0; }
            thead th:last-child  { border-radius: 6px 0 0 0; }
            tbody tr:hover { background: #f0fdf4; }

            /* ── Footer ── */
            .pdf-footer {
                background: #1e4a46;
                color: rgba(255,255,255,0.75);
                padding: 10px 28px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                font-size: 9px;
                margin-top: 10px;
            }
            .pdf-footer .footer-brand { color: #fbbd08; font-weight: 700; font-size: 10px; }
            .pdf-footer .footer-center { text-align: center; }
            .pdf-footer .footer-right  { text-align: left; }
        </style>
    </head>
    <body>
    <div class="page">

        <!-- ══ HEADER ══ -->
        <div class="pdf-header">
            <div class="logos">
                <div class="company-logo">
                    <img src="${companyLogo}" onerror="this.style.display='none'" />
                </div>
                <div class="divider"></div>
                <div class="client-logo">
                    ${clientLogoHtml}
                    <div>
                        <div class="client-logo-label">العميل</div>
                        <div style="font-weight:700;font-size:13px;">${clientName}</div>
                    </div>
                </div>
            </div>
            <div class="title-block">
                <h1>كشف رواتب الموظفين</h1>
                <div class="subtitle">فاتورة رقم: #${invoiceNumber} &nbsp;|&nbsp; تاريخ: ${invoiceDate}</div>
                <div style="margin-top:8px;display:flex;gap:8px;justify-content:flex-end;">
                    ${approvalBadge}
                    ${revisionBadge}
                </div>
            </div>
        </div>
        <div class="accent-bar" style="height:3px;background:linear-gradient(90deg,#fbbd08,#f59e0b);"></div>

        <!-- ══ META STRIP ══ -->
        <div class="meta-strip">
            <div class="meta-item"><span class="label">رقم الفاتورة</span><span class="value">#${invoiceNumber}</span></div>
            <div class="meta-item"><span class="label">تاريخ الإصدار</span><span class="value">${invoiceDate}</span></div>
            <div class="meta-item"><span class="label">العميل</span><span class="value">${clientName}</span></div>
            <div class="meta-item"><span class="label">إجمالي الموظفين</span><span class="value">${totalEmployees}</span></div>
            <div class="meta-item"><span class="label">تاريخ التصدير</span><span class="value">${today}</span></div>
        </div>

        <!-- ══ SUMMARY CARDS ══ -->
        <div class="summary-section">
            <div class="summary-title">ملخص حالات الدفع</div>
            <div class="summary-grid">
                <div class="summary-card card-blue">
                    <div class="s-label">إجمالي الموظفين</div>
                    <div class="s-value">${totalEmployees}</div>
                </div>
                <div class="summary-card card-green">
                    <div class="s-label">مدفوع بالكامل</div>
                    <div class="s-value">${paidCount}</div>
                </div>
                <div class="summary-card card-amber">
                    <div class="s-label">مدفوع جزئياً</div>
                    <div class="s-value">${partialCount}</div>
                </div>
                <div class="summary-card card-red">
                    <div class="s-label">غير مدفوع</div>
                    <div class="s-value">${unpaidCount}</div>
                </div>
                <div class="summary-card card-cyan">
                    <div class="s-label">WPS</div>
                    <div class="s-value">${wpsCount}</div>
                </div>
                <div class="summary-card" style="border-color:#e2e8f0;background:#f8fafc;">
                    <div class="s-label">شهري</div>
                    <div class="s-value" style="color:#475569;">${monthlyCount}</div>
                </div>
            </div>
        </div>

        <!-- ══ FINANCIAL BAR ══ -->
        <div class="financial-bar">
            <div class="fin-item">
                <div class="f-label">إجمالي الرواتب</div>
                <div class="f-value gold">${totalSalaries} ر.س</div>
            </div>
            <div class="fin-divider"></div>
            <div class="fin-item">
                <div class="f-label">المبلغ المدفوع</div>
                <div class="f-value green">${totalPaid} ر.س</div>
            </div>
            <div class="fin-divider"></div>
            <div class="fin-item">
                <div class="f-label">المبلغ المتبقي</div>
                <div class="f-value red">${totalRemaining} ر.س</div>
            </div>
        </div>

        <!-- ══ EMPLOYEES TABLE ══ -->
        <div class="table-section">
            <div class="table-title">تفاصيل الموظفين</div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الموظف</th>
                        <th>المشروع</th>
                        <th style="text-align:center;">أيام العمل</th>
                        <th>الراتب الإجمالي</th>
                        <th>المكافآت</th>
                        <th>السلف</th>
                        <th>خصومات الشهر</th>
                        <th>خصومات أخرى</th>
                        <th>صافي الراتب</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th style="text-align:center;">النوع</th>
                        <th style="text-align:center;">حالة الدفع</th>
                        <th style="text-align:center;">آخر دفعة</th>
                    </tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        </div>

        <!-- ══ FOOTER ══ -->
        <div class="pdf-footer">
            <div class="footer-brand">نظام الفواتير</div>
            <div class="footer-center">كشف رواتب فاتورة #${invoiceNumber} &mdash; ${clientName}</div>
            <div class="footer-right">تاريخ التصدير: ${today}</div>
        </div>

    </div>
    </body>
    </html>`;

    const container = document.createElement('div');
    container.innerHTML = html;
    document.body.appendChild(container);

    const options = {
        margin: 0,
        filename: `كشف_رواتب_فاتورة_${invoiceNumber}_${invoiceDate}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
    };

    html2pdf().set(options).from(container).save().then(() => {
        document.body.removeChild(container);
    });
}
</script>
@endpush
@endsection
