@extends('layouts.master')

@section('title', 'إدارة الموظفين')
@section('page_title', 'الموظفين')
@section('page_subtitle', 'إدارة بيانات الموظفين والرواتب')

@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-mini-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #edf2f7;
            transition: all 0.3s;
        }
        .stat-mini-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            color: #1a202c;
        }
        .stat-info p {
            font-size: 0.85rem;
            color: #718096;
            margin: 0;
            font-weight: 500;
        }

        /* Table Styling */
        .table-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #edf2f7;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .custom-table {
            width: 100%;
            margin-bottom: 0;
        }
        .custom-table th {
            background: #f8fafc;
            padding: 18px 15px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #4a5568;
            border-bottom: 1px solid #edf2f7;
            text-align: right;
        }
        .custom-table td {
            padding: 18px 15px;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #2d3748;
            border-bottom: 1px solid #f7fafc;
        }
        .emp-id {
            color: #10a37f;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
        }
        .employee-info-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #e6fffa;
            color: #319795;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .net-salary {
            font-weight: 700;
            text-decoration: underline;
            text-decoration-color: #10a37f;
            text-underline-offset: 4px;
        }
        .indicator-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8rem;
        }
        .indicator-green { background: #e6fffa; color: #319795; }
        .indicator-red { background: #fee2e2; color: #9b1c1c; }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            transition: all 0.2s;
            border: 1px solid #edf2f7;
        }
        .btn-action:hover {
            background: var(--primary-accent);
            color: #1e4a46;
            border-color: var(--primary-accent);
        }
    </style>
@endpush

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                onclick="exportEmployeesToPDF()">
            <i class="bi bi-file-pdf"></i>
            <span>تصدير PDF</span>
        </button>
        <button class="btn btn-outline-primary rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                onclick="exportEmployeesToExcel()">
            <i class="bi bi-file-earmark-excel"></i>
            <span>تصدير Excel</span>
        </button>
        <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2" id="addEmployee">
            <i class="bi bi-person-plus-fill"></i>
            <span>إضافة موظف</span>
        </button>
    </div>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['total'] }}</h3>
                <p>إجمالي الموظفين</p>
            </div>
            <div class="stat-icon-box" style="background: #e6fffa; color: #319795;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3 class="text-success">{{ $stats['paid'] }}</h3>
                <p>مدفوع</p>
            </div>
            <div class="stat-icon-box" style="background: #d1fae5; color: #065f46;">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3 class="text-warning">{{ $stats['partially_paid'] }}</h3>
                <p>مدفوع جزئياً</p>
            </div>
            <div class="stat-icon-box" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3 class="text-danger">{{ $stats['unpaid'] }}</h3>
                <p>غير مدفوع</p>
            </div>
            <div class="stat-icon-box" style="background: #fee2e2; color: #991b1b;">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
    </div>

    <!-- Search and Invoice Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('employees.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">تصفية حسب العميل</label>
                        <select class="form-select no-select2" name="client_id" id="clientFilter">
                            <option value="">كل العملاء</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ (string)($clientId ?? '') === (string)$client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">اختر فاتورة</label>
                        <select class="form-select no-select2" name="invoice_id" id="invoiceFilter">
                            <option value="">كل الفواتير</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" {{ request('invoice_id') == $inv->id ? 'selected' : '' }}>
                                    فاتورة #{{ $inv->number }} — {{ optional($inv->generation_date)->format('Y-m-d') }} — {{ $inv->client->name ?? 'بدون عميل' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">بحث</label>
                        <input type="text"
                               class="form-control"
                               name="search"
                               value="{{ $search ?? '' }}"
                               placeholder="ابحث بالاسم، المشروع، أو رقم الفاتورة">
                    </div>
                    <div class="col-md-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-2"></i>بحث
                            </button>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="{{ route('employees.index', ['filter' => 'all', 'search' => $search ?? '', 'invoice_id' => request('invoice_id')]) }}"
                   class="btn btn-sm {{ ($filter ?? 'all') === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                    الكل ({{ $stats['total'] }})
                </a>
                <a href="{{ route('employees.index', ['filter' => 'unpaid', 'search' => $search ?? '', 'invoice_id' => request('invoice_id')]) }}"
                   class="btn btn-sm {{ ($filter ?? 'all') === 'unpaid' ? 'btn-danger' : 'btn-outline-danger' }}">
                    غير مدفوع ({{ $stats['unpaid'] }})
                </a>
                <a href="{{ route('employees.index', ['filter' => 'partially_paid', 'search' => $search ?? '', 'invoice_id' => request('invoice_id')]) }}"
                   class="btn btn-sm {{ ($filter ?? 'all') === 'partially_paid' ? 'btn-warning' : 'btn-outline-warning' }}">
                    مدفوع جزئياً ({{ $stats['partially_paid'] }})
                </a>
                <a href="{{ route('employees.index', ['filter' => 'paid', 'search' => $search ?? '', 'invoice_id' => request('invoice_id')]) }}"
                   class="btn btn-sm {{ ($filter ?? 'all') === 'paid' ? 'btn-success' : 'btn-outline-success' }}">
                    مدفوع ({{ $stats['paid'] }})
                </a>
                <a href="{{ route('employees.index', ['filter' => 'wps', 'search' => $search ?? '', 'invoice_id' => request('invoice_id')]) }}"
                   class="btn btn-sm {{ ($filter ?? 'all') === 'wps' ? 'btn-info' : 'btn-outline-info' }}">
                    WPS ({{ $stats['wps'] }})
                </a>
                <a href="{{ route('employees.index', ['filter' => 'monthly', 'search' => $search ?? '', 'invoice_id' => request('invoice_id')]) }}"
                   class="btn btn-sm {{ ($filter ?? 'all') === 'monthly' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                    شهري ({{ $stats['monthly'] }})
                </a>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="table-card" id="employees-table-container">
        <div class="table-responsive">
            <table class="custom-table" id="employees-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>اسم الموظف</th>
                        <th>المشروع</th>
                        <th>إجمالي الراتب</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>نوع الراتب</th>
                        <th>حالة الدفع</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @if($employees->count() > 0)
                        @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->id }}</td>
                            <td>
                                <a href="{{ route('invoices.show', $employee->invoice_id) }}" class="text-primary fw-bold">
                                    #{{ $employee->invoice->number ?? '-' }}
                                </a>
                            </td>
                            <td>{{ $employee->invoice->client->name ?? '-' }}</td>
                            <td>
                                <div class="employee-info-cell">
                                    <div class="avatar-circle">
                                        {{ mb_substr($employee->employee_name, 0, 1) }}
                                    </div>
                                    <span class="fw-bold">{{ $employee->employee_name }}</span>
                                </div>
                            </td>
                            <td>{{ $employee->project ?? '-' }}</td>
                            <td class="text-primary fw-bold">{{ number_format($employee->total_salary ?? $employee->net_salary, 0) }} ر.س</td>
                            <td class="text-success fw-bold">{{ number_format($employee->total_paid ?? 0, 0) }} ر.س</td>
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
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('salary-invoices.employees.payments', [$employee->invoice_id, $employee->id]) }}"
                                       class="btn btn-sm btn-info"
                                       title="سجل دفعات الموظف">
                                        <i class="bi bi-clock-history"></i>
                                    </a>
                                    <a href="{{ route('salary-invoices.employees.index', $employee->invoice_id) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="عرض الفاتورة">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="bi bi-inbox fs-1 text-muted"></i>
                                <p class="text-muted mt-3">لا يوجد موظفين</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


    <!-- Add/Edit Employee Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">إضافة موظف جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="employeeForm">
                    @csrf
                    <input type="hidden" id="employeeId" name="id">

                    <div class="modal-body">
                        <!-- File Type Selection -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label required-field">نوع الملف</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="file_type" id="monthlySalary" value="رواتب شهرية" checked>
                                        <label class="form-check-label fw-bold" for="monthlySalary">
                                            <i class="fas fa-calendar me-2"></i>
                                            رواتب شهرية
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="file_type" id="wageProtection" value="حماية أجور">
                                        <label class="form-check-label fw-bold" for="wageProtection">
                                            <i class="fas fa-shield-alt me-2"></i>
                                            حماية أجور
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="client_id" class="form-label required-field">اسم العميل</label>
                                <select class="form-select" id="client_id" name="client_id" required>
                                    <option value="">اختر العميل</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_id" class="form-label required-field">الفاتورة</label> <!-- تغيير الاسم -->
                                <select class="form-select" id="invoice_id" name="invoice_id" required> <!-- تغيير الاسم -->
                                    <option value="">اختر الفاتورة</option>
                                    @foreach($invoices as $inv)
                                        <option value="{{ $inv->id }}">فاتورة #{{ $inv->number }} — {{ optional($inv->generation_date)->format('Y-m-d') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Employee Personal Information -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="name" class="form-label required-field">اسم الموظف</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-4">
                                <label for="phone_number" class="form-label required-field">رقم الهاتف</label>
                                <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                            </div>
                            <div class="col-md-4">
                                <label for="iban" class="form-label required-field">رقم الآيبان</label>
                                <input type="text" class="form-control" id="iban" name="iban" required>
                            </div>
                        </div>

                        <!-- Bank Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bank_name" class="form-label required-field">اسم البنك</label>
                                <input type="text" class="form-control" id="bank_name" name="bank_name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="account_holder_name" class="form-label required-field">صاحب الحساب</label>
                                <input type="text" class="form-control" id="account_holder_name" name="account_holder_name" required>
                            </div>
                        </div>

                        <!-- Salary Information -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="monthly_salary" class="form-label required-field">الراتب الشهري</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="monthly_salary" name="monthly_salary" required>
                                    <span class="input-group-text">﷼</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="wage_salary" class="form-label required-field">راتب حماية الأجور</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="wage_salary" name="wage_salary" required>
                                    <span class="input-group-text">﷼</span>
                                </div>
                                <small class="form-text text-muted mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    يجب ألا يتجاوز 50% من إجمالي الراتب
                                </small>
                            </div>
                            <div class="col-md-4">
                                <label for="total_salary" class="form-label">إجمالي الراتب</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control bg-light" id="total_salary" readonly style="font-weight: bold; color: var(--primary);">
                                    <span class="input-group-text">﷼</span>
                                </div>
                            </div>
                        </div>

                        <!-- Wage Protection Fields -->
                        <div class="row mb-3" id="wageProtectionFields">
                            <div class="col-md-6">
                                <label for="work_days" class="form-label required-field">أيام العمل</label>
                                <input type="number" class="form-control" id="work_days" name="work_days" min="1" max="31" placeholder="عدد أيام العمل" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الحالة</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <label class="form-check-label" for="is_active">موظف نشط</label>
                                </div>
                            </div>
                        </div>

                        <!-- Validation Alert -->
                        <div class="alert alert-warning alert-dismissible fade show" id="validationAlert" style="display: none;">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <span id="alertMessage"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn" style="background: var(--primary);">
                            <i class="fas fa-save me-2"></i>
                            حفظ الموظف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet">
    <style>
        .required-field::after {
            content: " *";
            color: red;
        }
        .table > :not(caption) > * > * {
            padding: 0.75rem 0.5rem;
        }
        .badge {
            font-size: 0.75rem;
            padding: 0.35em 0.65em;
        }
        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        .modal-header {
            border-bottom: 1px solid #e9ecef;
            background: var(--light);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.18.5/package/dist/xlsx.full.min.js"></script>
    <script>
        function exportEmployeesToPDF() {
            if (typeof html2pdf === 'undefined') {
                alert('جاري تحميل مكتبة PDF، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const companyLogoSrc = '{{ asset("assets/img/logo.png") }}';
            const today = new Date().toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
            const todayShort = new Date().toISOString().split('T')[0];

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

            const stats = {
                total:   {{ $stats['total'] }},
                paid:    {{ $stats['paid'] }},
                partial: {{ $stats['partially_paid'] }},
                unpaid:  {{ $stats['unpaid'] }},
                wps:     {{ $stats['wps'] }},
                monthly: {{ $stats['monthly'] }}
            };

            let tableRows = '';
            document.querySelectorAll('#employees-table tbody tr').forEach((row, i) => {
                const cells = row.querySelectorAll('td');
                if (!cells.length || cells.length < 10) return;

                const bg = i % 2 === 0 ? '#ffffff' : '#f8fafc';
                const empId      = cells[0]?.innerText.trim() || '-';
                const invNumber  = cells[1]?.innerText.trim() || '-';
                const clientName = cells[2]?.innerText.trim() || '-';
                const empName    = cells[3]?.querySelector('span.fw-bold')?.innerText.trim() || cells[3]?.innerText.trim() || '-';
                const project    = cells[4]?.innerText.trim() || '-';
                const totalSal   = cells[5]?.innerText.trim() || '-';
                const paid       = cells[6]?.innerText.trim() || '-';
                const remaining  = cells[7]?.innerText.trim() || '-';
                const salType    = cells[8]?.innerText.trim() || '-';
                const payStatus  = cells[9]?.innerText.trim() || '-';

                let statusBg = '#e2e8f0', statusColor = '#334155';
                if (payStatus.includes('مدفوع') && !payStatus.includes('جزئ')) { statusBg='#d1fae5'; statusColor='#065f46'; }
                else if (payStatus.includes('جزئ')) { statusBg='#fed7aa'; statusColor='#92400e'; }
                else if (payStatus.includes('غير')) { statusBg='#fee2e2'; statusColor='#991b1b'; }

                const typeBg    = salType.includes('WPS') ? '#cffafe' : '#e2e8f0';
                const typeColor = salType.includes('WPS') ? '#0e7490' : '#334155';

                const td = 'padding:7px 9px;border-bottom:1px solid #e2e8f0;font-size:11px;vertical-align:middle;';
                tableRows += `
                <tr style="background:${bg};">
                    <td style="${td}text-align:center;color:#64748b;">${empId}</td>
                    <td style="${td}text-align:center;color:#10a37f;font-weight:700;">${invNumber}</td>
                    <td style="${td}color:#475569;">${clientName}</td>
                    <td style="${td}font-weight:600;color:#1e293b;">${empName}</td>
                    <td style="${td}color:#475569;">${project}</td>
                    <td style="${td}text-align:right;color:#2563eb;font-weight:600;">${totalSal}</td>
                    <td style="${td}text-align:right;color:#059669;font-weight:600;">${paid}</td>
                    <td style="${td}text-align:right;color:#dc2626;font-weight:600;">${remaining}</td>
                    <td style="${td}text-align:center;"><span style="background:${typeBg};color:${typeColor};padding:2px 8px;border-radius:10px;font-size:10px;">${salType}</span></td>
                    <td style="${td}text-align:center;"><span style="background:${statusBg};color:${statusColor};padding:2px 8px;border-radius:10px;font-size:10px;">${payStatus}</span></td>
                </tr>`;
            });

            const html = `<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<title>تقرير الموظفين</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Tahoma','Arial',sans-serif; direction:rtl; background:#fff; color:#1e293b; font-size:12px; padding:16px; }
.pdf-header { background:linear-gradient(135deg,#1e4a46,#2d6a65); color:white; padding:18px 24px; border-radius:12px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; }
.stats-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:10px; margin-bottom:14px; }
.stat-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px; text-align:center; }
.stat-box .sl { font-size:10px; color:#64748b; margin-bottom:4px; }
.stat-box .sv { font-size:16px; font-weight:700; }
table { width:100%; border-collapse:collapse; font-size:11px; }
thead th { background:#1e4a46; color:#fff; padding:9px 8px; font-weight:600; white-space:nowrap; text-align:center; }
tbody td { padding:7px 8px; border-bottom:1px solid #e2e8f0; vertical-align:middle; }
.pdf-footer { margin-top:16px; padding:12px 20px; background:#f8fafc; border-radius:8px; display:flex; justify-content:space-between; align-items:center; color:#64748b; font-size:10px; }
</style>
</head>
<body>
<div class="pdf-header">
  <div style="text-align:right;">
    <div style="font-size:20px;font-weight:700;margin-bottom:6px;">تقرير الموظفين</div>
    <div style="font-size:12px;opacity:0.85;">نظام إدارة الفواتير — ${today}</div>
  </div>
  ${companyLogo ? `<img src="${companyLogo}" style="height:42px;">` : ''}
</div>

<div class="stats-grid">
  <div class="stat-box"><div class="sl">إجمالي الموظفين</div><div class="sv" style="color:#0284c7;">${stats.total}</div></div>
  <div class="stat-box"><div class="sl">مدفوع</div><div class="sv" style="color:#059669;">${stats.paid}</div></div>
  <div class="stat-box"><div class="sl">مدفوع جزئياً</div><div class="sv" style="color:#d97706;">${stats.partial}</div></div>
  <div class="stat-box"><div class="sl">غير مدفوع</div><div class="sv" style="color:#dc2626;">${stats.unpaid}</div></div>
  <div class="stat-box"><div class="sl">WPS</div><div class="sv" style="color:#0891b2;">${stats.wps}</div></div>
  <div class="stat-box"><div class="sl">شهري</div><div class="sv" style="color:#4b5563;">${stats.monthly}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>رقم الفاتورة</th>
      <th>العميل</th>
      <th>اسم الموظف</th>
      <th>المشروع</th>
      <th>إجمالي الراتب</th>
      <th>المدفوع</th>
      <th>المتبقي</th>
      <th>نوع الراتب</th>
      <th>حالة الدفع</th>
    </tr>
  </thead>
  <tbody>${tableRows}</tbody>
</table>

<div class="pdf-footer">
  <span style="font-weight:700;color:#1e4a46;">نظام إدارة الفواتير</span>
  <span>تقرير الموظفين — تاريخ التصدير: ${today}</span>
</div>
</body>
</html>`;

            const container = document.createElement('div');
            container.innerHTML = html;
            document.body.appendChild(container);

            html2pdf().set({
                margin: [8, 8, 8, 8],
                filename: `تقرير_الموظفين_${todayShort}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
            }).from(container).save().then(() => {
                document.body.removeChild(container);
                if (window.toastr) toastr.success('تم تصدير الموظفين إلى PDF بنجاح');
            });
            }); // end getWhiteLogoDataUrl
        }

        function exportEmployeesToExcel() {
            if (typeof XLSX === 'undefined') {
                alert('جاري تحميل مكتبة Excel، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const todayShort = new Date().toISOString().split('T')[0];

            const originalTable = document.getElementById('employees-table');
            const clonedTable = originalTable.cloneNode(true);

            // Remove last header (actions)
            clonedTable.querySelectorAll('thead tr').forEach(row => {
                const cells = row.querySelectorAll('th');
                if (cells.length) cells[cells.length - 1].remove();
            });

            // Clean body rows
            clonedTable.querySelectorAll('tbody tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (!cells.length) return;

                // Flatten employee name cell (strip avatar)
                const nameSpan = cells[3]?.querySelector('span.fw-bold');
                if (nameSpan) cells[3].innerHTML = nameSpan.innerText.trim();

                // Remove last cell (actions)
                cells[cells.length - 1].remove();
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(clonedTable);

            // Auto-size columns
            const range = XLSX.utils.decode_range(ws['!ref']);
            const colWidths = [];
            for (let C = range.s.c; C <= range.e.c; C++) {
                let maxLen = 10;
                for (let R = range.s.r; R <= range.e.r; R++) {
                    const cell = ws[XLSX.utils.encode_cell({ r: R, c: C })];
                    if (cell && cell.v) maxLen = Math.max(maxLen, String(cell.v).length + 4);
                }
                colWidths.push({ wch: Math.min(maxLen, 40) });
            }
            ws['!cols'] = colWidths;

            XLSX.utils.book_append_sheet(wb, ws, 'الموظفين');
            XLSX.writeFile(wb, `تقرير_الموظفين_${todayShort}.xlsx`);

            if (window.toastr) toastr.success('تم تصدير الموظفين إلى Excel بنجاح');
        }
    </script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 on filter dropdowns with RTL + bootstrap theme
            $('#clientFilter').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                width: '100%',
                placeholder: 'كل العملاء',
                allowClear: true,
                language: { noResults: function() { return 'لا توجد نتائج'; } }
            });

            $('#invoiceFilter').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                width: '100%',
                placeholder: 'كل الفواتير',
                allowClear: true,
                language: { noResults: function() { return 'لا توجد فواتير'; } }
            });

            // When client changes — reload invoice dropdown then submit
            $('#clientFilter').on('change', function() {
                const clientId = $(this).val();
                const $inv = $('#invoiceFilter');

                $inv.empty().append('<option value="">كل الفواتير</option>');
                $inv.trigger('change.select2');

                if (!clientId) {
                    $('#filterForm').submit();
                    return;
                }

                $.getJSON('{{ route("invoices.by-client", ["client" => "__ID__"]) }}'.replace('__ID__', clientId), function(invoices) {
                    invoices.forEach(function(inv) {
                        $inv.append(new Option(inv.text, inv.id, false, false));
                    });
                    $inv.trigger('change.select2');
                }).always(function() {
                    $('#filterForm').submit();
                });
            });


            // Open Modal
            $('#addEmployee').on('click', function() {
                $('#employeeModal').modal('show');
                resetForm();
            });

            // File Type Change Handler
            $('input[name="file_type"]').change(function() {
                const fileType = $(this).val();

                // work_days أصبح متاحاً لكلا النوعين
                $('#wageProtectionFields').show();
                $('#work_days').attr('required', 'required');

                // يمكنك إضافة منطق إضافي هنا إذا أردت
                if (fileType === 'حماية أجور') {
                    // منطق إضافي لحماية الأجور إذا needed
                } else {
                    // منطق إضافي للرواتب الشهرية إذا needed
                }
            });

// Calculate total salary automatically
            $('#monthly_salary, #wage_salary, #work_days').on('input', function() {
                calculateTotalSalary();
                validateWageSalary();
                calculateWorkDaysSalary(); // إضافة دالة جديدة
            });

// دالة جديدة لحساب الراتب بناءً على أيام العمل
            function calculateWorkDaysSalary() {
                const monthlySalary = parseFloat($('#monthly_salary').val()) || 0;
                const workDays = parseInt($('#work_days').val()) || 0;
                const fileType = $('input[name="file_type"]:checked').val();

                if (workDays > 0 && monthlySalary > 0) {
                    const dailyRate = monthlySalary / 30;
                    const workDaysSalary = dailyRate * workDays;

                    // إذا كان نوع الملف "حماية أجور"، يمكننا اقتراح قيمة لراتب الحماية
                    if (fileType === 'حماية أجور' && !$('#wage_salary').val()) {
                        $('#wage_salary').val(workDaysSalary.toFixed(0));
                    }

                    // تحديث الراتب الإجمالي
                    calculateTotalSalary();
                    validateWageSalary();
                }
            }

            function calculateTotalSalary() {
                const monthlySalary = parseFloat($('#monthly_salary').val()) || 0;
                const wageSalary = parseFloat($('#wage_salary').val()) || 0;
                const totalSalary = monthlySalary + wageSalary;

                $('#total_salary').val(totalSalary.toFixed(0));
            }

            function validateWageSalary() {
                const monthlySalary = parseFloat($('#monthly_salary').val()) || 0;
                const wageSalary = parseFloat($('#wage_salary').val()) || 0;
                const totalSalary = monthlySalary + wageSalary;
                const maxWageSalary = totalSalary * 0.5; // 50% من إجمالي الراتب

                const alert = $('#validationAlert');
                const message = $('#alertMessage');
                const saveBtn = $('#saveBtn');

                if (wageSalary > maxWageSalary) {
                    message.text(`راتب حماية الأجور (${wageSalary.toFixed(0)}) يتجاوز 50% من إجمالي الراتب (الحد الأقصى: ${maxWageSalary.toFixed(0)})`);
                    alert.show();
                    saveBtn.prop('disabled', true);
                    $('#wage_salary').addClass('is-invalid');
                } else {
                    alert.hide();
                    saveBtn.prop('disabled', false);
                    $('#wage_salary').removeClass('is-invalid');
                }
            }

// Save employee form (الجزء المعدل فقط)
            $('#employeeForm').on('submit', function(e) {
                e.preventDefault();

                const monthlySalary = parseFloat($('#monthly_salary').val()) || 0;
                const wageSalary = parseFloat($('#wage_salary').val()) || 0;
                const totalSalary = monthlySalary + wageSalary;
                const maxWageSalary = totalSalary * 0.5; // 50% من إجمالي الراتب

                if (wageSalary > maxWageSalary) {
                    alert('لا يمكن حفظ البيانات: راتب حماية الأجور يتجاوز 50% من إجمالي الراتب');
                    return false;
                }

                const formData = new FormData(this);
                const employeeId = $('#employeeId').val();
                const url = employeeId ? '/employees/' + employeeId : '/employees';
                const method = employeeId ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#employeeModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('خطأ في حفظ البيانات: ' + (xhr.responseJSON?.message || 'حدث خطأ غير متوقع'));
                    }
                });
            });
            function resetForm() {
                $('#employeeForm')[0].reset();
                $('#employeeId').val('');
                $('#modalTitle').text('إضافة موظف جديد');
                $('.select2').val(null).trigger('change');
                $('#wageProtectionFields').show(); // إظهار الحقل دائمًا
                $('#total_salary').val('');
                $('#validationAlert').hide();
                $('#saveBtn').prop('disabled', false);
            }

            // Save employee form
            $('#employeeForm').on('submit', function(e) {
                e.preventDefault();

                const monthlySalary = parseFloat($('#monthly_salary').val()) || 0;
                const wageSalary = parseFloat($('#wage_salary').val()) || 0;
                const maxWageSalary = monthlySalary * 0.5;

                if (wageSalary > maxWageSalary) {
                    alert('لا يمكن حفظ البيانات: راتب حماية الأجور يتجاوز 50% من الراتب الشهري');
                    return false;
                }

                const formData = new FormData(this);
                const employeeId = $('#employeeId').val();
                const url = employeeId ? '/employees/' + employeeId : '/employees';
                const method = employeeId ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#employeeModal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('خطأ في حفظ البيانات: ' + (xhr.responseJSON?.message || 'حدث خطأ غير متوقع'));
                    }
                });
            });

            // Edit employee
            $('.edit-employee').on('click', function() {
                const employeeId = $(this).data('id');

                $.get('/employees/' + employeeId, function(response) {
                    const employee = response.employee;

                    $('#employeeId').val(employee.id);
                    $('#modalTitle').text('تعديل بيانات الموظف');

                    // Set file type
                    $(`input[name="file_type"][value="${employee.file_type}"]`).prop('checked', true);

                    // Fill form fields
                    $('#client_id').val(employee.client_id).trigger('change');
                    $('#invoice_id').val(employee.invoice_id).trigger('change'); // تغيير إلى invoice_id
                    $('#name').val(employee.name);
                    $('#phone_number').val(employee.phone_number);
                    $('#iban').val(employee.iban);
                    $('#bank_name').val(employee.bank_name);
                    $('#account_holder_name').val(employee.account_holder_name);
                    $('#monthly_salary').val(employee.monthly_salary);
                    $('#wage_salary').val(employee.wage_salary);
                    $('#work_days').val(employee.work_days); // work_days أصبح متاحاً لكلا النوعين
                    $('#is_active').prop('checked', employee.is_active);

                    // إظهار الحقول دائمًا
                    $('#wageProtectionFields').show();

                    calculateTotalSalary();
                    validateWageSalary();

                    $('#employeeModal').modal('show');
                });
            });

            // Delete employee
            $('.delete-employee').on('click', function() {
                const employeeId = $(this).data('id');

                if (confirm('هل أنت متأكد من حذف هذا الموظف؟')) {
                    $.ajax({
                        url: '/employees/' + employeeId,
                        method: 'DELETE',
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            alert('خطأ في حذف الموظف: ' + (xhr.responseJSON?.message || 'حدث خطأ غير متوقع'));
                        }
                    });
                }
            });

            // Filter functionality
            $('#searchInput').on('keyup', function() {
                const value = $(this).val().toLowerCase();
                $('#employees-table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });

            $('#resetFilters').on('click', function() {
                $('#searchInput').val('');
                $('#fileTypeFilter').val('');
                $('#clientFilter').val('');
                $('#statusFilter').val('');
                $('#employees-table tbody tr').show();
            });
        });
    </script>
@endpush
