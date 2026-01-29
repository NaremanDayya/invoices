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
    <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2" id="addEmployee">
        <i class="bi bi-person-plus-fill"></i>
        <span>إضافة موظف</span>
    </button>
@endsection

@section('content')
    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['total'] ?? 15 }}</h3>
                <p>إجمالي الموظفين</p>
            </div>
            <div class="stat-icon-box" style="background: #e6fffa; color: #319795;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['wage_protection'] ?? 8 }}</h3>
                <p>حماية أجور</p>
            </div>
            <div class="stat-icon-box" style="background: #ebf8ff; color: #3182ce;">
                <i class="bi bi-shield-check"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['monthly_salary'] ?? 7 }}</h3>
                <p>رواتب شهرية</p>
            </div>
            <div class="stat-icon-box" style="background: #fffaf0; color: #dd6b20;">
                <i class="bi bi-calendar-check"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3 class="text-danger">{{ $stats['inactive'] ?? 0 }}</h3>
                <p>غير نشطين</p>
            </div>
            <div class="stat-icon-box" style="background: #fff5f5; color: #e53e3e;">
                <i class="bi bi-person-x-fill"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-100 p-3 mb-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <div class="search-box ms-0" style="width: 300px; background: #fcfcfc; border: 1px solid #f0f0f0;">
                <i class="bi bi-search text-muted"></i>
                <input type="text" placeholder="بحث باسم الموظف أو رقم الملف..." id="searchInput" style="font-size: 0.85rem;">
            </div>
            <select class="form-select border-0 bg-light rounded-xl" id="fileTypeFilter" style="width: 150px; font-size: 0.85rem;">
                <option value="">كل الأنواع</option>
                <option>حماية أجور</option>
                <option>رواتب شهرية</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            @include('components.export-dropdown')
        </div>
    </div>

    <!-- Employees Table -->
    <div class="table-card" id="employees-table-container">
        <div class="table-responsive">
            <table class="custom-table" id="employees-table">
                <thead>
                    <tr>
                        <th>رقم الملف</th>
                        <th>اسم الموظف</th>
                        <th>رقم الهاتف</th>
                        <th>الراتب</th>
                        <th>الخصومات</th>
                        <th>صافي الراتب</th>
                        <th class="text-center">أيام العمل</th>
                        <th class="text-center">الغياب</th>
                        <th class="text-center">الحساب البنكي</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $items = $employees->isEmpty() ? collect([]) : $employees;
                        if($items->isEmpty()){
                            for($i=1; $i<=7; $i++){
                                $names = ['محمد أحمد الشمري', 'فهد عبدالله العتيبي', 'سعود محمد القحطاني', 'خالد إبراهيم الغامدي', 'عبدالرحمن سعد الحربي', 'أحمد فيصل الدوسري', 'يوسف عمر السبيعي'];
                                $items->push((object)[
                                    'id' => $i,
                                    'invoice_number' => 'EMP-000'.($i),
                                    'name' => $names[$i-1] ?? 'موظف جديد',
                                    'phone_number' => '+96650000001'.($i-1),
                                    'monthly_salary' => 7000 + ($i*100),
                                    'total_deductions' => -330,
                                    'net_salary' => 6670 + ($i*100),
                                    'work_days' => 20 + rand(1, 5),
                                    'absences' => rand(1, 4),
                                    'iban' => 'SA820097942663253'.($i),
                                ]);
                            }
                        }
                    @endphp
                    @foreach($items as $employee)
                    <tr>
                        <td><span class="emp-id">{{ $employee->invoice_number }}</span></td>
                        <td>
                            <div class="employee-info-cell">
                                <div class="avatar-circle">
                                    {{ mb_substr($employee->name, 0, 1) }}
                                </div>
                                <span class="fw-bold">{{ $employee->name }}</span>
                            </div>
                        </td>
                        <td class="text-muted" dir="ltr">{{ $employee->phone_number }} <i class="bi bi-telephone ms-1"></i></td>
                        <td>{{ number_format($employee->monthly_salary, 0) }} ر.س</td>
                        <td class="text-danger">{{ number_format($employee->total_deductions ?? 0, 0) }} ر.س</td>
                        <td><span class="net-salary">{{ number_format($employee->net_salary ?? $employee->monthly_salary, 0) }} ر.س</span></td>
                        <td class="text-center">
                            <span class="indicator-circle indicator-green">{{ $employee->work_days ?? 21 }}</span>
                        </td>
                        <td class="text-center">
                            <span class="indicator-circle indicator-red">{{ $employee->absences ?? 0 }}</span>
                        </td>
                        <td class="text-center">
                            <i class="bi bi-hdd-network text-muted" title="{{ $employee->iban }}" style="cursor: pointer;"></i>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn-action edit-employee" data-id="{{ $employee->id }}"><i class="bi bi-pencil"></i></button>
                                <button class="btn-action text-danger delete-employee" data-id="{{ $employee->id }}"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
                        <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @php
                                        $fileTypeColors = [
                                            'حماية أجور' => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'shield-alt'],
                                            'رواتب شهرية' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'calendar']
                                        ];
                                        $type = $fileTypeColors[$employee->file_type] ?? $fileTypeColors['رواتب شهرية'];
                                    @endphp
                                    <span class="badge rounded-pill" style="background: {{ $type['bg'] }}; color: {{ $type['color'] }};">
                                        <i class="fas fa-{{ $type['icon'] }} me-1"></i>
                                        {{ $employee->file_type }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $employee->client->name ?? 'غير معروف' }}</div>
                                </td>
                                <td>{{ $employee->invoice_number }}</td>
                                <td>
                                    <div class="fw-bold">{{ $employee->name }}</div>
                                </td>
                                <td>{{ $employee->phone_number }}</td>
                                <td>
                                    <span class="font-mono text-sm">{{ $employee->iban }}</span>
                                </td>
                                <td>{{ $employee->bank_name }}</td>
                                <td>{{ $employee->account_holder_name }}</td>
                                <td>{{ number_format($employee->monthly_salary, 2) }} ﷼</td>
                                <td>{{ number_format($employee->wage_salary, 2) }} ﷼</td>
                                <td>
                                    <strong style="color: var(--primary);">
                                        {{ number_format($employee->monthly_salary + $employee->wage_salary, 2) }} ﷼
                                    </strong>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'check-circle'],
                                            'inactive' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'times-circle']
                                        ];
                                        $status = $employee->is_active ? $statusColors['active'] : $statusColors['inactive'];
                                    @endphp
                                    <span class="badge rounded-pill" style="background: {{ $status['bg'] }}; color: {{ $status['color'] }};">
                                        <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                                        {{ $employee->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm edit-employee"
                                                data-id="{{ $employee->id }}"
                                                style="background: var(--primary); color: white;"
                                                title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-employee"
                                                data-id="{{ $employee->id }}"
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
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
                                    @foreach($clients as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="invoice_id" class="form-label required-field">الفاتورة</label> <!-- تغيير الاسم -->
                                <select class="form-select" id="invoice_id" name="invoice_id" required> <!-- تغيير الاسم -->
                                    <option value="">اختر الفاتورة</option>
                                    @foreach($invoices as $id => $number)
                                        <option value="{{ $id }}">{{ $number }}</option> <!-- استخدام الـ ID -->
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
    @include('components.export-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupExportDropdown('exportDropdown', 'employees-table-container', 'employees-table', 'تقرير_الموظفين');
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                width: '100%',
                placeholder: 'اختر من القائمة',
                allowClear: true
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
                        $('#wage_salary').val(workDaysSalary.toFixed(2));
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

                $('#total_salary').val(totalSalary.toFixed(2));
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
                    message.text(`راتب حماية الأجور (${wageSalary.toFixed(2)}) يتجاوز 50% من إجمالي الراتب (الحد الأقصى: ${maxWageSalary.toFixed(2)})`);
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
