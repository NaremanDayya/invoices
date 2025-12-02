@extends('layouts.master')

@section('title', 'إدارة أوامر الدفع')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--primary);">
            <i class="fas fa-credit-card me-2"></i>
            إدارة أوامر الدفع
        </h2>
        <button class="btn" style="background: var(--primary); color: white;" data-bs-toggle="modal" data-bs-target="#addPaymentOrderModal">
            <i class="fas fa-plus me-2"></i>
            أمر دفع جديد
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">إجمالي المدفوعات</h6>
                            <h3 class="mb-0" style="color: var(--primary);">89</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-money-bill-wave" style="color: var(--primary);"></i>
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
                            <h6 class="card-title text-muted mb-2">مكتملة</h6>
                            <h3 class="mb-0" style="color: #10b981;">64</h3>
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
                            <h6 class="card-title text-muted mb-2">قيد المراجعة</h6>
                            <h3 class="mb-0" style="color: #f59e0b;">18</h3>
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
                            <h6 class="card-title text-muted mb-2">ملغاة</h6>
                            <h3 class="mb-0" style="color: #ef4444;">7</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-times-circle" style="color: #ef4444;"></i>
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
                    <input type="text" class="form-control" placeholder="بحث في أوامر الدفع...">
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">كل العملاء</option>
                        <option value="1">شركة التقنية</option>
                        <option value="2">مؤسسة النجاح</option>
                        <option value="3">شركة الأفق</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">حالة الموافقة</option>
                        <option value="approved">موافق عليه</option>
                        <option value="pending">قيد المراجعة</option>
                        <option value="rejected">مرفوض</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select">
                        <option value="">حالة الدفع</option>
                        <option value="paid">مدفوع</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="failed">فاشل</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" class="form-control" placeholder="من تاريخ">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Orders Table -->
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
                        <th class="border-0">رقم الأمر</th>
                        <th class="border-0">العميل</th>
                        <th class="border-0">رقم الفاتورة</th>
                        <th class="border-0">تاريخ أمر السداد</th>
                        <th class="border-0">رقم ملف الموظفين</th>
                        <th class="border-0">نوع السداد</th>
                        <th class="border-0">المبلغ الإجمالي</th>
                        <th class="border-0">إجمالي الخصم</th>
                        <th class="border-0">عدد الموظفين</th>
                        <th class="border-0">عدد الأيام</th>
                        <th class="border-0">حالة الموافقة</th>
                        <th class="border-0">تاريخ الموافقة الإدارية</th>
                        <th class="border-0">حالة السداد</th>
                        <th class="border-0">تاريخ السداد</th>
                        <th class="border-0">الفرق العددي</th>
                        <th class="border-0">أيام التأخير</th>
                        <th class="border-0">حالة الدفع</th>
                        <th class="border-0 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- أمر السداد 1 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#PO-2024-001</div>
                        </td>
                        <td>
                            <div class="fw-bold">شركة التقنية المتطورة</div>
                            <small class="text-muted">Mohamed Ahmed</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-001</div>
                        </td>
                        <td>2024-01-15</td>
                        <td>
                            <div class="fw-bold">#EMP-2024-001</div>
                        </td>
                        <td>
                            <span class="badge bg-primary">تحويل بنكي</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">27,500 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">1,200 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">5</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">30</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check me-1"></i>
                موافق
            </span>
                        </td>
                        <td>2024-01-18</td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check-circle me-1"></i>
                مسدد
            </span>
                        </td>
                        <td>2024-01-20</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">+3</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check me-1"></i>
                مكتمل
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="تأكيد الدفع">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- أمر السداد 2 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#PO-2024-002</div>
                        </td>
                        <td>
                            <div class="fw-bold">مؤسسة النجاح للتسويق</div>
                            <small class="text-muted">Ali Hassan</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-002</div>
                        </td>
                        <td>2024-01-20</td>
                        <td>
                            <div class="fw-bold">#EMP-2024-002</div>
                        </td>
                        <td>
                            <span class="badge bg-success">محفظة إلكترونية</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">13,200 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">800 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">3</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">22</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد المراجعة
            </span>
                        </td>
                        <td>---</td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد الانتظار
            </span>
                        </td>
                        <td>---</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">0</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">5</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                معلق
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="تأكيد الدفع">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- أمر السداد 3 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#PO-2024-003</div>
                        </td>
                        <td>
                            <div class="fw-bold">شركة الأفق الجديد</div>
                            <small class="text-muted">Sara Mohammed</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-003</div>
                        </td>
                        <td>2024-02-01</td>
                        <td>
                            <div class="fw-bold">#EMP-2024-003</div>
                        </td>
                        <td>
                            <span class="badge bg-info">بطاقة ائتمان</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">49,500 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">3,500 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">8</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">45</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                <i class="fas fa-times me-1"></i>
                مرفوض
            </span>
                        </td>
                        <td>---</td>
                        <td>
            <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                <i class="fas fa-times-circle me-1"></i>
                فاشل
            </span>
                        </td>
                        <td>---</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-danger">-2</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-danger">12</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fee2e2; color: #991b1b;">
                <i class="fas fa-times me-1"></i>
                مرفوض
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="إعادة الإرسال">
                                    <i class="fas fa-redo"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- أمر السداد 4 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#PO-2024-004</div>
                        </td>
                        <td>
                            <div class="fw-bold">مجموعة الإبداع التقني</div>
                            <small class="text-muted">Khalid Omar</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-004</div>
                        </td>
                        <td>2024-02-10</td>
                        <td>
                            <div class="fw-bold">#EMP-2024-004</div>
                        </td>
                        <td>
                            <span class="badge bg-primary">تحويل بنكي</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">19,800 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">500 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">2</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">18</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #d1fae5; color: #065f46;">
                <i class="fas fa-check me-1"></i>
                موافق
            </span>
                        </td>
                        <td>2024-02-12</td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد الانتظار
            </span>
                        </td>
                        <td>---</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">+1</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-success">0</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد المعالجة
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="تأكيد الدفع">
                                    <i class="fas fa-check"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- أمر السداد 5 -->
                    <tr>
                        <td>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox">
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold">#PO-2024-005</div>
                        </td>
                        <td>
                            <div class="fw-bold">شركة المستقبل الرقمي</div>
                            <small class="text-muted">Noura Abdullah</small>
                        </td>
                        <td>
                            <div class="fw-bold">#INV-2024-005</div>
                        </td>
                        <td>2024-02-15</td>
                        <td>
                            <div class="fw-bold">#EMP-2024-005</div>
                        </td>
                        <td>
                            <span class="badge bg-success">محفظة إلكترونية</span>
                        </td>
                        <td>
                            <div class="fw-bold" style="color: var(--primary);">66,000 ﷼</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger">2,500 ﷼</div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">6</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold">60</div>
                                <small class="text-muted">يوم</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد المراجعة
            </span>
                        </td>
                        <td>---</td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                قيد الانتظار
            </span>
                        </td>
                        <td>---</td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">0</div>
                                <small class="text-muted">موظف</small>
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="fw-bold text-warning">3</div>
                                <small class="text-muted">أيام</small>
                            </div>
                        </td>
                        <td>
            <span class="badge rounded-pill" style="background: #fef3c7; color: #92400e;">
                <i class="fas fa-clock me-1"></i>
                معلق
            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-info" title="طباعة">
                                    <i class="fas fa-print"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-success" title="تأكيد الدفع">
                                    <i class="fas fa-check"></i>
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
            عرض 1 إلى 5 من 89 أمر دفع
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
                    <a class="page-link" href="#">4</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">5</a>
                </li>
                <li class="page-item">
                    <a class="page-link" href="#">التالي</a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Add Payment Order Modal -->
    <div class="modal fade" id="addPaymentOrderModal" tabindex="-1" aria-labelledby="addPaymentOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header" style="background: var(--primary); color: white;">
                    <h5 class="modal-title" id="addPaymentOrderModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>
                        إضافة أمر دفع جديد
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="paymentOrderForm">
                        <!-- Order Information Row -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <!-- Basic Order Information -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                    <i class="fas fa-info-circle"></i>
                                    <h6 class="text-lg font-semibold">معلومات الأمر الأساسية</h6>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="orderNumber" class="block text-sm font-medium text-gray-700 mb-2">رقم الأمر</label>
                                        <input type="text" id="orderNumber" value="#PO-2024-" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                    </div>
                                    <div>
                                        <label for="orderDate" class="block text-sm font-medium text-gray-700 mb-2">تاريخ أمر السداد</label>
                                        <input type="text" id="orderDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors flatpickr" required>
                                    </div>
                                    <div>
                                        <label for="invoiceNumber" class="block text-sm font-medium text-gray-700 mb-2">رقم الفاتورة</label>
                                        <select id="invoiceNumber" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                            <option value="">اختر الفاتورة</option>
                                            <option value="INV-2024-001">#INV-2024-001</option>
                                            <option value="INV-2024-002">#INV-2024-002</option>
                                            <option value="INV-2024-003">#INV-2024-003</option>
                                            <option value="INV-2024-004">#INV-2024-004</option>
                                            <option value="INV-2024-005">#INV-2024-005</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="employeeFile" class="block text-sm font-medium text-gray-700 mb-2">رقم ملف الموظفين</label>
                                        <input type="text" id="employeeFile" value="#EMP-2024-" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Client Information -->
                            <div class="space-y-4">
                                <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                    <i class="fas fa-user"></i>
                                    <h6 class="text-lg font-semibold">معلومات العميل</h6>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="clientName" class="block text-sm font-medium text-gray-700 mb-2">اسم العميل</label>
                                        <select id="clientName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                            <option value="">اختر العميل</option>
                                            <option value="tech">شركة التقنية المتطورة</option>
                                            <option value="najah">مؤسسة النجاح للتسويق</option>
                                            <option value="horizon">شركة الأفق الجديد</option>
                                            <option value="ibdaa">مجموعة الإبداع التقني</option>
                                            <option value="future">شركة المستقبل الرقمي</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="clientContact" class="block text-sm font-medium text-gray-700 mb-2">اسم جهة الاتصال</label>
                                        <input type="text" id="clientContact" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="paymentType" class="block text-sm font-medium text-gray-700 mb-2">نوع السداد</label>
                                        <select id="paymentType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                            <option value="">اختر نوع السداد</option>
                                            <option value="bank">تحويل بنكي</option>
                                            <option value="wallet">محفظة إلكترونية</option>
                                            <option value="card">بطاقة ائتمان</option>
                                            <option value="cash">نقدي</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="totalAmount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ الإجمالي (﷼)</label>
                                        <input type="number" id="totalAmount" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Employee Details Row -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                <i class="fas fa-users"></i>
                                <h6 class="text-lg font-semibold">تفاصيل العمالة</h6>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="employeeCount" class="block text-sm font-medium text-gray-700 mb-2">عدد الموظفين</label>
                                    <input type="number" id="employeeCount" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                </div>
                                <div>
                                    <label for="workDays" class="block text-sm font-medium text-gray-700 mb-2">عدد الأيام</label>
                                    <input type="number" id="workDays" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                </div>
                                <div>
                                    <label for="dailyRate" class="block text-sm font-medium text-gray-700 mb-2">معدل اليومي (﷼)</label>
                                    <input type="number" id="dailyRate" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                </div>
                                <div>
                                    <label for="calculatedAmount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ المحسوب (﷼)</label>
                                    <input type="text" id="calculatedAmount" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
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
                                    <label for="discountAmount" class="block text-sm font-medium text-gray-700 mb-2">إجمالي الخصم (﷼)</label>
                                    <input type="number" id="discountAmount" min="0" step="0.01" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                </div>
                                <div>
                                    <label for="netAmount" class="block text-sm font-medium text-gray-700 mb-2">صافي المبلغ (﷼)</label>
                                    <input type="text" id="netAmount" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                                </div>
                                <div>
                                    <label for="numericDifference" class="block text-sm font-medium text-gray-700 mb-2">الفرق العددي</label>
                                    <input type="number" id="numericDifference" step="1" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الفرق</label>
                                    <div class="flex space-x-4 space-x-reverse mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="differenceType" value="positive" checked class="text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                            <span class="mr-2 text-green-600">زيادة</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="differenceType" value="negative" class="text-emerald-600 focus:ring-emerald-500 border-gray-300">
                                            <span class="mr-2 text-red-600">نقص</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status and Dates Row -->
                        <div class="space-y-4 mb-6">
                            <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                <i class="fas fa-clipboard-check"></i>
                                <h6 class="text-lg font-semibold">الحالات والتواريخ</h6>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="approvalStatus" class="block text-sm font-medium text-gray-700 mb-2">حالة الموافقة</label>
                                    <select id="approvalStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                        <option value="pending" selected>قيد المراجعة</option>
                                        <option value="approved">موافق عليه</option>
                                        <option value="rejected">مرفوض</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="approvalDate" class="block text-sm font-medium text-gray-700 mb-2">تاريخ الموافقة الإدارية</label>
                                    <input type="text" id="approvalDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors flatpickr">
                                </div>
                                <div>
                                    <label for="paymentStatus" class="block text-sm font-medium text-gray-700 mb-2">حالة السداد</label>
                                    <select id="paymentStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                        <option value="pending" selected>قيد الانتظار</option>
                                        <option value="paid">مسدد</option>
                                        <option value="failed">فاشل</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="paymentDate" class="block text-sm font-medium text-gray-700 mb-2">تاريخ السداد</label>
                                    <input type="text" id="paymentDate" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors flatpickr">
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information Row -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2 space-x-reverse text-emerald-600">
                                <i class="fas fa-sticky-note"></i>
                                <h6 class="text-lg font-semibold">معلومات إضافية</h6>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="delayDays" class="block text-sm font-medium text-gray-700 mb-2">أيام التأخير</label>
                                    <input type="number" id="delayDays" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors">
                                </div>
                                <div>
                                    <label for="paymentStatusOverall" class="block text-sm font-medium text-gray-700 mb-2">حالة الدفع النهائية</label>
                                    <select id="paymentStatusOverall" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" required>
                                        <option value="pending" selected>معلق</option>
                                        <option value="processing">قيد المعالجة</option>
                                        <option value="completed">مكتمل</option>
                                        <option value="rejected">مرفوض</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="orderNotes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                                <textarea id="orderNotes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-colors" placeholder="أي ملاحظات إضافية حول أمر الدفع..."></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn" style="background: var(--primary); color: white;" id="savePaymentOrderBtn">
                        <i class="fas fa-save me-2"></i>
                        حفظ أمر الدفع
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
            white-space: nowrap;
        }

        .table td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            border-color: #f3f4f6;
            white-space: nowrap;
        }

        .table tbody tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            font-size: 0.75rem;
            font-weight: 500;
        }

        .btn-sm {
            padding: 0.375rem 0.5rem;
            border-radius: 0.5rem;
            font-size: 0.8rem;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .form-check-inline {
            margin-right: 1rem;
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

            flatpickr('#orderDate', {
                ...flatpickrOptions,
                defaultDate: 'today'
            });

            flatpickr('#approvalDate', flatpickrOptions);
            flatpickr('#paymentDate', flatpickrOptions);
            // Set today's date as default for order date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('orderDate').value = today;

            // Calculate amount based on employee details
            const calculateAmount = () => {
                const employees = parseInt(document.getElementById('employeeCount').value) || 0;
                const days = parseInt(document.getElementById('workDays').value) || 0;
                const rate = parseFloat(document.getElementById('dailyRate').value) || 0;

                const calculatedAmount = employees * days * rate;
                document.getElementById('calculatedAmount').value = calculatedAmount.toFixed(2);

                // Auto-fill total amount if calculated amount is available
                if (calculatedAmount > 0 && !document.getElementById('totalAmount').value) {
                    document.getElementById('totalAmount').value = calculatedAmount.toFixed(2);
                }

                calculateNetAmount();
            };

            // Calculate net amount after discount
            const calculateNetAmount = () => {
                const totalAmount = parseFloat(document.getElementById('totalAmount').value) || 0;
                const discount = parseFloat(document.getElementById('discountAmount').value) || 0;

                const netAmount = totalAmount - discount;
                document.getElementById('netAmount').value = netAmount.toFixed(2);
            };

            // Event listeners for calculations
            document.getElementById('employeeCount').addEventListener('input', calculateAmount);
            document.getElementById('workDays').addEventListener('input', calculateAmount);
            document.getElementById('dailyRate').addEventListener('input', calculateAmount);
            document.getElementById('totalAmount').addEventListener('input', calculateNetAmount);
            document.getElementById('discountAmount').addEventListener('input', calculateNetAmount);

            // Auto-fill approval date when status changes to approved
            document.getElementById('approvalStatus').addEventListener('change', function() {
                if (this.value === 'approved') {
                    document.getElementById('approvalDate').value = today;
                } else {
                    document.getElementById('approvalDate').value = '';
                }
            });

            // Auto-fill payment date when status changes to paid
            document.getElementById('paymentStatus').addEventListener('change', function() {
                if (this.value === 'paid') {
                    document.getElementById('paymentDate').value = today;
                } else {
                    document.getElementById('paymentDate').value = '';
                }
            });

            // Save payment order button handler
            document.getElementById('savePaymentOrderBtn').addEventListener('click', function() {
                // Here you would typically send the form data to your backend
                alert('تم حفظ أمر الدفع بنجاح!');
                // Close the modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addPaymentOrderModal'));
                modal.hide();
            });
        });
    </script>
@endsection
