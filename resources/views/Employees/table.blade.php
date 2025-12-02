@extends('layouts.master')

@section('title', 'إدارة العمالة')

@section('content')
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
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">إجمالي الموظفين</h6>
                            <h3 class="mb-0" style="color: var(--primary);">48</h3>
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
                            <h6 class="card-title text-muted mb-2">رواتب هذا الشهر</h6>
                            <h3 class="mb-0" style="color: #10b981;">245,800 ﷼</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-money-bill-wave" style="color: #10b981;"></i>
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
                            <h6 class="card-title text-muted mb-2">متوسط الراتب</h6>
                            <h3 class="mb-0" style="color: #f59e0b;">5,120 ﷼</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-chart-line" style="color: #f59e0b;"></i>
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
                            <h6 class="card-title text-muted mb-2">أيام الغياب</h6>
                            <h3 class="mb-0" style="color: #ef4444;">12</h3>
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
                    <input type="text" class="form-control" placeholder="بحث بالاسم أو الهاتف...">
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">كل العملاء</option>
                        <option value="1">شركة التقنية</option>
                        <option value="2">مؤسسة النجاح</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">كل البنوك</option>
                        <option value="alrajhi">الراجحي</option>
                        <option value="albilad">البلاد</option>
                        <option value="alinhar">الإنماء</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">نوع الراتب</option>
                        <option value="with_safety">بالتأمين</option>
                        <option value="without_safety">بدون تأمين</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" placeholder="الراتب من">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--light);">
                    <tr>
                        <th class="border-0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
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
                    <tbody>
                    <!-- الموظف 1 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">رواتب</span>
                        </td>
                        <td>
                            <div class="fw-bold">شركة التقنية المتطورة</div>
                            <small class="text-muted">#CL-2024-001</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-001</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="fas fa-user" style="color: var(--primary);"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">أحمد محمد</div>
                                    <small class="text-muted">مطور ويب</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">+966 50 123 4567</div>
                        </td>
                        <td>
                            <div class="fw-bold">SA03 8000 0000 6080 1016 7519</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">الراجحي</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">8,000 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-danger">4</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">650 ﷼</div>
                            <small class="text-muted">تأخيرات + غياب</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #10b981;">6,433 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-1"></i>
                مدفوع
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="كشف الراتب">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الموظف 2 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success">مقاولين</span>
                        </td>
                        <td>
                            <div class="fw-bold">مؤسسة النجاح للتسويق</div>
                            <small class="text-muted">#CL-2024-002</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-002</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="fas fa-user" style="color: var(--primary);"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">فاطمة عبدالله</div>
                                    <small class="text-muted">مصممة جرافيك</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">+966 55 987 6543</div>
                        </td>
                        <td>
                            <div class="fw-bold">SA03 8000 0000 6080 1016 7520</div>
                        </td>
                        <td>
                            <span class="badge bg-info">البلاد</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">6,500 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">2</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">325 ﷼</div>
                            <small class="text-muted">تأخيرات</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #f59e0b;">5,733 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد الانتظار
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="كشف الراتب">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الموظف 3 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">رواتب</span>
                        </td>
                        <td>
                            <div class="fw-bold">شركة الأفق الجديد</div>
                            <small class="text-muted">#CL-2024-003</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-003</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="fas fa-user" style="color: var(--primary);"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">خالد إبراهيم</div>
                                    <small class="text-muted">مسوق إلكتروني</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">+966 54 555 8888</div>
                        </td>
                        <td>
                            <div class="fw-bold">SA03 8000 0000 6080 1016 7521</div>
                        </td>
                        <td>
                            <span class="badge bg-warning">الإنماء</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">7,200 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-danger">5</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">1,200 ﷼</div>
                            <small class="text-muted">غياب + تأخيرات</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #ef4444;">4,800 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                متأخر
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="كشف الراتب">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الموظف 4 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">رواتب</span>
                        </td>
                        <td>
                            <div class="fw-bold">مجموعة الإبداع التقني</div>
                            <small class="text-muted">#CL-2024-004</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-004</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="fas fa-user" style="color: var(--primary);"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">سارة عبدالرحمن</div>
                                    <small class="text-muted">مستشارة تقنية</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">+966 53 111 2222</div>
                        </td>
                        <td>
                            <div class="fw-bold">SA03 8000 0000 6080 1016 7522</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">الراجحي</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">9,000 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-success">0 ﷼</div>
                            <small class="text-muted">لا يوجد</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #10b981;">9,450 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-1"></i>
                مدفوع
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="كشف الراتب">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الموظف 5 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success">مقاولين</span>
                        </td>
                        <td>
                            <div class="fw-bold">شركة المستقبل الرقمي</div>
                            <small class="text-muted">#CL-2024-005</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-005</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-light rounded-circle p-2 me-2">
                                    <i class="fas fa-user" style="color: var(--primary);"></i>
                                </div>
                                <div>
                                    <div class="fw-bold">محمد علي</div>
                                    <small class="text-muted">مطور تطبيقات</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">+966 56 444 7777</div>
                        </td>
                        <td>
                            <div class="fw-bold">SA03 8000 0000 6080 1016 7523</div>
                        </td>
                        <td>
                            <span class="badge bg-info">البلاد</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">10,000 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">3</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">1,050 ﷼</div>
                            <small class="text-muted">غياب</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: #f59e0b;">8,400 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد الانتظار
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-primary" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="كشف الراتب">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            عرض 1 إلى 5 من 48 موظف
        </div>
        <nav>
            <ul class="pagination mb-0">
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
                <div class="modal-header bg-emerald-500 text-white p-4">
                    <h5 class="modal-title text-xl font-bold flex items-center" id="addEmployeeModalLabel">
                        <i class="fas fa-plus-circle ml-2"></i>
                        إضافة موظف جديد
                    </h5>
                    <button type="button" class="btn-close text-white bg-emerald-600 hover:bg-emerald-700 w-8 h-8 rounded-full flex items-center justify-center" data-bs-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-6">
                    <form id="employeeForm">
                        <!-- Personal Information Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <!-- Personal Information -->
                            <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <h6 class="form-section-title text-emerald-500 font-semibold mb-4 text-lg flex items-center">
                                    <i class="fas fa-user ml-2"></i>
                                    المعلومات الشخصية
                                </h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="employeeName" class="form-label block font-medium text-gray-700 mb-2">اسم الموظف</label>
                                        <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeeName" required>
                                    </div>
                                    <div>
                                        <label for="employeePosition" class="form-label block font-medium text-gray-700 mb-2">المسمى الوظيفي</label>
                                        <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeePosition" required>
                                    </div>
                                    <div>
                                        <label for="employeePhone" class="form-label block font-medium text-gray-700 mb-2">رقم الجوال</label>
                                        <input type="tel" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeePhone" required>
                                    </div>
                                    <div>
                                        <label for="employeeEmail" class="form-label block font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                                        <input type="email" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeeEmail">
                                    </div>
                                    <div>
                                        <label for="employeeId" class="form-label block font-medium text-gray-700 mb-2">رقم الهوية</label>
                                        <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeeId" required>
                                    </div>
                                    <div>
                                        <label for="employeeNationality" class="form-label block font-medium text-gray-700 mb-2">الجنسية</label>
                                        <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeeNationality" value="سعودي">
                                    </div>
                                </div>
                            </div>

                            <!-- Work Information -->
                            <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <h6 class="form-section-title text-emerald-500 font-semibold mb-4 text-lg flex items-center">
                                    <i class="fas fa-building ml-2"></i>
                                    معلومات العمل
                                </h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="clientName" class="form-label block font-medium text-gray-700 mb-2">العميل</label>
                                        <select class="form-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="clientName" required>
                                            <option value="">اختر العميل</option>
                                            <option value="tech">شركة التقنية المتطورة</option>
                                            <option value="najah">مؤسسة النجاح للتسويق</option>
                                            <option value="horizon">شركة الأفق الجديد</option>
                                            <option value="ibdaa">مجموعة الإبداع التقني</option>
                                            <option value="future">شركة المستقبل الرقمي</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="invoiceNumber" class="form-label block font-medium text-gray-700 mb-2">رقم الفاتورة</label>
                                        <select class="form-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="invoiceNumber" required>
                                            <option value="">اختر الفاتورة</option>
                                            <option value="INV-2024-001">#INV-2024-001</option>
                                            <option value="INV-2024-002">#INV-2024-002</option>
                                            <option value="INV-2024-003">#INV-2024-003</option>
                                            <option value="INV-2024-004">#INV-2024-004</option>
                                            <option value="INV-2024-005">#INV-2024-005</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="fileType" class="form-label block font-medium text-gray-700 mb-2">نوع الملف</label>
                                        <select class="form-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="fileType" required>
                                            <option value="salary">رواتب</option>
                                            <option value="contractor">مقاولين</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="hireDate" class="form-label block font-medium text-gray-700 mb-2">تاريخ التعيين</label>
                                        <input type="text" class="flatpickr-input form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white cursor-pointer" id="hireDate" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bank Information -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 mb-6">
                            <h6 class="form-section-title text-emerald-500 font-semibold mb-4 text-lg flex items-center">
                                <i class="fas fa-university ml-2"></i>
                                معلومات البنك
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="bankName" class="form-label block font-medium text-gray-700 mb-2">البنك</label>
                                    <select class="form-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="bankName" required>
                                        <option value="">اختر البنك</option>
                                        <option value="alrajhi">الراجحي</option>
                                        <option value="albilad">البلاد</option>
                                        <option value="alinhar">الإنماء</option>
                                        <option value="aljazira">الجزيرة</option>
                                        <option value="riyad">الرياض</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="ibanNumber" class="form-label block font-medium text-gray-700 mb-2">رقم الآيبان</label>
                                    <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="ibanNumber" required>
                                </div>
                                <div>
                                    <label for="accountNumber" class="form-label block font-medium text-gray-700 mb-2">رقم الحساب</label>
                                    <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="accountNumber" required>
                                </div>
                            </div>
                        </div>

                        <!-- Salary Information -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 mb-6">
                            <h6 class="form-section-title text-emerald-500 font-semibold mb-4 text-lg flex items-center">
                                <i class="fas fa-money-bill-wave ml-2"></i>
                                معلومات الراتب
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="basicSalary" class="form-label block font-medium text-gray-700 mb-2">الراتب الأساسي (﷼)</label>
                                    <input type="number" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="basicSalary" min="0" step="0.01" required>
                                </div>
                                <div>
                                    <label for="salaryType" class="form-label block font-medium text-gray-700 mb-2">نوع الراتب</label>
                                    <select class="form-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="salaryType" required>
                                        <option value="with_safety">بالتأمين</option>
                                        <option value="without_safety">بدون تأمين</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="insuranceAmount" class="form-label block font-medium text-gray-700 mb-2">قيمة التأمين (﷼)</label>
                                    <input type="number" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="insuranceAmount" min="0" step="0.01" value="0">
                                </div>
                                <div>
                                    <label for="grossSalary" class="form-label block font-medium text-gray-700 mb-2">الراتب الإجمالي (﷼)</label>
                                    <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" id="grossSalary" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Deductions and Final Salary -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200 mb-6">
                            <h6 class="form-section-title text-emerald-500 font-semibold mb-4 text-lg flex items-center">
                                <i class="fas fa-calculator ml-2"></i>
                                الخصومات والراتب النهائي
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="absenceDays" class="form-label block font-medium text-gray-700 mb-2">أيام الغياب</label>
                                    <input type="number" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="absenceDays" min="0" value="0">
                                </div>
                                <div>
                                    <label for="absenceDeduction" class="form-label block font-medium text-gray-700 mb-2">خصم الغياب (﷼)</label>
                                    <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" id="absenceDeduction" readonly>
                                </div>
                                <div>
                                    <label for="otherDeductions" class="form-label block font-medium text-gray-700 mb-2">خصومات أخرى (﷼)</label>
                                    <input type="number" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="otherDeductions" min="0" step="0.01" value="0">
                                </div>
                                <div>
                                    <label for="finalSalary" class="form-label block font-medium text-gray-700 mb-2">الراتب النهائي (﷼)</label>
                                    <input type="text" class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" id="finalSalary" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Status and Notes -->
                        <div class="form-section bg-gray-50 rounded-xl p-4 border border-gray-200">
                            <h6 class="form-section-title text-emerald-500 font-semibold mb-4 text-lg flex items-center">
                                <i class="fas fa-clipboard-check ml-2"></i>
                                الحالة والملاحظات
                            </h6>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="paymentStatus" class="form-label block font-medium text-gray-700 mb-2">حالة الدفع</label>
                                    <select class="form-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="paymentStatus" required>
                                        <option value="paid">مدفوع</option>
                                        <option value="pending" selected>قيد الانتظار</option>
                                        <option value="delayed">متأخر</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="paymentDate" class="form-label block font-medium text-gray-700 mb-2">تاريخ الدفع</label>
                                    <input type="text" class="flatpickr-input form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent bg-white cursor-pointer" id="paymentDate" readonly>
                                </div>
                                <div>
                                    <label for="employeeStatus" class="form-label block font-medium text-gray-700 mb-2">حالة الموظف</label>
                                    <select class="form-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeeStatus" required>
                                        <option value="active" selected>نشط</option>
                                        <option value="inactive">غير نشط</option>
                                        <option value="suspended">موقوف</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="employeeNotes" class="form-label block font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                                <textarea class="form-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" id="employeeNotes" rows="3" placeholder="أي ملاحظات إضافية حول الموظف..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-between">
                    <button type="button" class="btn btn-secondary bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-2 rounded-lg font-medium flex items-center transition-colors duration-200" id="saveEmployeeBtn">
                        <i class="fas fa-save ml-2"></i>
                        حفظ الموظف
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .table th {
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
            padding: 1rem 0.75rem;
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

        .modal-header .btn-close {
            filter: invert(1);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set today's date as default for hire date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('hireDate').value = today;

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

                // Assuming 30 days in a month
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

            // Auto-generate IBAN based on bank selection
            document.getElementById('bankName').addEventListener('change', function() {
                const bankCodes = {
                    'alrajhi': 'SA03 8000 0000',
                    'albilad': 'SA04 7000 0000',
                    'alinhar': 'SA05 6000 0000',
                    'aljazira': 'SA06 5000 0000',
                    'riyad': 'SA07 4000 0000'
                };

                const bankCode = bankCodes[this.value];
                if (bankCode) {
                    // Generate random account number (in real app, this would come from user input)
                    const randomAccount = Math.floor(1000000000 + Math.random() * 9000000000);
                    document.getElementById('ibanNumber').value = `${bankCode} ${randomAccount}`;
                    document.getElementById('accountNumber').value = randomAccount;
                }
            });

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
            document.getElementById('saveEmployeeBtn').addEventListener('click', function() {
                // Here you would typically send the form data to your backend
                alert('تم حفظ الموظف بنجاح!');
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal'));
                modal.hide();
            });
        });
        flatpickr.localize(flatpickr.l10ns.ar);

        // Initialize date pickers
        flatpickr("#hireDate", {
            dateFormat: "Y-m-d",
            allowInput: false,
            locale: "ar"
        });

        flatpickr("#paymentDate", {
            dateFormat: "Y-m-d",
            allowInput: false,
            locale: "ar"
        });
    </script>
@endsection
