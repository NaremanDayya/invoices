@extends('layouts.master')

@section('title', 'المشاريع')
@section('page_title', 'المشاريع')
@section('page_subtitle', 'عرض المشاريع والموظفين المرتبطين بها')

@push('styles')
<style>
    .project-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        transition: all 0.3s;
    }
    .project-card:hover {
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }
    .project-header {
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 15px;
        margin-bottom: 15px;
    }
    .employee-row {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 8px;
        background: #f8f9fa;
        transition: background 0.2s;
    }
    .employee-row:hover {
        background: #e9ecef;
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .stat-box.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-box.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-box.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
</style>
@endpush

@section('page_actions')
    <a href="{{ route('invoices.index') }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
        <i class="bi bi-arrow-right me-2"></i>رجوع للفواتير
    </a>
@endsection

@section('content')
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box info">
                <p class="mb-1">إجمالي المشاريع</p>
                <h3 class="mb-0">{{ $stats['total_projects'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p class="mb-1">إجمالي الموظفين</p>
                <h3 class="mb-0">{{ $stats['total_employees'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box success">
                <p class="mb-1">إجمالي المدفوع</p>
                <h3 class="mb-0">{{ number_format($stats['total_paid'], 0) }}</h3>
                <p class="mb-0">ر.س</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box warning">
                <p class="mb-1">إجمالي المتبقي</p>
                <h3 class="mb-0">{{ number_format($stats['total_remaining'], 0) }}</h3>
                <p class="mb-0">ر.س</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('projects.index') }}">
                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold">بحث عن مشروع</label>
                        <input type="text" name="search" class="form-control" value="{{ $search }}" placeholder="اسم المشروع...">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold">تصفية حسب العميل</label>
                        <select name="client_id" class="form-select">
                            <option value="">جميع العملاء</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>
                                    {{ $client->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-2"></i>بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Projects List -->
    @if($projects->count() > 0)
        @foreach($projects as $project)
            <div class="project-card">
                <div class="project-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h4 class="mb-2 fw-bold text-primary">
                                <i class="bi bi-folder-fill me-2"></i>{{ $project['name'] }}
                            </h4>
                            <div class="d-flex gap-3 text-muted">
                                <span><i class="bi bi-building me-1"></i>{{ $project['client_name'] }}</span>
                                <span><i class="bi bi-file-text me-1"></i>{{ $project['invoice_number'] }}</span>
                                <span><i class="bi bi-people me-1"></i>{{ $project['employees_count'] }} موظف</span>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="d-flex justify-content-end gap-3">
                                <div>
                                    <small class="text-muted d-block">إجمالي الرواتب</small>
                                    <strong class="text-primary fs-5">{{ number_format($project['total_salaries'], 0) }} ر.س</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">المدفوع</small>
                                    <strong class="text-success fs-5">{{ number_format($project['total_paid'], 0) }} ر.س</strong>
                                </div>
                                <div>
                                    <small class="text-muted d-block">المتبقي</small>
                                    <strong class="text-danger fs-5">{{ number_format($project['total_remaining'], 0) }} ر.س</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employees Table -->
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>اسم الموظف</th>
                                <th class="text-center">أيام العمل</th>
                                <th class="text-end">الراتب</th>
                                <th class="text-end">المدفوع</th>
                                <th class="text-end">المتبقي</th>
                                <th class="text-center">نوع الراتب</th>
                                <th class="text-center">الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($project['employees'] as $employee)
                                <tr class="employee-row">
                                    <td class="fw-bold">
                                        <i class="bi bi-person-circle me-2 text-primary"></i>{{ $employee['name'] }}
                                    </td>
                                    <td class="text-center">{{ $employee['work_days'] }}</td>
                                    <td class="text-end text-primary fw-bold">{{ number_format($employee['salary'], 0) }} ر.س</td>
                                    <td class="text-end text-success fw-bold">{{ number_format($employee['paid'], 0) }} ر.س</td>
                                    <td class="text-end text-danger fw-bold">{{ number_format($employee['remaining'], 0) }} ر.س</td>
                                    <td class="text-center">
                                        @if($employee['salary_type'] === 'wps')
                                            <span class="badge bg-info">WPS</span>
                                        @else
                                            <span class="badge bg-secondary">شهري</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($employee['payment_status'] === 'paid')
                                            <span class="badge bg-success">مدفوع</span>
                                        @elseif($employee['payment_status'] === 'partially_paid')
                                            <span class="badge bg-warning">مدفوع جزئياً</span>
                                        @else
                                            <span class="badge bg-danger">غير مدفوع</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="2">الإجمالي:</td>
                                <td class="text-end text-primary">{{ number_format($project['total_salaries'], 0) }} ر.س</td>
                                <td class="text-end text-success">{{ number_format($project['total_paid'], 0) }} ر.س</td>
                                <td class="text-end text-danger">{{ number_format($project['total_remaining'], 0) }} ر.س</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-folder-x display-1 text-muted"></i>
                <h5 class="text-muted mt-3">لا توجد مشاريع</h5>
                <p class="text-muted">لم يتم العثور على أي مشاريع تحتوي على موظفين</p>
            </div>
        </div>
    @endif
@endsection
