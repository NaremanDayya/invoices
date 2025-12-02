@section('title', 'إدارة العمالة')
@push('styles')
    <style>
        :root {
            --primary: #10b981;
            --light: #f8fafc;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #374151;
        }
        .modal-header .btn-close {
            filter: invert(1);
        }

        .form-section-title {
            border-bottom: 2px solid var(--primary);
            padding-bottom: 0.5rem;
        }

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .border-gray-200 {
            border-color: #e5e7eb;
        }
        .card {
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .table th {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-color: #f3f4f6;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
            padding: 0.5rem 0.75rem;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            border-radius: 0.5rem;
        }

        .text-success { color: #10b981 !important; }
        .text-warning { color: #f59e0b !important; }
        .text-danger { color: #ef4444 !important; }

        .employee-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .pagination .page-link {
            color: var(--primary);
            border: 1px solid #e5e7eb;
        }

        .pagination .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .filter-section {
            background-color: white;
            border-radius: 0.75rem;
            padding: 1.25rem;
        }

        .form-control, .form-select {
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
            padding: 0.5rem 0.75rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.25);
        }

        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .btn-primary:hover {
            background-color: #0da271;
            border-color: #0da271;
        }

        .status-badge {
            padding: 0.5rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .table-responsive {
            border-radius: 0.75rem;
        }

        .table thead {
            background-color: var(--light);
        }

        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.875rem;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
    @endpush
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--primary);">
            <i class="fas fa-users me-2"></i>
            إدارة العمالة
        </h2>
        <button class="btn" style="background: var(--primary); color: white;" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
            <i class="fas fa-plus me-2"></i>
            موظف جديد
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left-color: var(--primary);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">إجمالي الموظفين</h6>
                            <h3 class="mb-0" style="color: var(--primary);" id="totalEmployees">48</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-users" style="color: var(--primary);"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left-color: #10b981;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">رواتب هذا الشهر</h6>
                            <h3 class="mb-0" style="color: #10b981;" id="totalSalaries">245,800 ﷼</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-money-bill-wave" style="color: #10b981;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left-color: #f59e0b;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">متوسط الراتب</h6>
                            <h3 class="mb-0" style="color: #f59e0b;" id="averageSalary">5,120 ﷼</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-chart-line" style="color: #f59e0b;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 stat-card" style="border-left-color: #ef4444;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">أيام الغياب</h6>
                            <h3 class="mb-0" style="color: #ef4444;" id="totalAbsences">12</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-user-clock" style="color: #ef4444;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="بحث بالاسم أو الهاتف...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="clientFilter">
                        <option value="">كل العملاء</option>
                        <option value="1">شركة التقنية</option>
                        <option value="2">مؤسسة النجاح</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="bankFilter">
                        <option value="">كل البنوك</option>
                        <option value="alrajhi">الراجحي</option>
                        <option value="albilad">البلاد</option>
                        <option value="alinhar">الإنماء</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="salaryTypeFilter">
                        <option value="">نوع الراتب</option>
                        <option value="with_safety">بالتأمين</option>
                        <option value="without_safety">بدون تأمين</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" id="salaryFromFilter" placeholder="الراتب من">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100" id="filterBtn">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Employees Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-hover mb-0" id="employeesTable">
                    <thead style="background: var(--light);">
                    <tr>
                        <th class="border-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                            </div>
                        </th>
                        <th class="border-0">نوع الملف</th>
                        <th class="border-0">العميل</th>
                        <th class="border-0">رقم الفاتورة</th>
                        <th class="border-0">اسم الموظف</th>
                        <th class="border-0">رقم الجوال</th>
                        <th class="border-0">الايبان</th>
                        <th class="border-0">البنك</th>
                        <th class="border-0">الراتب</th>
                        <th class="border-0">أيام الغياب</th>
                        <th class="border-0">الخصومات</th>
                        <th class="border-0">الراتب النهائي</th>
                        <th class="border-0">الحالة</th>
                        <th class="border-0 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody id="employeesTableBody">
                    <!-- Employees will be loaded here dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted" id="paginationInfo">
            عرض 1 إلى 5 من 48 موظف
        </div>
        <nav>
            <ul class="pagination mb-0" id="pagination">
                <li class="page-item disabled">
                    <a class="page-link" href="#">السابق</a>
                </li>
                <li class="page-item active">
                    <a class="page-link" href="#">1</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">2</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">3</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">التالي</a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header bg-primary text-white p-4">
                    <h5 class="modal-title text-xl font-bold flex items-center" id="addEmployeeModalLabel">
                        <i class="fas fa-plus-circle ml-2"></i>
                        إضافة موظف جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-6">
                    <form id="employeeForm">
                        <!-- Personal Information Row -->
                        <div class="row mb-4">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 h-100">
                                    <h6 class="form-section-title text-primary font-semibold mb-4 text-lg">
                                        <i class="fas fa-user ml-2"></i>
                                        المعلومات الشخصية
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="employeeName" class="form-label">اسم الموظف *</label>
                                            <input type="text" class="form-control" id="employeeName" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="employeePosition" class="form-label">المسمى الوظيفي *</label>
                                            <input type="text" class="form-control" id="employeePosition" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="employeePhone" class="form-label">رقم الجوال *</label>
                                            <input type="tel" class="form-control" id="employeePhone" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="employeeEmail" class="form-label">البريد الإلكتروني</label>
                                            <input type="email" class="form-control" id="employeeEmail">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="employeeId" class="form-label">رقم الهوية *</label>
                                            <input type="text" class="form-control" id="employeeId" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="employeeNationality" class="form-label">الجنسية</label>
                                            <input type="text" class="form-control" id="employeeNationality" value="سعودي">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Work Information -->
                            <div class="col-md-6">
                                <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 h-100">
                                    <h6 class="form-section-title text-primary font-semibold mb-4 text-lg">
                                        <i class="fas fa-building ml-2"></i>
                                        معلومات العمل
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="clientName" class="form-label">العميل *</label>
                                            <select class="form-select" id="clientName" required>
                                                <option value="">اختر العميل</option>
                                                <option value="tech">شركة التقنية المتطورة</option>
                                                <option value="najah">مؤسسة النجاح للتسويق</option>
                                                <option value="horizon">شركة الأفق الجديد</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="invoiceNumber" class="form-label">رقم الفاتورة *</label>
                                            <select class="form-select" id="invoiceNumber" required>
                                                <option value="">اختر الفاتورة</option>
                                                <option value="INV-2024-001">#INV-2024-001</option>
                                                <option value="INV-2024-002">#INV-2024-002</option>
                                                <option value="INV-2024-003">#INV-2024-003</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="fileType" class="form-label">نوع الملف *</label>
                                            <select class="form-select" id="fileType" required>
                                                <option value="salary">رواتب</option>
                                                <option value="contractor">مقاولين</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="hireDate" class="form-label">تاريخ التعيين</label>
                                            <input type="date" class="form-control" id="hireDate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Information -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 mb-4">
                            <h6 class="form-section-title text-primary font-semibold mb-4 text-lg">
                                <i class="fas fa-university ml-2"></i>
                                معلومات البنك
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="bankName" class="form-label">البنك *</label>
                                    <select class="form-select" id="bankName" required>
                                        <option value="">اختر البنك</option>
                                        <option value="الراجحي">الراجحي</option>
                                        <option value="البلاد">البلاد</option>
                                        <option value="الإنماء">الإنماء</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="ibanNumber" class="form-label">رقم الآيبان *</label>
                                    <input type="text" class="form-control" id="ibanNumber" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="accountNumber" class="form-label">رقم الحساب *</label>
                                    <input type="text" class="form-control" id="accountNumber" required>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Information -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 mb-4">
                            <h6 class="form-section-title text-primary font-semibold mb-4 text-lg">
                                <i class="fas fa-money-bill-wave ml-2"></i>
                                معلومات الراتب
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="basicSalary" class="form-label">الراتب الأساسي (﷼) *</label>
                                    <input type="number" class="form-control" id="basicSalary" min="0" step="0.01" required>
                                </div>
                                <div class="col-md-3">
                                    <label for="salaryType" class="form-label">نوع الراتب *</label>
                                    <select class="form-select" id="salaryType" required>
                                        <option value="with_safety">بالتأمين</option>
                                        <option value="without_safety">بدون تأمين</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="insuranceAmount" class="form-label">قيمة التأمين (﷼)</label>
                                    <input type="number" class="form-control" id="insuranceAmount" min="0" step="0.01" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="grossSalary" class="form-label">الراتب الإجمالي (﷼)</label>
                                    <input type="text" class="form-control bg-light" id="grossSalary" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions and Final Salary -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 mb-4">
                            <h6 class="form-section-title text-primary font-semibold mb-4 text-lg">
                                <i class="fas fa-calculator ml-2"></i>
                                الخصومات والراتب النهائي
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="absenceDays" class="form-label">أيام الغياب</label>
                                    <input type="number" class="form-control" id="absenceDays" min="0" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="absenceDeduction" class="form-label">خصم الغياب (﷼)</label>
                                    <input type="text" class="form-control bg-light" id="absenceDeduction" readonly>
                                </div>
                                <div class="col-md-3">
                                    <label for="otherDeductions" class="form-label">خصومات أخرى (﷼)</label>
                                    <input type="number" class="form-control" id="otherDeductions" min="0" step="0.01" value="0">
                                </div>
                                <div class="col-md-3">
                                    <label for="finalSalary" class="form-label">الراتب النهائي (﷼)</label>
                                    <input type="text" class="form-control bg-light" id="finalSalary" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Status and Notes -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <h6 class="form-section-title text-primary font-semibold mb-4 text-lg">
                                <i class="fas fa-clipboard-check ml-2"></i>
                                الحالة والملاحظات
                            </h6>
                            <div class="row g-3 mb-3">
                                <div class="col-md-4">
                                    <label for="paymentStatus" class="form-label">حالة الدفع</label>
                                    <select class="form-select" id="paymentStatus" required>
                                        <option value="paid">مدفوع</option>
                                        <option value="pending" selected>قيد الانتظار</option>
                                        <option value="delayed">متأخر</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="paymentDate" class="form-label">تاريخ الدفع</label>
                                    <input type="date" class="form-control" id="paymentDate">
                                </div>
                                <div class="col-md-4">
                                    <label for="employeeStatus" class="form-label">حالة الموظف</label>
                                    <select class="form-select" id="employeeStatus" required>
                                        <option value="active" selected>نشط</option>
                                        <option value="inactive">غير نشط</option>
                                        <option value="suspended">موقوف</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="employeeNotes" class="form-label">ملاحظات إضافية</label>
                                <textarea class="form-control" id="employeeNotes" rows="3" placeholder="أي ملاحظات إضافية حول الموظف..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-gray-50 px-6 py-4 border-top border-gray-200 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-primary" id="saveEmployeeBtn">
                        <i class="fas fa-save ml-2"></i>
                        حفظ الموظف
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Sample employee data based on your model
    const employees = [
        {
            id: 1,
            fileTypes: [{ name: 'رواتب' }],
            client: { name: 'شركة التقنية المتطورة', code: '#CL-2024-001' },
            invoice: { id: '#INV-2024-001' },
            name: 'أحمد محمد',
            position: 'مطور ويب',
            phone: '+966 50 123 4567',
            iban: 'SA03 8000 0000 6080 1016 7519',
            bank_name: 'الراجحي',
            month_salary: 8000,
            absence_days: 4,
            deductions: 650,
            final_salary: 6433,
            payment_status: 'paid',
            work_days: 26
        },
        {
            id: 2,
            fileTypes: [{ name: 'مقاولين' }],
            client: { name: 'مؤسسة النجاح للتسويق', code: '#CL-2024-002' },
            invoice: { id: '#INV-2024-002' },
            name: 'فاطمة عبدالله',
            position: 'مصممة جرافيك',
            phone: '+966 55 987 6543',
            iban: 'SA03 8000 0000 6080 1016 7520',
            bank_name: 'البلاد',
            month_salary: 6500,
            absence_days: 2,
            deductions: 325,
            final_salary: 5733,
            payment_status: 'pending',
            work_days: 28
        },
        {
            id: 3,
            fileTypes: [{ name: 'رواتب' }],
            client: { name: 'شركة الأفق الجديد', code: '#CL-2024-003' },
            invoice: { id: '#INV-2024-003' },
            name: 'خالد إبراهيم',
            position: 'مسوق إلكتروني',
            phone: '+966 54 555 8888',
            iban: 'SA03 8000 0000 6080 1016 7521',
            bank_name: 'الإنماء',
            month_salary: 7200,
            absence_days: 5,
            deductions: 1200,
            final_salary: 4800,
            payment_status: 'delayed',
            work_days: 25
        },
        {
            id: 4,
            fileTypes: [{ name: 'رواتب' }],
            client: { name: 'مجموعة الإبداع التقني', code: '#CL-2024-004' },
            invoice: { id: '#INV-2024-004' },
            name: 'سارة عبدالرحمن',
            position: 'مستشارة تقنية',
            phone: '+966 53 111 2222',
            iban: 'SA03 8000 0000 6080 1016 7522',
            bank_name: 'الراجحي',
            month_salary: 9000,
            absence_days: 0,
            deductions: 0,
            final_salary: 9450,
            payment_status: 'paid',
            work_days: 30
        },
        {
            id: 5,
            fileTypes: [{ name: 'مقاولين' }],
            client: { name: 'شركة المستقبل الرقمي', code: '#CL-2024-005' },
            invoice: { id: '#INV-2024-005' },
            name: 'محمد علي',
            position: 'مطور تطبيقات',
            phone: '+966 56 444 7777',
            iban: 'SA03 8000 0000 6080 1016 7523',
            bank_name: 'البلاد',
            month_salary: 10000,
            absence_days: 3,
            deductions: 1050,
            final_salary: 8400,
            payment_status: 'pending',
            work_days: 27
        }
    ];

    // Function to render employees table
    function renderEmployees(employeesToRender) {
        const tableBody = document.getElementById('employeesTableBody');
        tableBody.innerHTML = '';

        employeesToRender.forEach(employee => {
            const row = document.createElement('tr');

            // Determine status badge
            let statusBadge = '';
            if (employee.payment_status === 'paid') {
                statusBadge = `<span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                        <i class="fas fa-check-circle me-1"></i>
                        مدفوع
                    </span>`;
            } else if (employee.payment_status === 'pending') {
                statusBadge = `<span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                        <i class="fas fa-clock me-1"></i>
                        قيد الانتظار
                    </span>`;
            } else {
                statusBadge = `<span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        متأخر
                    </span>`;
            }

            // Determine file type badge color
            const fileType = employee.fileTypes[0].name;
            const fileTypeBadge = fileType === 'رواتب' ? 'bg-primary' : 'bg-success';

            row.innerHTML = `
                    <td>
                        <div class="form-check">
                            <input class="form-check-input employee-checkbox" type="checkbox" value="${employee.id}">
                        </div>
                    </td>
                    <td>
                        <span class="badge ${fileTypeBadge}">${fileType}</span>
                    </td>
                    <td>
                        <div class="fw-bold">${employee.client.name}</div>
                        <small class="text-muted">${employee.client.code}</small>
                    </td>
                    <td>
                        <div class="fw-bold">${employee.invoice.id}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="employee-avatar me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <div class="fw-bold">${employee.name}</div>
                                <small class="text-muted">${employee.position}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold">${employee.phone}</div>
                    </td>
                    <td>
                        <div class="fw-bold">${employee.iban}</div>
                    </td>
                    <td>
                        <span class="badge bg-secondary">${employee.bank_name}</span>
                    </td>
                    <td>
                        <div class="fw-bold" style="color: var(--primary);">${employee.month_salary.toLocaleString()} ﷼</div>
                    </td>
                    <td>
                        <div class="text-center">
                            <div class="fw-bold ${employee.absence_days > 0 ? 'text-danger' : 'text-success'}">${employee.absence_days}</div>
                            <small class="text-muted">أيام</small>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-danger">${employee.deductions.toLocaleString()} ﷼</div>
                        <small class="text-muted">${employee.absence_days > 0 ? 'غياب + تأخيرات' : 'تأخيرات'}</small>
                    </td>
                    <td>
                        <div class="fw-bold" style="color: #10b981;">${employee.final_salary.toLocaleString()} ﷼</div>
                    </td>
                    <td>
                        ${statusBadge}
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض" onclick="viewEmployee(${employee.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" title="تعديل" onclick="editEmployee(${employee.id})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" title="كشف الراتب" onclick="generateSalarySlip(${employee.id})">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger" title="حذف" onclick="deleteEmployee(${employee.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;

            tableBody.appendChild(row);
        });
    }

    // Function to filter employees
    function filterEmployees() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const clientFilter = document.getElementById('clientFilter').value;
        const bankFilter = document.getElementById('bankFilter').value;
        const salaryTypeFilter = document.getElementById('salaryTypeFilter').value;
        const salaryFromFilter = document.getElementById('salaryFromFilter').value;

        let filteredEmployees = employees.filter(employee => {
            // Search filter
            const matchesSearch = !searchTerm ||
                employee.name.toLowerCase().includes(searchTerm) ||
                employee.phone.includes(searchTerm);

            // Client filter
            const matchesClient = !clientFilter ||
                employee.client.name.includes(clientFilter);

            // Bank filter
            const matchesBank = !bankFilter ||
                employee.bank_name === bankFilter;

            // Salary type filter
            const matchesSalaryType = !salaryTypeFilter ||
                (salaryTypeFilter === 'with_safety' && employee.month_salary > 5000) ||
                (salaryTypeFilter === 'without_safety' && employee.month_salary <= 5000);

            // Salary from filter
            const matchesSalaryFrom = !salaryFromFilter ||
                employee.month_salary >= parseFloat(salaryFromFilter);

            return matchesSearch && matchesClient && matchesBank && matchesSalaryType && matchesSalaryFrom;
        });

        renderEmployees(filteredEmployees);
        updatePaginationInfo(filteredEmployees.length);
    }

    // Function to update pagination info
    function updatePaginationInfo(count) {
        document.getElementById('paginationInfo').textContent = `عرض 1 إلى ${count} من ${count} موظف`;
    }

    // Employee action functions
    function viewEmployee(id) {
        alert(`عرض بيانات الموظف رقم ${id}`);
        // In a real app, this would redirect to employee details page
    }

    function editEmployee(id) {
        alert(`تعديل بيانات الموظف رقم ${id}`);
        // In a real app, this would open edit modal or redirect to edit page
    }

    function generateSalarySlip(id) {
        alert(`إنشاء كشف راتب للموظف رقم ${id}`);
        // In a real app, this would generate a salary slip PDF
    }

    function deleteEmployee(id) {
        if (confirm('هل أنت متأكد من حذف هذا الموظف؟')) {
            alert(`تم حذف الموظف رقم ${id}`);
            // In a real app, this would send a DELETE request to the server
        }
    }

    // Initialize the table when page loads
    document.addEventListener('DOMContentLoaded', function() {
        renderEmployees(employees);
        initializeCreateEmployeeModal();
        // Add event listeners for filtering
        document.getElementById('filterBtn').addEventListener('click', filterEmployees);
        document.getElementById('searchInput').addEventListener('input', filterEmployees);
        document.getElementById('clientFilter').addEventListener('change', filterEmployees);
        document.getElementById('bankFilter').addEventListener('change', filterEmployees);
        document.getElementById('salaryTypeFilter').addEventListener('change', filterEmployees);
        document.getElementById('salaryFromFilter').addEventListener('input', filterEmployees);

        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.employee-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    });
</script>
<script>
    // Create Employee Functions
    function initializeCreateEmployeeModal() {
        // Set today's date as default for hire date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('hireDate').value = today;

        // Auto-generate IBAN based on bank selection
        document.getElementById('bankName').addEventListener('change', function() {
            const bankCodes = {
                'الراجحي': 'SA03 8000 0000',
                'البلاد': 'SA04 7000 0000',
                'الإنماء': 'SA05 6000 0000'
            };

            const bankCode = bankCodes[this.value];
            if (bankCode) {
                // Generate random account number
                const randomAccount = Math.floor(1000000000 + Math.random() * 9000000000);
                document.getElementById('ibanNumber').value = `${bankCode} ${randomAccount}`;
                document.getElementById('accountNumber').value = randomAccount;
            }
        });

        // Calculate gross salary
        const calculateGrossSalary = () => {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const insuranceAmount = parseFloat(document.getElementById('insuranceAmount').value) || 0;
            const grossSalary = basicSalary + insuranceAmount;
            document.getElementById('grossSalary').value = grossSalary.toFixed(2);
            calculateFinalSalary();
        };

        // Calculate absence deduction
        const calculateAbsenceDeduction = () => {
            const basicSalary = parseFloat(document.getElementById('basicSalary').value) || 0;
            const absenceDays = parseInt(document.getElementById('absenceDays').value) || 0;
            const dailyRate = basicSalary / 30;
            const absenceDeduction = absenceDays * dailyRate;
            document.getElementById('absenceDeduction').value = absenceDeduction.toFixed(2);
            calculateFinalSalary();
        };

        // Calculate final salary
        const calculateFinalSalary = () => {
            const grossSalary = parseFloat(document.getElementById('grossSalary').value) || 0;
            const absenceDeduction = parseFloat(document.getElementById('absenceDeduction').value) || 0;
            const otherDeductions = parseFloat(document.getElementById('otherDeductions').value) || 0;
            const finalSalary = grossSalary - absenceDeduction - otherDeductions;
            document.getElementById('finalSalary').value = finalSalary.toFixed(2);
        };

        // Event listeners for calculations
        document.getElementById('basicSalary').addEventListener('input', calculateGrossSalary);
        document.getElementById('insuranceAmount').addEventListener('input', calculateGrossSalary);
        document.getElementById('absenceDays').addEventListener('input', calculateAbsenceDeduction);
        document.getElementById('otherDeductions').addEventListener('input', calculateFinalSalary);

        // Auto-fill payment date when status changes to paid
        document.getElementById('paymentStatus').addEventListener('change', function() {
            if (this.value === 'paid') {
                document.getElementById('paymentDate').value = today;
            } else {
                document.getElementById('paymentDate').value = '';
            }
        });

        // Save employee button handler
        document.getElementById('saveEmployeeBtn').addEventListener('click', createEmployee);
    }

    // Create new employee
    function createEmployee() {
        const form = document.getElementById('employeeForm');

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Collect form data
        const employeeData = {
            name: document.getElementById('employeeName').value,
            position: document.getElementById('employeePosition').value,
            phone: document.getElementById('employeePhone').value,
            email: document.getElementById('employeeEmail').value,
            national_id: document.getElementById('employeeId').value,
            nationality: document.getElementById('employeeNationality').value,
            client: document.getElementById('clientName').value,
            invoice_number: document.getElementById('invoiceNumber').value,
            file_type: document.getElementById('fileType').value,
            hire_date: document.getElementById('hireDate').value,
            bank_name: document.getElementById('bankName').value,
            iban: document.getElementById('ibanNumber').value,
            account_number: document.getElementById('accountNumber').value,
            basic_salary: parseFloat(document.getElementById('basicSalary').value),
            salary_type: document.getElementById('salaryType').value,
            insurance_amount: parseFloat(document.getElementById('insuranceAmount').value) || 0,
            absence_days: parseInt(document.getElementById('absenceDays').value) || 0,
            other_deductions: parseFloat(document.getElementById('otherDeductions').value) || 0,
            final_salary: parseFloat(document.getElementById('finalSalary').value),
            payment_status: document.getElementById('paymentStatus').value,
            payment_date: document.getElementById('paymentDate').value,
            status: document.getElementById('employeeStatus').value,
            notes: document.getElementById('employeeNotes').value
        };

        // Here you would typically send the data to your backend
        console.log('Employee Data:', employeeData);

        // Simulate API call
        simulateCreateEmployee(employeeData);
    }

    // Simulate creating employee (replace with actual API call)
    function simulateCreateEmployee(employeeData) {
        // Show loading state
        const saveBtn = document.getElementById('saveEmployeeBtn');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري الحفظ...';
        saveBtn.disabled = true;

        // Simulate API delay
        setTimeout(() => {
            // Generate new employee ID
            const newId = employees.length + 1;

            // Create new employee object
            const newEmployee = {
                id: newId,
                fileTypes: [{ name: employeeData.file_type === 'salary' ? 'رواتب' : 'مقاولين' }],
                client: {
                    name: getClientName(employeeData.client),
                    code: `#CL-2024-00${newId}`
                },
                invoice: { id: employeeData.invoice_number },
                name: employeeData.name,
                position: employeeData.position,
                phone: employeeData.phone,
                iban: employeeData.iban,
                bank_name: employeeData.bank_name,
                month_salary: employeeData.basic_salary,
                absence_days: employeeData.absence_days,
                deductions: employeeData.other_deductions + (employeeData.absence_days * (employeeData.basic_salary / 30)),
                final_salary: employeeData.final_salary,
                payment_status: employeeData.payment_status,
                work_days: 30 - employeeData.absence_days
            };

            // Add to employees array
            employees.unshift(newEmployee);

            // Update UI
            renderEmployees(employees);
            updateStatistics();

            // Show success message
            alert('تم إضافة الموظف بنجاح!');

            // Reset form and close modal
            document.getElementById('employeeForm').reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal'));
            modal.hide();

            // Reset button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

        }, 1500);
    }

    // Helper function to get client name
    function getClientName(clientValue) {
        const clients = {
            'tech': 'شركة التقنية المتطورة',
            'najah': 'مؤسسة النجاح للتسويق',
            'horizon': 'شركة الأفق الجديد'
        };
        return clients[clientValue] || 'غير محدد';
    }

    // Update statistics after adding new employee
    function updateStatistics() {
        const totalEmployees = employees.length;
        const totalSalaries = employees.reduce((sum, emp) => sum + emp.final_salary, 0);
        const averageSalary = totalSalaries / totalEmployees;
        const totalAbsences = employees.reduce((sum, emp) => sum + emp.absence_days, 0);

        document.getElementById('totalEmployees').textContent = totalEmployees;
        document.getElementById('totalSalaries').textContent = totalSalaries.toLocaleString() + ' ﷼';
        document.getElementById('averageSalary').textContent = Math.round(averageSalary).toLocaleString() + ' ﷼';
        document.getElementById('totalAbsences').textContent = totalAbsences;

        updatePaginationInfo(employees.length);
    }
</script>
@endpush
