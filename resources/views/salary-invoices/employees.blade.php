@extends('layouts.master')

@section('title', 'موظفي فاتورة الرواتب #' . $invoice->number)
@section('page_title', 'كشف رواتب الموظفين')
@section('page_subtitle', 'فاتورة #' . $invoice->number . ' - ' . ($invoice->client->name ?? ''))

@section('page_actions')
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" onclick="exportSalaryPDF()">
            <i class="bi bi-file-earmark-pdf me-2"></i>تصدير PDF
        </button>
        @if($invoice->revision_status == 'pending' && auth()->user()->hasPermission('preview_invoice_employees'))
            <button type="button" class="btn btn-warning" onclick="openRevisionModal()">
                <i class="bi bi-pencil-square me-2"></i>مراجعة
            </button>
        @endif
        @if($invoice->approval_status !== 'approved' && auth()->user()->hasPermission('approve_invoice_employees'))
            <button type="button" class="btn btn-primary" onclick="approveInvoice()">
                <i class="bi bi-check-circle me-2"></i>اعتماد
            </button>
        @endif
        <button type="button"
                class="btn btn-outline-info position-relative"
                data-bs-toggle="offcanvas"
                data-bs-target="#revisionHistoryOffcanvas"
                title="سجل المراجعات">
            <i class="bi bi-clock-history"></i>
            @if($invoice->revisionStatuses->count() > 0)
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;">
                    {{ $invoice->revisionStatuses->count() }}
                </span>
            @endif
        </button>
        <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-2"></i>رجوع
        </a>
    </div>
@endsection

@section('content')
    <style>
        /* Modern Card Styles */
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #edf2f7;
            transition: all 0.3s ease;
            height: 100%;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
            border-color: #cbd5e0;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .stat-label {
            font-size: 13px;
            color: #718096;
            margin-bottom: 4px;
        }
        .stat-value {
            font-size: 24px;
            font-weight: 700;
            line-height: 1.2;
        }

        /* Financial Bar */
        .financial-bar {
            background: linear-gradient(135deg, #1e4a46 0%, #2d6a65 100%);
            border-radius: 16px;
            padding: 20px 30px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            color: white;
            margin-bottom: 24px;
        }
        .fin-item {
            text-align: center;
        }
        .fin-label {
            font-size: 12px;
            opacity: 0.8;
            margin-bottom: 4px;
        }
        .fin-value {
            font-size: 22px;
            font-weight: 700;
        }
        .fin-value.gold { color: #fbbd08; }
        .fin-value.green { color: #6ee7b7; }
        .fin-value.red { color: #fca5a5; }

        /* Filter Tabs */
        .filter-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            padding: 4px;
            background: #f8fafc;
            border-radius: 12px;
        }
        .filter-tabs .btn {
            border-radius: 10px !important;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid #edf2f7;
            overflow-x: auto;
            overflow-y: visible;
            max-width: 100%;
        }
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background: #f8fafc;
            color: #1e4a46;
            font-weight: 600;
            font-size: 12px;
            padding: 15px 12px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .table tbody td {
            padding: 12px;
            font-size: 13px;
            vertical-align: middle;
            border-bottom: 1px solid #edf2f7;
        }
        .table tbody tr:hover {
            background-color: #f0fdf4;
        }

        /* Badges */
        .badge-custom {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-paid { background: #d1fae5; color: #065f46; }
        .badge-partial { background: #fed7aa; color: #92400e; }
        .badge-unpaid { background: #fee2e2; color: #991b1b; }
        .badge-wps { background: #cffafe; color: #0e7490; }
        .badge-monthly { background: #e2e8f0; color: #334155; }

        /* Amount Styles */
        .amount-positive { color: #059669; font-weight: 600; }
        .amount-negative { color: #dc2626; font-weight: 600; }
        .amount-neutral { color: #2563eb; font-weight: 600; }

        /* Search Box */
        .search-wrapper {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s;
        }
        .search-wrapper:focus-within {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .search-wrapper input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 14px;
        }
    </style>

    <div class="container-fluid px-4 py-3">
        <!-- Revision Notes Alert -->
        @if($invoice->revision_notes && $invoice->revision_status !== 'pending')
            <div class="alert alert-{{ $invoice->revision_status === 'revision_approved' ? 'success' : 'danger' }} alert-dismissible fade show rounded-3 mb-4 border-0" role="alert">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-{{ $invoice->revision_status === 'revision_approved' ? 'check-circle-fill' : 'exclamation-triangle-fill' }} fs-5"></i>
                    <div>
                        <strong>ملاحظات المراجعة:</strong> {{ $invoice->revision_notes }}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Summary Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background: #e0f2fe; color: #0284c7;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <span class="badge bg-light text-dark px-3 py-2">إجمالي</span>
                    </div>
                    <div class="stat-label">عدد الموظفين</div>
                    <div class="stat-value text-primary" id="card-total-employees">{{ $summary['total_employees'] }}</div>
                    <div class="mt-2 text-muted small">
                        <span class="text-success me-2"><i class="bi bi-check-circle-fill"></i> <span id="card-paid-employees">{{ $summary['paid_employees'] }}</span> مدفوع</span>
                        <span class="text-warning"><i class="bi bi-hourglass-split"></i> <span id="card-partial-employees">{{ $summary['partially_paid_employees'] }}</span> جزئي</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background: #dcfce7; color: #059669;">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <span class="badge bg-light text-dark px-3 py-2">إجمالي</span>
                    </div>
                    <div class="stat-label">إجمالي الرواتب</div>
                    <div class="stat-value text-success" id="card-total-salaries">{{ number_format($summary['total_salaries'], 0) }} <small class="fs-6">ر.س</small></div>
                    <div class="mt-2 text-muted small">صافي الرواتب</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background: #fef9c3; color: #a16207;">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <span class="badge bg-light text-dark px-3 py-2">مدفوع</span>
                    </div>
                    <div class="stat-label">المبلغ المدفوع</div>
                    <div class="stat-value text-warning" id="card-total-paid">{{ number_format($summary['total_paid'], 0) }} <small class="fs-6">ر.س</small></div>
                    <div class="mt-2 text-muted small"><span id="card-paid-label">{{ $summary['paid_employees'] }}</span> موظف مدفوع بالكامل</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background: #fee2e2; color: #b91c1c;">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <span class="badge bg-light text-dark px-3 py-2">متبقي</span>
                    </div>
                    <div class="stat-label">المبلغ المتبقي</div>
                    <div class="stat-value text-danger" id="card-total-remaining">{{ number_format($summary['total_remaining'], 0) }} <small class="fs-6">ر.س</small></div>
                    <div class="mt-2 text-muted small"><span id="card-unpaid-label">{{ $summary['unpaid_employees'] }}</span> موظف غير مدفوع</div>
                </div>
            </div>
        </div>

        <!-- Financial Progress Bar -->
        <div class="financial-bar">
            <div class="fin-item">
                <div class="fin-label">إجمالي الرواتب</div>
                <div class="fin-value gold" id="bar-total-salaries">{{ number_format($summary['total_salaries'], 0) }} ر.س</div>
            </div>
            <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.2);"></div>
            <div class="fin-item">
                <div class="fin-label">المبلغ المدفوع</div>
                <div class="fin-value green" id="bar-total-paid">{{ number_format($summary['total_paid'], 0) }} ر.س</div>
                <small class="opacity-75" id="bar-paid-pct">{{ $summary['total_employees'] > 0 ? round(($summary['total_paid'] / max($summary['total_salaries'],1)) * 100, 1) : 0 }}%</small>
            </div>
            <div style="width: 1px; height: 40px; background: rgba(255,255,255,0.2);"></div>
            <div class="fin-item">
                <div class="fin-label">المبلغ المتبقي</div>
                <div class="fin-value red" id="bar-total-remaining">{{ number_format($summary['total_remaining'], 0) }} ر.س</div>
                <small class="opacity-75" id="bar-remaining-pct">{{ $summary['total_employees'] > 0 ? round(($summary['total_remaining'] / max($summary['total_salaries'],1)) * 100, 1) : 0 }}%</small>
            </div>
        </div>

        <!-- Toolbar: Search + Filters -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-5">
                        <form method="GET" action="{{ route('salary-invoices.employees.index', $invoice->id) }}">
                            <div class="d-flex gap-2">
                                <div class="flex-grow-1 search-wrapper">
                                    <i class="bi bi-search text-muted"></i>
                                    <input type="text" name="search" value="{{ $search }}" placeholder="ابحث باسم الموظف أو رقم الهوية...">
                                </div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-search"></i>
                                </button>
                                @if(request()->has('search') || request()->has('filter'))
                                    <a href="{{ route('salary-invoices.employees.index', $invoice->id) }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-lg"></i>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" onchange="if(this.value) window.location.href='/salary-invoices/'+this.value+'/employees'">
                            @foreach($allSalaryInvoices as $inv)
                                <option value="{{ $inv->id }}" {{ $inv->id == $invoice->id ? 'selected' : '' }}>
                                    فاتورة #{{ $inv->number }} — {{ $inv->generation_date }} — {{ $inv->client->name ?? 'بدون عميل' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 text-md-end">
                        @if($invoice->approval_status === 'approved' && auth()->user()->hasPermission('add_invoice_employee_payment'))
                            <button type="button" class="btn btn-success" id="processBatchBtn"
                                    data-bs-toggle="modal" data-bs-target="#batchPaymentModal" disabled>
                                <i class="bi bi-cash-coin me-2"></i>
                                معالجة الدفعات (<span id="selectedCount">0</span>)
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs mt-4">
                    <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id, 'filter'=>'all', 'search'=>$search]) }}"
                       class="btn {{ $filter==='all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        <i class="bi bi-list-ul me-1"></i> الكل ({{ $summary['total_employees'] }})
                    </a>
                    <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id, 'filter'=>'unpaid', 'search'=>$search]) }}"
                       class="btn {{ $filter==='unpaid' ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="bi bi-x-circle me-1"></i> غير مدفوع ({{ $summary['unpaid_employees'] }})
                    </a>
                    <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id, 'filter'=>'partially_paid', 'search'=>$search]) }}"
                       class="btn {{ $filter==='partially_paid' ? 'btn-warning' : 'btn-outline-warning' }}">
                        <i class="bi bi-hourglass-split me-1"></i> مدفوع جزئياً ({{ $summary['partially_paid_employees'] }})
                    </a>
                    <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id, 'filter'=>'paid', 'search'=>$search]) }}"
                       class="btn {{ $filter==='paid' ? 'btn-success' : 'btn-outline-success' }}">
                        <i class="bi bi-check-circle me-1"></i> مدفوع ({{ $summary['paid_employees'] }})
                    </a>
                    <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id, 'filter'=>'wps', 'search'=>$search]) }}"
                       class="btn {{ $filter==='wps' ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="bi bi-bank me-1"></i> WPS ({{ $summary['wps_employees'] }})
                    </a>
                    <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id, 'filter'=>'monthly', 'search'=>$search]) }}"
                       class="btn {{ $filter==='monthly' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                        <i class="bi bi-calendar-month me-1"></i> شهري ({{ $summary['monthly_employees'] }})
                    </a>
                </div>
            </div>
        </div>

        <!-- Employees Table -->
        <div class="table-container">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            @if($invoice->approval_status === 'approved')
                                <th class="text-center" width="40">
                                    <div class="form-check d-flex justify-content-center">
                                        <input class="form-check-input" type="checkbox" id="selectAll">
                                    </div>
                                </th>
                            @endif
                            <th class="text-center">#</th>
                            <th class="text-center">الموظف</th>
                            <th class="text-center">المشروع</th>
                            <th class="text-center">أيام العمل</th>
                            <th class="text-center">الراتب</th>
                            <th class="text-center">المكافآت</th>
                            <th class="text-center">السلف</th>
                            <th class="text-center">الخصومات</th>
                            <th class="text-center">صافي الراتب</th>
                            <th class="text-center">المدفوع</th>
                            <th class="text-center">المتبقي</th>
                            <th class="text-center">رقم الآيبان</th>
                            <th class="text-center">اسم البنك</th>
                            <th class="text-center">صاحب الحساب</th>
                            <th class="text-center">النوع</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">آخر دفعة</th>
                            <th class="text-center">فترة الاستجابة</th>
                            <th class="text-center"></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($employees as $employee)
                            <tr data-payment-status="{{ $employee->payment_status }}"
                                data-salary-type="{{ $employee->salary_type ?? 'monthly' }}"
                                data-net-salary="{{ $employee->net_salary ?? $employee->total_salary ?? 0 }}"
                                data-total-paid="{{ $employee->total_paid ?? 0 }}"
                                data-remaining="{{ $employee->remaining_amount ?? 0 }}">
                                @if($invoice->approval_status === 'approved')
                                    <td class="text-center">
                                        <div class="form-check d-flex justify-content-center">
                                            <input class="form-check-input employee-checkbox"
                                                   type="checkbox"
                                                   value="{{ $employee->id }}"
                                                   data-employee-name="{{ $employee->employee_name }}"
                                                   data-total-salary="{{ $employee->total_salary ?? $employee->net_salary }}"
                                                   data-total-paid="{{ $employee->total_paid ?? 0 }}"
                                                   data-remaining="{{ $employee->remaining_amount ?? $employee->net_salary }}"
                                                   data-salary-type="{{ $employee->salary_type ?? 'monthly' }}"
                                                   data-wps-paid="{{ $employee->wps_paid ?? 0 }}"
                                                {{ $employee->payment_status === 'paid' ? 'disabled' : '' }}>
                                        </div>
                                    </td>
                                @endif
                                <td class="text-center text-muted">{{ $employee->id }}</td>
                                <td class="text-center">
                                    <div class="fw-bold">{{ $employee->employee_name }}</div>
                                </td>
                                <td class="text-center">{{ $employee->project ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ (int)($employee->work_days_count ?? $employee->work_days ?? 0) }}</td>
                                <td class="text-center amount-neutral">{{ number_format($employee->basic_salary ?? 0, 0) }}</td>
                                <td class="text-center amount-positive">{{ number_format($employee->bonuses ?? 0, 0) }}</td>
                                <td class="text-center amount-negative" style="color:#dc2626 !important; font-weight:600;">{{ number_format($employee->advance_deductions ?? 0, 0) }}</td>
                                <td class="text-center amount-negative" style="color:#dc2626 !important; font-weight:600;">{{ number_format((is_array($employee->deductions) ? 0 : ($employee->deductions ?? 0)) + ($employee->monthly_deductions ?? 0), 0) }}</td>
                                <td class="text-center amount-positive fw-bold">{{ number_format($employee->net_salary ?? $employee->total_salary ?? 0, 0) }}</td>
                                <td class="text-center text-info fw-bold">{{ number_format($employee->total_paid ?? 0, 0) }}</td>
                                <td class="text-center {{ ($employee->remaining_amount ?? 0) > 0 ? 'amount-negative' : 'text-success' }} fw-bold">
                                    {{ number_format($employee->remaining_amount ?? $employee->net_salary, 0) }}
                                </td>
                                <td class="text-center" style="font-size:12px; direction:ltr;">
                                    {{ $employee->iban ?? '-' }}
                                </td>
                                <td class="text-center" style="font-size:12px;">
                                    {{ $employee->bank_name ?? '-' }}
                                </td>
                                <td class="text-center" style="font-size:12px;">
                                    {{ $employee->account_holder_name ?? '-' }}
                                </td>
                                <td class="text-center">
                                    @if(($employee->salary_type ?? 'monthly') === 'wps')
                                        <span class="badge-custom badge-wps">
                                            <i class="bi bi-bank me-1"></i>WPS
                                        </span>
                                    @else
                                        <span class="badge-custom badge-monthly">
                                            <i class="bi bi-calendar-month me-1"></i>شهري
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($employee->payment_status === 'paid')
                                        <span class="badge-custom badge-paid">
                                            <i class="bi bi-check-circle-fill me-1"></i>مدفوع
                                        </span>
                                    @elseif($employee->payment_status === 'partially_paid')
                                        <span class="badge-custom badge-partial">
                                            <i class="bi bi-hourglass-split me-1"></i>جزئي
                                        </span>
                                    @else
                                        <span class="badge-custom badge-unpaid">
                                            <i class="bi bi-x-circle-fill me-1"></i>غير مدفوع
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-muted small">
                                    {{ $employee->last_payment_date ? \Carbon\Carbon::parse($employee->last_payment_date)->format('Y-m-d') : '-' }}
                                </td>
                                <td class="text-center">
                                    @php
                                        $responseTotalMinutes = null;
                                        if ($invoice->approved_at && $employee->last_payment_date) {
                                            $approvalDate = \Carbon\Carbon::parse($invoice->approved_at);
                                            $lastPayDate = \Carbon\Carbon::parse($employee->last_payment_date);
                                            $responseTotalMinutes = $approvalDate->diffInMinutes($lastPayDate);
                                        }
                                        $responseHours = $responseTotalMinutes !== null ? intdiv($responseTotalMinutes, 60) : null;
                                        $responseMinutes = $responseTotalMinutes !== null ? $responseTotalMinutes % 60 : null;
                                    @endphp
                                    @if($responseTotalMinutes !== null)
                                        <span class="badge {{ $responseHours > 720 ? 'bg-danger' : ($responseHours > 336 ? 'bg-warning text-dark' : 'bg-success') }}" title="الفرق بين تاريخ اعتماد الفاتورة وآخر دفعة للموظف">
                                            <i class="bi bi-clock me-1"></i>{{ $responseHours }} س {{ $responseMinutes }} د
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        @if($invoice->approval_status === 'approved' && $employee->payment_status !== 'paid' && auth()->user()->hasPermission('add_invoice_employee_payment'))
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
                    <i class="bi bi-inbox display-1 text-muted mb-3"></i>
                    <h5 class="text-muted">لا يوجد موظفين</h5>
                    <p class="text-muted">{{ $filter !== 'all' ? 'لا يوجد موظفين في هذا التصنيف' : 'لم يتم إضافة موظفين لهذه الفاتورة بعد' }}</p>
                </div>
            @endif
        </div>
    </div>

    @include('partials.modals.salary-payment')
    @include('partials.modals.batch-payment')
    @include('partials.modals.revision-modal')

    {{-- Revision History Offcanvas --}}
    <div class="offcanvas offcanvas-end" tabindex="-1" id="revisionHistoryOffcanvas" aria-labelledby="revisionHistoryLabel" style="width:420px;">
        <div class="offcanvas-header border-0 pb-0" style="background:linear-gradient(135deg,#1e4a46,#2d6a65);">
            <div>
                <h5 class="offcanvas-title text-white fw-bold" id="revisionHistoryLabel">
                    <i class="bi bi-clock-history me-2"></i>سجل المراجعات
                </h5>
                <p class="text-white opacity-75 small mb-0">فاتورة #{{ $invoice->number }}</p>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body p-0">
            {{-- Current Status Banner --}}
            <div class="px-4 py-3 border-bottom"
                 style="background:{{ $invoice->revision_status === 'revision_approved' ? '#f0fdf4' : ($invoice->revision_status === 'revision_rejected' ? '#fef2f2' : '#fffbeb') }};">
                <div class="d-flex align-items-center gap-2">
                    @if($invoice->revision_status === 'revision_approved')
                        <span class="badge rounded-pill px-3 py-2 fs-6" style="background:#d1fae5;color:#065f46;">
                            <i class="bi bi-patch-check-fill me-1"></i>مراجعة معتمدة
                        </span>
                    @elseif($invoice->revision_status === 'revision_rejected')
                        <span class="badge rounded-pill px-3 py-2 fs-6" style="background:#fee2e2;color:#991b1b;">
                            <i class="bi bi-patch-exclamation-fill me-1"></i>مراجعة مرفوضة
                        </span>
                    @else
                        <span class="badge rounded-pill px-3 py-2 fs-6" style="background:#fef3c7;color:#92400e;">
                            <i class="bi bi-hourglass-split me-1"></i>قيد المراجعة
                        </span>
                    @endif
                    <small class="text-muted">الحالة الحالية</small>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="px-4 py-3">
                @if($invoice->revisionStatuses->count() > 0)
                    <p class="text-muted small mb-3 fw-semibold">{{ $invoice->revisionStatuses->count() }} سجل مراجعة</p>
                    <div class="revision-timeline">
                        @foreach($invoice->revisionStatuses->sortByDesc('created_at') as $revision)
                            @php
                                $isApproved = $revision->revision_status === 'approved';
                                $iconColor  = $isApproved ? '#059669' : '#dc2626';
                                $iconBg     = $isApproved ? '#d1fae5' : '#fee2e2';
                                $icon       = $isApproved ? 'patch-check-fill' : 'patch-exclamation-fill';
                                $label      = $isApproved ? 'قبول المراجعة' : 'رفض المراجعة';
                            @endphp
                            <div class="revision-item d-flex gap-3 mb-4 position-relative">
                                {{-- Timeline dot --}}
                                <div class="flex-shrink-0 d-flex flex-column align-items-center">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                         style="width:38px;height:38px;background:{{ $iconBg }};color:{{ $iconColor }};font-size:1.1rem;flex-shrink:0;">
                                        <i class="bi bi-{{ $icon }}"></i>
                                    </div>
                                    @if(!$loop->last)
                                        <div style="width:2px;flex:1;background:#e2e8f0;margin-top:4px;min-height:24px;"></div>
                                    @endif
                                </div>
                                {{-- Content --}}
                                <div class="flex-grow-1 pb-2">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="fw-bold" style="color:{{ $iconColor }};font-size:0.9rem;">{{ $label }}</span>
                                        <small class="text-muted" style="font-size:0.75rem;white-space:nowrap;">
                                            {{ $revision->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="d-flex align-items-center gap-1 mb-2">
                                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white"
                                             style="width:20px;height:20px;font-size:0.65rem;font-weight:700;">
                                            {{ mb_substr($revision->revisedBy->name ?? 'N', 0, 1) }}
                                        </div>
                                        <small class="text-muted">{{ $revision->revisedBy->name ?? 'غير معروف' }}</small>
                                        <small class="text-muted">·</small>
                                        <small class="text-muted">{{ $revision->created_at->format('Y-m-d H:i') }}</small>
                                    </div>
                                    @if($revision->revision_notes)
                                        <div class="rounded-3 p-2 small"
                                             style="background:{{ $isApproved ? '#f0fdf4' : '#fef2f2' }};color:#475569;border-right:3px solid {{ $iconColor }};">
                                            <i class="bi bi-chat-quote me-1" style="color:{{ $iconColor }};"></i>
                                            {{ $revision->revision_notes }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="mb-3" style="width:64px;height:64px;border-radius:50%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                            <i class="bi bi-clock-history fs-3 text-muted"></i>
                        </div>
                        <p class="text-muted mb-0">لا يوجد سجل مراجعات بعد</p>
                        <small class="text-muted">ستظهر هنا المراجعات بعد إتمامها</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        // ============================================
        // Global Variables
        // ============================================
        let wpsMaxPercentage = 70;
        let currentEmployeeData = {};

        // ============================================
        // Initialize
        // ============================================
        document.addEventListener('DOMContentLoaded', function() {
            loadWpsSettings();
            initializeCheckboxes();
            recalculateCards();
        });

        // ============================================
        // Recalculate summary cards from visible rows
        // ============================================
        function recalculateCards() {
            const rows = document.querySelectorAll('tbody tr[data-payment-status]');
            if (!rows.length) return;

            let total = 0, paid = 0, partial = 0, unpaid = 0;
            let totalSalaries = 0, totalPaid = 0, totalRemaining = 0;

            rows.forEach(function(row) {
                const status = row.dataset.paymentStatus;
                const salary    = parseFloat(row.dataset.netSalary   || 0);
                const paidAmt   = parseFloat(row.dataset.totalPaid   || 0);
                const remaining = parseFloat(row.dataset.remaining   || 0);

                total++;
                totalSalaries  += salary;
                totalPaid      += paidAmt;
                totalRemaining += remaining;

                if (status === 'paid')             paid++;
                else if (status === 'partially_paid') partial++;
                else                               unpaid++;
            });

            const fmt = n => new Intl.NumberFormat('ar-SA').format(Math.round(n));
            const paidPct      = totalSalaries > 0 ? (totalPaid      / totalSalaries * 100).toFixed(1) : 0;
            const remainingPct = totalSalaries > 0 ? (totalRemaining / totalSalaries * 100).toFixed(1) : 0;

            const set = (id, val) => { const el = document.getElementById(id); if (el) el.innerHTML = val; };

            set('card-total-employees', total);
            set('card-paid-employees',  paid);
            set('card-partial-employees', partial);
            set('card-total-salaries',  fmt(totalSalaries) + ' <small class="fs-6">ر.س</small>');
            set('card-total-paid',      fmt(totalPaid)     + ' <small class="fs-6">ر.س</small>');
            set('card-paid-label',      paid);
            set('card-total-remaining', fmt(totalRemaining) + ' <small class="fs-6">ر.س</small>');
            set('card-unpaid-label',    unpaid);

            set('bar-total-salaries', fmt(totalSalaries) + ' ر.س');
            set('bar-total-paid',     fmt(totalPaid)     + ' ر.س');
            set('bar-total-remaining',fmt(totalRemaining)+ ' ر.س');
            set('bar-paid-pct',       paidPct      + '%');
            set('bar-remaining-pct',  remainingPct + '%');
        }

        // ============================================
        // WPS Settings
        // ============================================
        async function loadWpsSettings() {
            try {
                const response = await fetch('/salary-invoices/wps-settings');
                const data = await response.json();
                if (data.success) {
                    wpsMaxPercentage = data.wps_max_percentage;
                    document.querySelectorAll('[id$="WpsMaxPercentage"]').forEach(el => {
                        if (el) el.textContent = wpsMaxPercentage;
                    });
                }
            } catch (error) {
                console.error('Error loading WPS settings:', error);
            }
        }

        // ============================================
        // Checkbox Handling
        // ============================================
        function initializeCheckboxes() {
            const selectAll = document.getElementById('selectAll');
            if (!selectAll) return;

            selectAll.addEventListener('change', function() {
                document.querySelectorAll('.employee-checkbox:not(:disabled)').forEach(cb => {
                    cb.checked = this.checked;
                });
                updateSelectedCount();
            });

            document.querySelectorAll('.employee-checkbox').forEach(cb => {
                cb.addEventListener('change', updateSelectedCount);
            });
        }

        function updateSelectedCount() {
            const count = document.querySelectorAll('.employee-checkbox:checked').length;
            document.getElementById('selectedCount').textContent = count;
            const batchBtn = document.getElementById('processBatchBtn');
            if (batchBtn) {
                batchBtn.disabled = count === 0;
            }
        }

        // ============================================
        // Payment Modal (Single)
        // ============================================
        document.getElementById('paymentModal')?.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            currentEmployeeData = {
                id: button.getAttribute('data-employee-id'),
                name: button.getAttribute('data-employee-name'),
                totalSalary: parseFloat(button.getAttribute('data-total-salary')),
                totalPaid: parseFloat(button.getAttribute('data-total-paid')),
                remaining: parseFloat(button.getAttribute('data-remaining')),
                salaryType: button.getAttribute('data-salary-type')
            };

            document.getElementById('employeeId').value = currentEmployeeData.id;
            renderSingleEmployeeCard();
        });

        function renderSingleEmployeeCard() {
            const container = document.getElementById('singleEmployeeCard');
            if (!container) return;

            const emp = currentEmployeeData;
            const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
            const maxWpsForRemaining = Math.min(maxWpsAmount, emp.remaining);

            container.innerHTML = `
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-2">${emp.name}</h6>
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge-custom ${emp.salaryType === 'wps' ? 'badge-wps' : 'badge-monthly'}">
                                ${emp.salaryType === 'wps' ? 'WPS' : 'شهري'}
                            </span>
                        </div>
                        <div class="small">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">الإجمالي:</span>
                                <span class="fw-bold amount-neutral">${formatNumber(emp.totalSalary)} ريال</span>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">المدفوع:</span>
                                <span class="fw-bold text-info">${formatNumber(emp.totalPaid)} ريال</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">المتبقي:</span>
                                <span class="fw-bold amount-negative">${formatNumber(emp.remaining)} ريال</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold">نوع الدفع</label>
                                <select class="form-select" id="singlePaymentType">
                                    <option value="full">دفع كامل (${formatNumber(emp.remaining)} ريال)</option>
                                    <option value="partial">دفع جزئي</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">وضع الدفع</label>
                                <select class="form-select" id="singlePaymentMode">
                                    <option value="monthly">شهري</option>
                                    <option value="wps">WPS</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="singleAmountSection" style="display: none;">
                                <label class="form-label small fw-bold">المبلغ</label>
                                <input type="number" class="form-control" id="singlePaymentAmount"
                                       step="0.01" min="0.01" max="${emp.remaining}" placeholder="المبلغ">
                                <small class="text-muted">الحد الأقصى: ${formatNumber(emp.remaining)}</small>
                                <div id="singleWpsLimit" style="display: none; margin-top: 4px;">
                                    <small class="text-warning">
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                        WPS: ${formatNumber(maxWpsForRemaining)} ريال
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

            attachSingleEmployeeEvents();
        }

        function attachSingleEmployeeEvents() {
            const typeSelect = document.getElementById('singlePaymentType');
            const modeSelect = document.getElementById('singlePaymentMode');
            const amountSection = document.getElementById('singleAmountSection');
            const amountInput = document.getElementById('singlePaymentAmount');
            const wpsLimit = document.getElementById('singleWpsLimit');
            const wpsWarning = document.getElementById('wpsWarning');

            if (typeSelect) {
                typeSelect.addEventListener('change', function() {
                    if (this.value === 'partial') {
                        amountSection.style.display = 'block';
                        if (amountInput) amountInput.required = true;
                    } else {
                        amountSection.style.display = 'none';
                        if (amountInput) amountInput.required = false;
                    }
                });
            }

            if (modeSelect) {
                modeSelect.addEventListener('change', function() {
                    if (this.value === 'wps') {
                        if (wpsLimit) wpsLimit.style.display = 'block';
                        if (wpsWarning) wpsWarning.style.display = 'block';
                    } else {
                        if (wpsLimit) wpsLimit.style.display = 'none';
                        if (wpsWarning) wpsWarning.style.display = 'none';
                    }
                });
            }

            if (amountInput) {
                amountInput.addEventListener('input', function() {
                    validateAmount(this, 'singleAmountError');
                });
            }
        }

        function validateAmount(input, errorId) {
            const amount = parseFloat(input.value);
            const errorDiv = document.getElementById(errorId);

            if (!errorDiv) return true;

            if (isNaN(amount) || amount <= 0) {
                input.classList.add('is-invalid');
                errorDiv.textContent = 'المبلغ يجب أن يكون أكبر من صفر';
                return false;
            }

            if (amount > currentEmployeeData.remaining) {
                input.classList.add('is-invalid');
                errorDiv.textContent = 'المبلغ يتجاوز المبلغ المتبقي';
                return false;
            }

            if (document.getElementById('singlePaymentMode')?.value === 'wps') {
                const maxWpsAmount = (currentEmployeeData.totalSalary * wpsMaxPercentage) / 100;
                const actualMax = Math.min(maxWpsAmount, currentEmployeeData.remaining);

                if (amount > actualMax) {
                    input.classList.add('is-invalid');
                    errorDiv.textContent = `المبلغ يتجاوز حد WPS (${formatNumber(actualMax)} ريال)`;
                    return false;
                }
            }

            input.classList.remove('is-invalid');
            errorDiv.textContent = '';
            return true;
        }

        // ============================================
        // Submit Payment Form
        // ============================================
        document.getElementById('paymentForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            if (!validatePaymentForm()) return;

            const submitBtn = document.getElementById('submitPaymentBtn');
            const originalText = submitBtn.innerHTML;
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
                        payments: [buildPaymentData()]
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'تمت المعالجة بنجاح',
                        text: data.message,
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#10b981'
                    });
                    location.reload();
                } else {
                    showError(data);
                }
            } catch (error) {
                console.error('Payment Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الاتصال',
                    text: error.message || 'حدث خطأ أثناء معالجة الدفع',
                    confirmButtonText: 'حسناً'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        function validatePaymentForm() {
            const paymentType = document.getElementById('singlePaymentType').value;
            const paymentMode = document.getElementById('singlePaymentMode').value;

            if (paymentType === 'partial') {
                const amountInput = document.getElementById('singlePaymentAmount');
                return validateAmount(amountInput, 'singleAmountError');
            }

            return true;
        }

        function buildPaymentData() {
            const paymentType = document.getElementById('singlePaymentType').value;
            const paymentMode = document.getElementById('singlePaymentMode').value;
            const notes = document.getElementById('paymentNotes').value;

            let amount = currentEmployeeData.remaining;
            if (paymentType === 'partial') {
                amount = parseFloat(document.getElementById('singlePaymentAmount').value);
            }

            return {
                employee_id: currentEmployeeData.id,
                payment_type: paymentType,
                payment_mode: paymentMode,
                amount: amount,
                notes: notes
            };
        }

        // ============================================
        // Batch Payment Modal
        // ============================================
        let batchEmployeesData = [];

        document.getElementById('batchPaymentModal')?.addEventListener('show.bs.modal', function() {
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
            renderBatchEmployees();
        });

        function renderBatchEmployees() {
            const container = document.getElementById('batchEmployeesList');
            if (!container) return;

            container.innerHTML = '';

            batchEmployeesData.forEach((emp, index) => {
                const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
                const remainingWpsAllowance = maxWpsAmount - emp.wpsPaid;
                const maxWpsForRemaining = Math.min(remainingWpsAllowance, emp.remaining);

                const card = document.createElement('div');
                card.className = 'card border-0 shadow-sm mb-3';
                card.innerHTML = `
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <h6 class="fw-bold mb-2">${emp.name}</h6>
                        <span class="badge-custom ${emp.salaryType === 'wps' ? 'badge-wps' : 'badge-monthly'}">
                            ${emp.salaryType === 'wps' ? 'WPS' : 'شهري'}
                        </span>
                        <div class="mt-2 small">
                            <div><span class="text-muted">المتبقي:</span> <span class="fw-bold amount-negative">${formatNumber(emp.remaining)}</span></div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">نوع الدفع</label>
                                <select class="form-select form-select-sm batch-payment-type" data-index="${index}">
                                    <option value="full">دفع كامل (${formatNumber(emp.remaining)} ريال)</option>
                                    <option value="partial">دفع جزئي</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">وضع الدفع</label>
                                <select class="form-select form-select-sm batch-payment-mode" data-index="${index}">
                                    <option value="monthly">شهري</option>
                                    <option value="wps">WPS</option>
                                </select>
                            </div>
                            <div class="col-md-3 batch-amount-section-${index}" style="display: none;">
                                <label class="form-label small fw-bold">المبلغ</label>
                                <input type="number" class="form-control form-control-sm batch-payment-amount"
                                       data-index="${index}" step="0.01" min="0.01" max="${emp.remaining}" placeholder="المبلغ">
                                <div class="invalid-feedback batch-amount-error-${index}"></div>
                            </div>
                            <div class="col-md-2 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger mt-4" onclick="removeBatchEmployee(${index})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="batch-wps-limit-${index} small text-warning mt-2" style="display: none;">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            الحد الأقصى WPS: ${formatNumber(maxWpsForRemaining)} ريال
                        </div>
                    </div>
                </div>
            </div>
        `;
                container.appendChild(card);
            });

            attachBatchEvents();
        }

        function attachBatchEvents() {
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
                    const hasWps = Array.from(document.querySelectorAll('.batch-payment-mode')).some(s => s.value === 'wps');

                    if (this.value === 'wps') {
                        if (wpsLimit) wpsLimit.style.display = 'block';
                    } else {
                        if (wpsLimit) wpsLimit.style.display = 'none';
                    }

                    document.getElementById('batchWpsWarning').style.display = hasWps ? 'block' : 'none';
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
                        if (errorDiv) errorDiv.textContent = 'المبلغ يجب أن يكون أكبر من صفر';
                        return;
                    }

                    if (amount > emp.remaining) {
                        this.classList.add('is-invalid');
                        if (errorDiv) errorDiv.textContent = 'المبلغ يتجاوز المبلغ المتبقي';
                        return;
                    }

                    if (paymentMode === 'wps') {
                        const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
                        const maxWpsForRemaining = Math.min(maxWpsAmount, emp.remaining);

                        if (amount > maxWpsForRemaining) {
                            this.classList.add('is-invalid');
                            if (errorDiv) errorDiv.textContent = `يتجاوز حد WPS (${formatNumber(maxWpsForRemaining)} ريال)`;
                            return;
                        }
                    }

                    this.classList.remove('is-invalid');
                    if (errorDiv) errorDiv.textContent = '';
                });
            });
        }

        function removeBatchEmployee(index) {
            batchEmployeesData.splice(index, 1);
            document.getElementById('batchSelectedCount').textContent = batchEmployeesData.length;

            if (batchEmployeesData.length === 0) {
                bootstrap.Modal.getInstance(document.getElementById('batchPaymentModal')).hide();
                return;
            }

            renderBatchEmployees();
        }

        // Submit Batch Payments
        document.getElementById('submitBatchPaymentBtn')?.addEventListener('click', async function() {
            const payments = [];
            let hasErrors = false;

            for (let i = 0; i < batchEmployeesData.length; i++) {
                const emp = batchEmployeesData[i];
                const paymentType = document.querySelector(`.batch-payment-type[data-index="${i}"]`).value;
                const paymentMode = document.querySelector(`.batch-payment-mode[data-index="${i}"]`).value;
                const notes = `دفعة ${paymentType === 'full' ? 'كاملة' : 'جزئية'} - ${paymentMode === 'wps' ? 'WPS' : 'شهري'}`;

                let amount = emp.remaining;

                if (paymentType === 'partial') {
                    const amountInput = document.querySelector(`.batch-payment-amount[data-index="${i}"]`);
                    if (!amountInput || !amountInput.value) {
                        hasErrors = true;
                        continue;
                    }

                    amount = parseFloat(amountInput.value);

                    // Validate
                    if (isNaN(amount) || amount <= 0 || amount > emp.remaining) {
                        hasErrors = true;
                        continue;
                    }

                    if (paymentMode === 'wps') {
                        const maxWpsAmount = (emp.totalSalary * wpsMaxPercentage) / 100;
                        const maxWpsForRemaining = Math.min(maxWpsAmount, emp.remaining);
                        if (amount > maxWpsForRemaining) {
                            hasErrors = true;
                            continue;
                        }
                    }
                }

                payments.push({
                    employee_id: emp.id,
                    payment_type: paymentType,
                    payment_mode: paymentMode,
                    amount: amount,
                    notes: notes
                });
            }

            if (hasErrors || payments.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في البيانات',
                    text: 'يرجى التحقق من المبالغ المدخلة',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            const result = await Swal.fire({
                title: 'تأكيد معالجة الدفعات',
                html: `
            <p>هل أنت متأكد من معالجة <strong>${payments.length}</strong> دفعة؟</p>
            <div class="alert alert-info mt-3 p-3 text-start">
                <div class="d-flex justify-content-between mb-2">
                    <span>الإجمالي:</span>
                    <span class="fw-bold">${formatNumber(payments.reduce((sum, p) => sum + p.amount, 0))} ريال</span>
                </div>
            </div>
        `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، معالجة',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#10b981'
            });

            if (!result.isConfirmed) return;

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
                    await Swal.fire({
                        icon: 'success',
                        title: 'تمت المعالجة بنجاح',
                        html: `
                    <p>${data.message}</p>
                    <div class="mt-3">
                        <strong>تم معالجة ${payments.length} دفعة بنجاح</strong>
                    </div>
                `,
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#10b981'
                    });
                    location.reload();
                } else {
                    showError(data);
                }
            } catch (error) {
                console.error('Batch Payment Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الاتصال',
                    text: error.message || 'حدث خطأ أثناء معالجة الدفعات',
                    confirmButtonText: 'حسناً'
                });
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="bi bi-check-circle me-2"></i>تأكيد معالجة الدفعات';
            }
        });

        // ============================================
        // Revision Modal
        // ============================================
        function openRevisionModal() {
            const modal = new bootstrap.Modal(document.getElementById('revisionModal'));
            modal.show();
        }

        document.getElementById('revisionForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            const checkedRadio = document.querySelector('input[name="revision_status"]:checked');
            if (!checkedRadio) {
                Swal.fire({ icon: 'warning', title: 'تنبيه', text: 'يرجى اختيار قرار المراجعة', confirmButtonText: 'حسناً' });
                return;
            }

            const revisionStatus = checkedRadio.value;
            const revisionNotes = document.getElementById('revisionNotes').value;

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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        revision_status: revisionStatus,
                        revision_notes: revisionNotes
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('revisionModal')).hide();

                    await Swal.fire({
                        icon: 'success',
                        title: 'تمت المراجعة بنجاح',
                        text: data.message,
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#10b981'
                    });

                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        location.reload();
                    }
                } else {
                    showError(data);
                }
            } catch (error) {
                console.error('Revision Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الاتصال',
                    text: error.message || 'حدث خطأ أثناء المراجعة',
                    confirmButtonText: 'حسناً'
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });

        // ============================================
        // Approve Invoice
        // ============================================
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
                confirmButtonColor: '#10b981',
                inputAttributes: {
                    'aria-label': 'ملاحظات الاعتماد'
                }
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
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#10b981'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'فشل في الاعتماد',
                            text: data.message,
                            confirmButtonText: 'حسناً'
                        });
                    }
                } catch (error) {
                    console.error('Approve Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ أثناء اعتماد الفاتورة',
                        confirmButtonText: 'حسناً'
                    });
                }
            }
        }

        // ============================================
        // Error Display
        // ============================================
        function showError(data) {
            let errorMessage = data.message || 'حدث خطأ أثناء المعالجة';

            if (data.errors) {
                errorMessage += '<br><br><div class="text-start small bg-light p-3 rounded">';
                errorMessage += '<strong>تفاصيل الأخطاء:</strong><br>';

                if (Array.isArray(data.errors)) {
                    data.errors.forEach(err => {
                        if (err.error) {
                            errorMessage += `<div class="mt-2">- ${err.error}</div>`;
                        }
                    });
                } else if (typeof data.errors === 'object') {
                    Object.keys(data.errors).forEach(key => {
                        const errorValue = data.errors[key];
                        if (Array.isArray(errorValue)) {
                            errorValue.forEach(msg => {
                                errorMessage += `<div class="mt-2">- ${msg}</div>`;
                            });
                        }
                    });
                }

                errorMessage += '</div>';
            }

            Swal.fire({
                icon: 'error',
                title: 'فشل في المعالجة',
                html: errorMessage,
                confirmButtonText: 'حسناً',
                width: '600px'
            });
        }

        // ============================================
        // PDF Export — delegates to window._exportSalaryPDF defined below
        // ============================================
        function exportSalaryPDF() { window._exportSalaryPDF && window._exportSalaryPDF(); }

        // ============================================
        // Utility Functions
        // ============================================
        function formatNumber(num) {
            return new Intl.NumberFormat('ar-SA', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(num);
        }
    </script>
@endpush

{{-- Inline script so exportSalaryPDF is available globally before @push scripts load --}}
<script>
window._exportSalaryPDF = function exportSalaryPDF() {
    const invoiceNumber   = '{{ $invoice->number }}';
    const invoiceDate     = '{{ $invoice->generation_date ?? now()->format("Y-m-d") }}';
    const clientName      = '{{ $invoice->client->name ?? "" }}';
    const clientLogo      = '{{ $invoice->client->logo ? asset("storage/" . $invoice->client->logo) : "" }}';
    const companyLogoSrc  = '{{ asset("assets/img/logo.png") }}';
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

    function getWhiteLogoDataUrl(src, callback) {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            const canvas = document.createElement('canvas');
            canvas.width = img.naturalWidth;
            canvas.height = img.naturalHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            ctx.globalCompositeOperation = 'source-in';
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            callback(canvas.toDataURL('image/png'));
        };
        img.onerror = function() { callback(''); };
        img.src = src + '?v=' + Date.now();
    }

    getWhiteLogoDataUrl(companyLogoSrc, function(whiteLogoDataUrl) {
    const companyLogo = whiteLogoDataUrl || companyLogoSrc;

    let tableRows = '';
    document.querySelectorAll('table tbody tr').forEach((row, i) => {
        const cells = row.querySelectorAll('td');
        if (!cells.length) return;
        const offset = cells[0].querySelector('input[type="checkbox"]') ? 1 : 0;
        const bg = i % 2 === 0 ? '#ffffff' : '#f8fafc';
        const id          = cells[offset]?.innerText.trim() || '-';
        const name        = cells[offset+1]?.innerText.trim() || '-';
        const project     = cells[offset+2]?.innerText.trim() || '-';
        const workDays    = cells[offset+3]?.innerText.trim() || '-';
        const basicSalary = cells[offset+4]?.innerText.trim() || '-';
        const bonuses     = cells[offset+5]?.innerText.trim() || '-';
        const advances    = cells[offset+6]?.innerText.trim() || '-';
        const deductions  = cells[offset+7]?.innerText.trim() || '-';
        const netSalary   = cells[offset+8]?.innerText.trim() || '-';
        const paid        = cells[offset+9]?.innerText.trim() || '-';
        const remaining   = cells[offset+10]?.innerText.trim() || '-';
        const salaryType  = cells[offset+11]?.innerText.trim() || '-';
        const payStatus   = cells[offset+12]?.innerText.trim() || '-';
        const lastPayment = cells[offset+13]?.innerText.trim() || '-';

        let statusBg = '#e2e8f0', statusColor = '#334155';
        if (payStatus.includes('مدفوع') && !payStatus.includes('جزئ')) { statusBg='#d1fae5'; statusColor='#065f46'; }
        else if (payStatus.includes('جزئ')) { statusBg='#fed7aa'; statusColor='#92400e'; }
        else if (payStatus.includes('غير')) { statusBg='#fee2e2'; statusColor='#991b1b'; }

        const typeBg    = salaryType.includes('WPS') ? '#cffafe' : '#e2e8f0';
        const typeColor = salaryType.includes('WPS') ? '#0e7490' : '#334155';

        const td = 'padding:7px 9px;border-bottom:1px solid #e2e8f0;font-size:11px;vertical-align:middle;';
        tableRows += `
        <tr style="background:${bg};">
            <td style="${td}text-align:center;color:#64748b;">${id}</td>
            <td style="${td}font-weight:600;color:#1e293b;">${name}</td>
            <td style="${td}color:#475569;">${project}</td>
            <td style="${td}text-align:center;font-weight:600;">${workDays}</td>
            <td style="${td}text-align:right;color:#2563eb;">${basicSalary}</td>
            <td style="${td}text-align:right;color:#059669;">${bonuses}</td>
            <td style="${td}text-align:right;color:#dc2626;">${advances}</td>
            <td style="${td}text-align:right;color:#dc2626;">${deductions}</td>
            <td style="${td}text-align:right;font-weight:700;color:#059669;">${netSalary}</td>
            <td style="${td}text-align:right;color:#0891b2;font-weight:600;">${paid}</td>
            <td style="${td}text-align:right;color:#dc2626;font-weight:600;">${remaining}</td>
            <td style="${td}text-align:center;"><span style="background:${typeBg};color:${typeColor};padding:2px 8px;border-radius:10px;font-size:10px;">${salaryType}</span></td>
            <td style="${td}text-align:center;"><span style="background:${statusBg};color:${statusColor};padding:2px 8px;border-radius:10px;font-size:10px;">${payStatus}</span></td>
            <td style="${td}text-align:center;color:#64748b;">${lastPayment}</td>
        </tr>`;
    });

    const approvalBadge = approvalStatus === 'approved'
        ? `<span style="background:#d1fae5;color:#065f46;padding:4px 12px;border-radius:20px;font-size:11px;">معتمدة</span>`
        : approvalStatus === 'rejected'
        ? `<span style="background:#fee2e2;color:#991b1b;padding:4px 12px;border-radius:20px;font-size:11px;">مرفوضة</span>`
        : `<span style="background:#fef9c3;color:#854d0e;padding:4px 12px;border-radius:20px;font-size:11px;">قيد الانتظار</span>`;

    const revisionBadge = revisionStatus === 'revision_approved'
        ? `<span style="background:#d1fae5;color:#065f46;padding:4px 12px;border-radius:20px;font-size:11px;">مراجعة معتمدة</span>`
        : revisionStatus === 'revision_rejected'
        ? `<span style="background:#fee2e2;color:#991b1b;padding:4px 12px;border-radius:20px;font-size:11px;">مراجعة مرفوضة</span>`
        : `<span style="background:#cffafe;color:#0e7490;padding:4px 12px;border-radius:20px;font-size:11px;">قيد المراجعة</span>`;

    const clientLogoHtml = clientLogo
        ? `<img src="${clientLogo}" style="height:45px;width:auto;object-fit:contain;" onerror="this.style.display='none'" />`
        : `<div style="width:45px;height:45px;background:#e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#475569;">${clientName.charAt(0)}</div>`;

    const thStyle = 'padding:10px 9px;background:#1e4a46;color:#fff;font-weight:600;font-size:11px;white-space:nowrap;border:none;';

    const html = `<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<title>كشف رواتب فاتورة #${invoiceNumber}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family: 'Tahoma','Arial',sans-serif; direction:rtl; background:#fff; color:#1e293b; font-size:12px; padding:16px; word-spacing:normal; letter-spacing:normal; }
.pdf-header { background:linear-gradient(135deg,#1e4a46,#2d6a65); color:white; padding:18px 24px; border-radius:12px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; }
.logos { display:flex; align-items:center; gap:16px; }
.divider { width:1px; height:40px; background:rgba(255,255,255,0.3); }
.stats-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:14px; }
.stat-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; text-align:center; }
.stat-box .sl { font-size:10px; color:#64748b; margin-bottom:4px; }
.stat-box .sv { font-size:16px; font-weight:700; }
.fin-bar { background:linear-gradient(135deg,#1e4a46,#2d6a65); border-radius:8px; padding:12px 20px; display:flex; justify-content:space-around; align-items:center; color:white; margin-bottom:14px; }
.fi { text-align:center; }
.fi .fl { font-size:10px; opacity:0.8; margin-bottom:3px; }
.fi .fv { font-size:16px; font-weight:700; }
.fi .fv.gold { color:#fbbd08; }
.fi .fv.green { color:#6ee7b7; }
.fi .fv.red { color:#fca5a5; }
table { width:100%; border-collapse:collapse; font-size:11px; }
thead th { background:#1e4a46; color:#fff; padding:9px 8px; font-weight:600; white-space:nowrap; }
tbody td { padding:7px 8px; border-bottom:1px solid #e2e8f0; vertical-align:middle; }
.pdf-footer {
    margin-top: 20px;
    padding: 16px 28px;
    background: #f1f5f9;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
    color: #475569;
    font-size: 12px;
    width: 100%;
    box-sizing: border-box;
}
</style>
</head>
<body>
<div class="pdf-header">
  <div style="text-align:right;">
    <div style="font-size:20px;font-weight:700;margin-bottom:6px;">كشف رواتب الموظفين</div>
    <div style="font-size:12px;opacity:0.85;margin-bottom:4px;">
      <span>فاتورة رقم: ${invoiceNumber}</span>
      <span style="margin:0 8px;opacity:0.5;">|</span>
      <span>التاريخ: ${invoiceDate}</span>
      <span style="margin:0 8px;opacity:0.5;">|</span>
      <span>العميل: ${clientName}</span>
    </div>
    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px;">${approvalBadge} ${revisionBadge}</div>
  </div>
  <div class="logos">
    ${clientLogoHtml}
    <div class="divider"></div>
    ${companyLogo ? `<img src="${companyLogo}" style="height:38px;">` : ''}
  </div>
</div>

<div class="stats-grid">
  <div class="stat-box"><div class="sl">إجمالي الموظفين</div><div class="sv" style="color:#0284c7;">${totalEmployees}</div></div>
  <div class="stat-box"><div class="sl">مدفوع بالكامل</div><div class="sv" style="color:#059669;">${paidCount}</div></div>
  <div class="stat-box"><div class="sl">مدفوع جزئياً</div><div class="sv" style="color:#d97706;">${partialCount}</div></div>
  <div class="stat-box"><div class="sl">غير مدفوع</div><div class="sv" style="color:#dc2626;">${unpaidCount}</div></div>
  <div class="stat-box"><div class="sl">WPS</div><div class="sv" style="color:#0891b2;">${wpsCount}</div></div>
  <div class="stat-box"><div class="sl">شهري</div><div class="sv" style="color:#4b5563;">${monthlyCount}</div></div>
</div>

<div class="fin-bar">
  <div class="fi"><div class="fl">إجمالي الرواتب</div><div class="fv gold">${totalSalaries} ر.س</div></div>
  <div style="width:1px;height:36px;background:rgba(255,255,255,0.2);"></div>
  <div class="fi"><div class="fl">المبلغ المدفوع</div><div class="fv green">${totalPaid} ر.س</div></div>
  <div style="width:1px;height:36px;background:rgba(255,255,255,0.2);"></div>
  <div class="fi"><div class="fl">المبلغ المتبقي</div><div class="fv red">${totalRemaining} ر.س</div></div>
</div>

<table>
  <thead>
    <tr>
      <th style="text-align:center;">#</th>
      <th style="text-align:right;">اسم الموظف</th>
      <th style="text-align:right;">المشروع</th>
      <th style="text-align:center;">أيام العمل</th>
      <th style="text-align:right;">الراتب الأساسي</th>
      <th style="text-align:right;">المكافآت</th>
      <th style="text-align:right;">السلف</th>
      <th style="text-align:right;">الخصومات</th>
      <th style="text-align:right;">صافي الراتب</th>
      <th style="text-align:right;">المدفوع</th>
      <th style="text-align:right;">المتبقي</th>
      <th style="text-align:center;">النوع</th>
      <th style="text-align:center;">حالة الدفع</th>
      <th style="text-align:center;">آخر دفعة</th>
    </tr>
  </thead>
  <tbody>${tableRows}</tbody>
</table>

<div class="pdf-footer">
  <span style="font-weight:700;color:#1e4a46;">نظام الفواتير</span>
  <span>كشف رواتب فاتورة #${invoiceNumber} — ${clientName}</span>
  <span>تاريخ التصدير: ${today}</span>
</div>
</body>
</html>`;

    if (typeof html2pdf === 'undefined') {
        alert('جاري تحميل مكتبة PDF، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
        return;
    }

    const container = document.createElement('div');
    container.innerHTML = html;
    document.body.appendChild(container);

    html2pdf().set({
        margin: [8, 8, 8, 8],
        filename: `كشف_رواتب_فاتورة_${invoiceNumber}_${invoiceDate}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
    }).from(container).save().then(() => {
        document.body.removeChild(container);
    });
    }); // end getWhiteLogoDataUrl
};
</script>
