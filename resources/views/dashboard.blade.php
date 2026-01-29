@extends('layouts.master')

@section('title', 'التقارير التحليلية')
@section('page_title', 'التقارير')
@section('page_subtitle', 'إنشاء وتصدير التقارير التحليلية')

@push('styles')
    <style>
        .report-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #edf2f7;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .report-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
            border-color: var(--primary-accent);
        }
        .report-icon-container {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .report-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 8px;
        }
        .report-desc {
            color: #718096;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .btn-create-report {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #4a5568;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
        }
        .btn-create-report:hover {
            background: #edf2f7;
            border-color: #cbd5e0;
            color: #2d3748;
        }
        .summary-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #edf2f7;
            text-align: center;
        }
        .summary-val {
            font-size: 2rem;
            font-weight: 800;
            color: #1a202c;
            margin-bottom: 5px;
        }
        .summary-label {
            color: #718096;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <div class="row g-4 mb-5">
        <!-- Invoices Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #e6fffa; color: #319795;">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                    </div>
                    <h3 class="report-title">تقرير الفواتير</h3>
                    <p class="report-desc">إحصائيات شاملة عن الفواتير الصادرة والملغية والمدفوعة</p>
                </div>
                <a href="{{ route('dashboard.reports.issued-invoices') }}" class="btn-create-report text-decoration-none">
                    <i class="bi bi-plus-circle"></i>
                    إنشاء التقرير
                </a>
            </div>
        </div>

        <!-- Customers Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #fffaf0; color: #dd6b20;">
                        <i class="bi bi-people"></i>
                    </div>
                    <h3 class="report-title">إحصائيات العملاء</h3>
                    <p class="report-desc">قائمة شاملة بجميع العملاء وبيانات التواصل والفوترة</p>
                </div>
                <a href="{{ route('clients.index') }}" class="btn-create-report text-decoration-none">
                    <i class="bi bi-eye"></i>
                    عرض العملاء
                </a>
            </div>
        </div>

        <!-- Revenue Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #f0fff4; color: #38a169;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <h3 class="report-title">تقرير الإيرادات (لنا)</h3>
                    <p class="report-desc">متابعة الفروقات السعرية التي تم تحصيلها لصالحنا</p>
                </div>
                <a href="{{ route('dashboard.reports.financial-for-us') }}" class="btn-create-report text-decoration-none">
                    <i class="bi bi-plus-circle"></i>
                    إنشاء التقرير
                </a>
            </div>
        </div>

        <!-- Late Invoices Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #fff5f5; color: #e53e3e;">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h3 class="report-title">تقرير التأخيرات</h3>
                    <p class="report-desc">الفواتير المتأخرة وتحليل أسباب التأخير في السداد</p>
                </div>
                <a href="{{ route('dashboard.reports.late-invoices') }}" class="btn-create-report text-decoration-none">
                    <i class="bi bi-plus-circle"></i>
                    إنشاء التقرير
                </a>
            </div>
        </div>

        <!-- Employees (Users) Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #ebf8ff; color: #3182ce;">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <h3 class="report-title">تقرير الموظفين</h3>
                    <p class="report-desc">كشف الموظفين الإداريين والميدانيين (غير حماية الأجور)</p>
                </div>
                <a href="{{ route('dashboard.reports.users') }}" class="btn-create-report text-decoration-none">
                    <i class="bi bi-plus-circle"></i>
                    إنشاء التقرير
                </a>
            </div>
        </div>

        <!-- Workers (WPS) Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #faf5ff; color: #805ad5;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h3 class="report-title">تقرير حماية الأجور</h3>
                    <p class="report-desc">كشف العمال المسجلين في نظام حماية الأجور (WPS)</p>
                </div>
                <a href="{{ route('dashboard.reports.workers') }}" class="btn-create-report text-decoration-none">
                    <i class="bi bi-plus-circle"></i>
                    إنشاء التقرير
                </a>
            </div>
        </div>

        <!-- Supervisors/Managers Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #fffaf0; color: #d69e2e;">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <h3 class="report-title">كشف الإشراف والإدارة</h3>
                    <p class="report-desc">إحصائيات المشرفين والمدراء الموزعين على العملاء</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dashboard.reports.supervisors') }}" class="btn-create-report text-decoration-none flex-grow-1">
                        المشرفين
                    </a>
                    <a href="{{ route('dashboard.reports.managers') }}" class="btn-create-report text-decoration-none flex-grow-1">
                        المدراء
                    </a>
                </div>
            </div>
        </div>

        <!-- Work Days Report -->
        <div class="col-md-6 col-lg-4">
            <div class="report-card">
                <div>
                    <div class="report-icon-container" style="background-color: #f7fafc; color: #4a5568;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h3 class="report-title">تقرير أيام العمل</h3>
                    <p class="report-desc">تحليل أيام العمل المنفذة من قبل الموظفين والعمال</p>
                </div>
                <a href="{{ route('dashboard.reports.work-days') }}" class="btn-create-report text-decoration-none">
                    <i class="bi bi-plus-circle"></i>
                    عرض التقرير
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Section -->
    <div class="mt-5">
        <h4 class="fw-bold mb-4">ملخص سريع</h4>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-val text-primary">{{ $statistics['total_invoices'] }}</div>
                    <div class="summary-label">التقارير هذا الشهر</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-val">اليوم</div>
                    <div class="summary-label">آخر تقرير</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-val text-warning">3</div>
                    <div class="summary-label">تقارير مجدولة</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="summary-card">
                    <div class="summary-val text-success">الفواتير</div>
                    <div class="summary-label">الأكثر طلباً</div>
                </div>
            </div>
        </div>
    </div>
@endsection
