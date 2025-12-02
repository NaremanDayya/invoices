@extends('layouts.master')

@section('title', 'إدارة العمالة')

@section('content')
    <div>
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0" style="color: var(--primary);">
                <i class="fas fa-users me-2"></i>
                إدارة العمالة
            </h2>
            <div>
                <button class="btn btn-primary" id="addEmployee" style="background: var(--primary); color: white;">
                    <i class="fas fa-user-plus me-2"></i>
                    إضافة موظف جديد
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">إجمالي الموظفين</h6>
                                <h3 class="mb-0" style="color: var(--primary);">{{ $stats['total'] ?? 0 }}</h3>
                            </div>
                            <div class="bg-light rounded-circle p-3">
                                <i class="fas fa-users" style="color: var(--primary);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #10b981;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">حماية أجور</h6>
                                <h3 class="mb-0" style="color: #10b981;">{{ $stats['wage_protection'] ?? 0 }}</h3>
                            </div>
                            <div class="bg-light rounded-circle p-3">
                                <i class="fas fa-shield-alt" style="color: #10b981;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #f59e0b;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">رواتب شهرية</h6>
                                <h3 class="mb-0" style="color: #f59e0b;">{{ $stats['monthly_salary'] ?? 0 }}</h3>
                            </div>
                            <div class="bg-light rounded-circle p-3">
                                <i class="fas fa-calendar" style="color: #f59e0b;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid #ef4444;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">غير نشطين</h6>
                                <h3 class="mb-0" style="color: #ef4444;">{{ $stats['inactive'] ?? 0 }}</h3>
                            </div>
                            <div class="bg-light rounded-circle p-3">
                                <i class="fas fa-user-slash" style="color: #ef4444;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">بحث سريع</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            class="block w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                            placeholder="ابحث في الموظفين..."
                            id="searchInput"
                        >
                    </div>
                </div>

                <!-- File Type Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الملف</label>
                    <select
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                        id="fileTypeFilter"
                    >
                        <option value="">كل الأنواع</option>
                        <option value="حماية أجور">حماية أجور</option>
                        <option value="رواتب شهرية">رواتب شهرية</option>
                    </select>
                </div>

                <!-- Client Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل</label>
                    <select
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                        id="clientFilter"
                    >
                        <option value="">كل العملاء</option>
                        @foreach($clients as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                        id="statusFilter"
                    >
                        <option value="">كل الحالات</option>
                        <option value="active">نشط</option>
                        <option value="inactive">غير نشط</option>
                    </select>
                </div>

                <!-- Reset Button -->
                <div>
                    <button
                        class="w-full px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 font-medium"
                        id="resetFilters"
                    >
                        <i class="fas fa-refresh text-gray-500"></i>
                        <span>إعادة تعيين</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Employees Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="employees-table">
                        <thead style="background: var(--light);">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">نوع الملف</th>
                            <th class="border-0">العميل</th>
                            <th class="border-0">رقم الفاتورة</th>
                            <th class="border-0">اسم الموظف</th>
                            <th class="border-0">رقم الهاتف</th>
                            <th class="border-0">رقم الآيبان</th>
                            <th class="border-0">اسم البنك</th>
                            <th class="border-0">صاحب الحساب</th>
                            <th class="border-0">الراتب الشهري</th>
                            <th class="border-0">راتب الحماية</th>
                            <th class="border-0">إجمالي الراتب</th>
                            <th class="border-0">الحالة</th>
                            <th class="border-0 text-center">الإجراءات</th>
                        </tr>
                        </thead>
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
