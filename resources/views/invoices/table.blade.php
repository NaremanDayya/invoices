@extends('layouts.master')

@section('title', 'إدارة الفواتير')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--primary);">
            <i class="fas fa-file-invoice me-2"></i>
            إدارة الفواتير
        </h2>
        <button class="btn" style="background: var(--primary); color: white;" data-bs-toggle="modal" data-bs-target="#addInvoiceModal">
            <i class="fas fa-plus me-2"></i>
            فاتورة جديدة
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">إجمالي الفواتير</h6>
                            <h3 class="mb-0" style="color: var(--primary);">156</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-receipt" style="color: var(--primary);"></i>
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
                            <h6 class="card-title text-muted mb-2">مدفوعة</h6>
                            <h3 class="mb-0" style="color: #10b981;">98</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-check-circle" style="color: #10b981;"></i>
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
                            <h6 class="card-title text-muted mb-2">قيد الانتظار</h6>
                            <h3 class="mb-0" style="color: #f59e0b;">42</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-clock" style="color: #f59e0b;"></i>
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
                            <h6 class="card-title text-muted mb-2">متأخرة</h6>
                            <h3 class="mb-0" style="color: #ef4444;">16</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
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
                    <input type="text" class="form-control" placeholder="بحث في الفواتير...">
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">كل الحالات</option>
                        <option value="paid">مدفوعة</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="overdue">متأخرة</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">كل العملاء</option>
                        <option value="1">شركة التقنية</option>
                        <option value="2">مؤسسة النجاح</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" placeholder="من تاريخ">
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" placeholder="إلى تاريخ">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices Table -->
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
                        <th class="border-0">رقم الفاتورة</th>
                        <th class="border-0">العميل</th>
                        <th class="border-0">تاريخ الإصدار</th>
                        <th class="border-0">أيام تأخير الإصدار</th>
                        <th class="border-0">نوع الخدمة</th>
                        <th class="border-0">إجمالي العمالة</th>
                        <th class="border-0">أيام العمل</th>
                        <th class="border-0">المبلغ قبل الضريبة</th>
                        <th class="border-0">الضريبة</th>
                        <th class="border-0">المبلغ الإجمالي</th>
                        <th class="border-0">فرق المبلغ</th>
                        <th class="border-0">حالة السداد</th>
                        <th class="border-0">تاريخ السداد</th>
                        <th class="border-0">أيام تأخير السداد</th>
                        <th class="border-0">حالة الفاتورة</th>
                        <th class="border-0 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- الفاتورة 1 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-001</div>
                        </td>
                        <td>
                            <div class="fw-bold">شركة التقنية المتطورة</div>
                            <small class="text-muted">info@tech.com</small>
                        </td>
                        <td>2024-01-15</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary">تطوير ويب</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">5</div>
                                <small class="text-muted">عامل</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">30</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">25,000 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">2,500 ﷼</div>
                            <small class="text-muted">15%</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">27,500 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-success">0 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-1"></i>
                مدفوعة
            </span>
                        </td>
                        <td>2024-01-20</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-1"></i>
                مكتملة
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
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الفاتورة 2 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-002</div>
                        </td>
                        <td>
                            <div class="fw-bold">مؤسسة النجاح للتسويق</div>
                            <small class="text-muted">contact@najah.com</small>
                        </td>
                        <td>2024-01-20</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">5</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-success">تصميم جرافيك</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">3</div>
                                <small class="text-muted">عامل</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">22</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">12,000 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">1,200 ﷼</div>
                            <small class="text-muted">10%</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">13,200 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">-800 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد الانتظار
            </span>
                        </td>
                        <td>---</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">15</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                معلقة
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
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الفاتورة 3 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-003</div>
                        </td>
                        <td>
                            <div class="fw-bold">شركة الأفق الجديد</div>
                            <small class="text-muted">sales@horizon.com</small>
                        </td>
                        <td>2024-02-01</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-danger">10</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-info">تسويق إلكتروني</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">8</div>
                                <small class="text-muted">عامل</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">45</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">45,000 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">4,500 ﷼</div>
                            <small class="text-muted">10%</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">49,500 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">-5,000 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                متأخرة
            </span>
                        </td>
                        <td>---</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-danger">25</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                <i class="fas fa-exclamation-triangle me-1"></i>
                متأخرة
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
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الفاتورة 4 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-004</div>
                        </td>
                        <td>
                            <div class="fw-bold">مجموعة الإبداع التقني</div>
                            <small class="text-muted">info@ibdaa.com</small>
                        </td>
                        <td>2024-02-10</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-warning">استشارات تقنية</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">2</div>
                                <small class="text-muted">عامل</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">18</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">18,000 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">1,800 ﷼</div>
                            <small class="text-muted">10%</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">19,800 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-success">200 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-1"></i>
                مدفوعة
            </span>
                        </td>
                        <td>2024-02-25</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-1"></i>
                مكتملة
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
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- الفاتورة 5 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-005</div>
                        </td>
                        <td>
                            <div class="fw-bold">شركة المستقبل الرقمي</div>
                            <small class="text-muted">dev@future.com</small>
                        </td>
                        <td>2024-02-15</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">3</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-danger">تطوير تطبيقات</span>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">6</div>
                                <small class="text-muted">عامل</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">60</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">60,000 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">6,000 ﷼</div>
                            <small class="text-muted">10%</small>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">66,000 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-warning">-1,500 ﷼</div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد الانتظار
            </span>
                        </td>
                        <td>---</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">8</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                معلقة
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
                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div class="text-muted">
            عرض 1 إلى 5 من 156 فاتورة
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

    <!-- Add Invoice Modal -->
    <div class="modal fade" id="addInvoiceModal" tabindex="-1" aria-labelledby="addInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary); color: white;">
                    <h5 class="modal-title" id="addInvoiceModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>
                        إضافة فاتورة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="invoiceForm">
                        <!-- Client Information Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <!-- Client Information -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                    <i class="fas fa-user"></i>
                                    <h6 class="text-lg font-semibold">معلومات العميل</h6>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="clientName" class="block text-sm font-medium text-gray-700 mb-2">اسم العميل</label>
                                        <input type="text" id="clientName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                    </div>
                                    <div>
                                        <label for="clientEmail" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                                        <input type="email" id="clientEmail" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="clientPhone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                                        <input type="tel" id="clientPhone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="clientAddress" class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                                        <input type="text" id="clientAddress" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Information -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                    <i class="fas fa-file-invoice"></i>
                                    <h6 class="text-lg font-semibold">معلومات الفاتورة</h6>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="invoiceNumber" class="block text-sm font-medium text-gray-700 mb-2">رقم الفاتورة</label>
                                        <input type="text" id="invoiceNumber" value="#INV-2024-" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                    </div>
                                    <div>
                                        <label for="invoiceDate" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإصدار</label>
                                        <input type="text" id="invoiceDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors flatpickr" required>
                                    </div>
                                    <div>
                                        <label for="dueDate" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق</label>
                                        <input type="text" id="dueDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors flatpickr" required>
                                    </div>
                                    <div>
                                        <label for="serviceType" class="block text-sm font-medium text-gray-700 mb-2">نوع الخدمة</label>
                                        <select id="serviceType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                            <option value="">اختر نوع الخدمة</option>
                                            <option value="web">تطوير ويب</option>
                                            <option value="design">تصميم جرافيك</option>
                                            <option value="marketing">تسويق إلكتروني</option>
                                            <option value="consulting">استشارات تقنية</option>
                                            <option value="app">تطوير تطبيقات</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Service Details Row -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                <i class="fas fa-cogs"></i>
                                <h6 class="text-lg font-semibold">تفاصيل الخدمة</h6>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="totalWorkers" class="block text-sm font-medium text-gray-700 mb-2">إجمالي العمالة</label>
                                    <input type="number" id="totalWorkers" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                </div>
                                <div>
                                    <label for="workDays" class="block text-sm font-medium text-gray-700 mb-2">أيام العمل</label>
                                    <input type="number" id="workDays" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                </div>
                                <div>
                                    <label for="dailyRate" class="block text-sm font-medium text-gray-700 mb-2">معدل اليومي (﷼)</label>
                                    <input type="number" id="dailyRate" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                </div>
                                <div>
                                    <label for="subtotal" class="block text-sm font-medium text-gray-700 mb-2">المبلغ قبل الضريبة (﷼)</label>
                                    <input type="text" id="subtotal" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                </div>
                            </div>
                        </div>

                        <!-- Financial Details Row -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                <i class="fas fa-calculator"></i>
                                <h6 class="text-lg font-semibold">التفاصيل المالية</h6>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="taxRate" class="block text-sm font-medium text-gray-700 mb-2">نسبة الضريبة (%)</label>
                                    <input type="number" id="taxRate" min="0" max="100" step="0.1" value="15" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                </div>
                                <div>
                                    <label for="taxAmount" class="block text-sm font-medium text-gray-700 mb-2">قيمة الضريبة (﷼)</label>
                                    <input type="text" id="taxAmount" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                </div>
                                <div>
                                    <label for="totalAmount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ الإجمالي (﷼)</label>
                                    <input type="text" id="totalAmount" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                </div>
                                <div>
                                    <label for="amountDifference" class="block text-sm font-medium text-gray-700 mb-2">فرق المبلغ (﷼)</label>
                                    <input type="number" id="amountDifference" step="0.01" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Status Row -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                <i class="fas fa-credit-card"></i>
                                <h6 class="text-lg font-semibold">حالة السداد</h6>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="paymentStatus" class="block text-sm font-medium text-gray-700 mb-2">حالة السداد</label>
                                    <select id="paymentStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                        <option value="paid">مدفوعة</option>
                                        <option value="pending" selected>قيد الانتظار</option>
                                        <option value="overdue">متأخرة</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="paymentDate" class="block text-sm font-medium text-gray-700 mb-2">تاريخ السداد</label>
                                    <input type="text" id="paymentDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors flatpickr">
                                </div>
                                <div>
                                    <label for="invoiceStatus" class="block text-sm font-medium text-gray-700 mb-2">حالة الفاتورة</label>
                                    <select id="invoiceStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                        <option value="completed">مكتملة</option>
                                        <option value="pending" selected>معلقة</option>
                                        <option value="overdue">متأخرة</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Row -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                <i class="fas fa-sticky-note"></i>
                                <h6 class="text-lg font-semibold">ملاحظات إضافية</h6>
                            </div>
                            <textarea id="invoiceNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" placeholder="أي ملاحظات إضافية حول الفاتورة..."></textarea>
                        </div>
                    </form>                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn" style="background: var(--primary); color: white;" id="saveInvoiceBtn">
                        <i class="fas fa-save me-2"></i>
                        حفظ الفاتورة
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
            const flatpickrOptions = {
                locale: 'ar',
                dateFormat: 'Y-m-d',
                allowInput: true,
                clickOpens: true,
                position: 'auto right'
            };

            flatpickr('#invoiceDate', {
                ...flatpickrOptions,
                defaultDate: 'today'
            });

            flatpickr('#dueDate', {
                ...flatpickrOptions,
                defaultDate: 'today'
            });

            flatpickr('#paymentDate', flatpickrOptions);
            // Calculate subtotal when worker details change
            const calculateSubtotal = () => {
                const workers = parseInt(document.getElementById('totalWorkers').value) || 0;
                const days = parseInt(document.getElementById('workDays').value) || 0;
                const rate = parseFloat(document.getElementById('dailyRate').value) || 0;

                const subtotal = workers * days * rate;
                document.getElementById('subtotal').value = subtotal.toFixed(2);

                calculateTaxAndTotal();
            };

            // Calculate tax and total amounts
            const calculateTaxAndTotal = () => {
                const subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
                const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;

                const taxAmount = (subtotal * taxRate) / 100;
                const totalAmount = subtotal + taxAmount;

                document.getElementById('taxAmount').value = taxAmount.toFixed(2);
                document.getElementById('totalAmount').value = totalAmount.toFixed(2);
            };

            // Set today's date as default for invoice date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('invoiceDate').value = today;

            // Set due date to 30 days from today
            const dueDate = new Date();
            dueDate.setDate(dueDate.getDate() + 30);
            document.getElementById('dueDate').value = dueDate.toISOString().split('T')[0];

            // Event listeners for calculations
            document.getElementById('totalWorkers').addEventListener('input', calculateSubtotal);
            document.getElementById('workDays').addEventListener('input', calculateSubtotal);
            document.getElementById('dailyRate').addEventListener('input', calculateSubtotal);
            document.getElementById('taxRate').addEventListener('input', calculateTaxAndTotal);

            // Save invoice button handler
            document.getElementById('saveInvoiceBtn').addEventListener('click', function() {
                // Here you would typically send the form data to your backend
                alert('تم حفظ الفاتورة بنجاح!');
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addInvoiceModal'));
                modal.hide();
            });
        });
    </script>
@endsection
