@extends('layouts.master')

@section('title', 'موظفي فاتورة الرواتب #' . $invoice->number)

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">موظفي فاتورة الرواتب #{{ $invoice->number }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('invoices.index') }}">الفواتير</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoices.show', $invoice->id) }}">فاتورة #{{ $invoice->number }}</a></li>
                    <li class="breadcrumb-item active">الموظفين</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-2"></i>رجوع للفاتورة
            </a>
        </div>
    </div>

    <!-- Invoice Status Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">حالة الاعتماد</h6>
                    @if($invoice->approval_status === 'approved')
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle me-1"></i>معتمدة
                        </span>
                    @elseif($invoice->approval_status === 'rejected')
                        <span class="badge bg-danger fs-6">
                            <i class="bi bi-x-circle me-1"></i>مرفوضة
                        </span>
                    @else
                        <span class="badge bg-warning fs-6">
                            <i class="bi bi-clock me-1"></i>قيد الانتظار
                        </span>
                    @endif
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">تاريخ الفاتورة</h6>
                    <p class="mb-0 fw-bold">{{ $invoice->generation_date }}</p>
                </div>
                <div class="col-md-3">
                    <h6 class="text-muted mb-1">العميل</h6>
                    <p class="mb-0 fw-bold">{{ $invoice->client->name ?? '-' }}</p>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        @permission('preview_invoice_employees')
                            @if($invoice->approval_status !== 'approved')
                                <button type="button" class="btn btn-info flex-fill" onclick="completeRevision()">
                                    <i class="bi bi-check-circle me-2"></i> تمت المراجعة
                                </button>
                            @endif
                        @endpermission
                        @permission('approve_invoice_employees')
                            @if($invoice->approval_status !== 'approved')
                                <button type="button" class="btn btn-success flex-fill" onclick="approveInvoice()">
                                    <i class="bi bi-check-circle me-2"></i>اعتماد
                                </button>
                            @endif
                        @endpermission
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">إجمالي الموظفين</h6>
                            <h3 class="mb-0 text-primary">{{ $summary['total_employees'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="bi bi-people fs-2 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">مدفوع بالكامل</h6>
                            <h3 class="mb-0 text-success">{{ $summary['paid_employees'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-check-circle fs-2 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">مدفوع جزئياً</h6>
                            <h3 class="mb-0 text-warning">{{ $summary['partially_paid_employees'] }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-hourglass-split fs-2 text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">غير مدفوع</h6>
                            <h3 class="mb-0 text-danger">{{ $summary['unpaid_employees'] }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-x-circle fs-2 text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-4">
                    <h6 class="text-muted mb-2">إجمالي الرواتب</h6>
                    <h4 class="mb-0 text-primary">{{ number_format($summary['total_salaries'], 0) }} ريال</h4>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-2">المبلغ المدفوع</h6>
                    <h4 class="mb-0 text-success">{{ number_format($summary['total_paid'], 0) }} ريال</h4>
                </div>
                <div class="col-md-4">
                    <h6 class="text-muted mb-2">المبلغ المتبقي</h6>
                    <h4 class="mb-0 text-danger">{{ number_format($summary['total_remaining'], 0) }} ريال</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Invoice Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('salary-invoices.employees.index', $invoice->id) }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">اختر فاتورة</label>
                        <select class="form-select" name="invoice_id" onchange="if(this.value) window.location.href='/salary-invoices/'+this.value+'/employees'">
                            @foreach($allSalaryInvoices as $inv)
                                <option value="{{ $inv->id }}" {{ $inv->id == $invoice->id ? 'selected' : '' }}>
                                    فاتورة #{{ $inv->number }} - {{ $inv->generation_date }} - {{ $inv->client->name ?? 'بدون عميل' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">بحث</label>
                        <input type="text"
                               class="form-control"
                               name="search"
                               value="{{ $search }}"
                               placeholder="ابحث بالاسم، المشروع، أو ID">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="bi bi-search me-2"></i>بحث
                            </button>
                            <a href="{{ route('salary-invoices.employees.index', $invoice->id) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="btn-group" role="group">
                        <a href="{{ route('salary-invoices.employees.index', ['invoice' => $invoice->id, 'filter' => 'all', 'search' => $search]) }}"
                           class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                            الكل ({{ $summary['total_employees'] }})
                        </a>
                        <a href="{{ route('salary-invoices.employees.index', ['invoice' => $invoice->id, 'filter' => 'unpaid', 'search' => $search]) }}"
                           class="btn btn-sm {{ $filter === 'unpaid' ? 'btn-danger' : 'btn-outline-danger' }}">
                            غير مدفوع ({{ $summary['unpaid_employees'] }})
                        </a>
                        <a href="{{ route('salary-invoices.employees.index', ['invoice' => $invoice->id, 'filter' => 'partially_paid', 'search' => $search]) }}"
                           class="btn btn-sm {{ $filter === 'partially_paid' ? 'btn-warning' : 'btn-outline-warning' }}">
                            مدفوع جزئياً ({{ $summary['partially_paid_employees'] }})
                        </a>
                        <a href="{{ route('salary-invoices.employees.index', ['invoice' => $invoice->id, 'filter' => 'paid', 'search' => $search]) }}"
                           class="btn btn-sm {{ $filter === 'paid' ? 'btn-success' : 'btn-outline-success' }}">
                            مدفوع ({{ $summary['paid_employees'] }})
                        </a>
                        <a href="{{ route('salary-invoices.employees.index', ['invoice' => $invoice->id, 'filter' => 'wps', 'search' => $search]) }}"
                           class="btn btn-sm {{ $filter === 'wps' ? 'btn-info' : 'btn-outline-info' }}">
                            WPS ({{ $summary['wps_employees'] }})
                        </a>
                        <a href="{{ route('salary-invoices.employees.index', ['invoice' => $invoice->id, 'filter' => 'monthly', 'search' => $search]) }}"
                           class="btn btn-sm {{ $filter === 'monthly' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                            شهري ({{ $summary['monthly_employees'] }})
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    @if($invoice->approval_status === 'approved')
                        <button type="button"
                                class="btn btn-success"
                                id="processBatchBtn"
                                data-bs-toggle="modal"
                                data-bs-target="#batchPaymentModal"
                                disabled>
                            <i class="bi bi-cash-coin me-2"></i>معالجة الدفعات (<span id="selectedCount">0</span>)
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card">
        <div class="card-body">
            @if($employees->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
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
                                <th>الخصومات</th>
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
                                                   {{ $employee->payment_status === 'paid' ? 'disabled' : '' }}>
                                        </td>
                                    @endif
                                    <td>{{ $employee->id }}</td>
                                    <td class="fw-bold">{{ $employee->employee_name }}</td>
                                    <td>{{ $employee->project ?? '-' }}</td>
                                    <td class="text-center">{{ $employee->work_days ?? '-' }}</td>
                                    <td class="text-primary fw-bold">{{ number_format($employee->basic_salary ?? 0, 0) }} ر.س</td>
                                    <td class="text-warning fw-bold">{{ number_format(($employee->monthly_deductions ?? 0) + ($employee->advance_deductions ?? 0), 0) }} ر.س</td>
                                    <td class="text-success fw-bold">{{ number_format($employee->net_salary ?? $employee->total_salary ?? 0, 0) }} ر.س</td>
                                    <td class="text-info fw-bold">{{ number_format($employee->total_paid ?? 0, 0) }} ر.س</td>
                                    <td class="text-danger fw-bold">{{ number_format($employee->remaining_amount ?? $employee->net_salary, 0) }} ر.س</td>
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
                Object.values(data.errors).forEach(errors => {
                    errorMessage += errors.join('\n') + '\n';
                });
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
            name: checkbox.getAttribute('data-employee-name'),
            totalSalary: parseFloat(checkbox.getAttribute('data-total-salary')),
            totalPaid: parseFloat(checkbox.getAttribute('data-total-paid')),
            remaining: parseFloat(checkbox.getAttribute('data-remaining')),
            salaryType: checkbox.getAttribute('data-salary-type')
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
        const maxWpsForRemaining = Math.min(maxWpsAmount, emp.remaining);

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
                const maxWpsForRemaining = Math.min(maxWpsAmount, emp.remaining);

                if (amount > maxWpsForRemaining) {
                    this.classList.add('is-invalid');
                    errorDiv.textContent = `يتجاوز حد WPS (${formatNumber(maxWpsForRemaining)} ريال)`;
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
    }
}

// Complete Revision
async function completeRevision() {
    const result = await Swal.fire({
        title: 'تأكيد إتمام المراجعة',
        text: 'هل تم الانتهاء من مراجعة الفاتورة؟',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'نعم، المراجعة تمت',
        cancelButtonText: 'إلغاء',
        confirmButtonColor: '#0dcaf0'
    });

    if (result.isConfirmed) {
        try {
            const response = await fetch('{{ route("salary-invoices.complete-revision", $invoice->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'تمت المراجعة',
                    text: data.message,
                    confirmButtonColor: '#198754'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'فشل إتمام المراجعة',
                    text: data.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ في الاتصال',
                text: 'حدث خطأ أثناء إتمام المراجعة',
                confirmButtonColor: '#dc3545'
            });
        }
    }
}

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
@endpush
@endsection
