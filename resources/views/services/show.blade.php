@extends('layouts.master')

@section('title', 'تفاصيل الخدمة')
@section('page_title', $service->name)
@section('page_subtitle', 'عرض تفاصيل الخدمة')

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('services.edit', $service) }}" class="btn btn-primary rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-pencil"></i>
            <span>تعديل</span>
        </a>
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-arrow-right"></i>
            <span>رجوع</span>
        </a>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Service Information -->
        <div class="col-lg-8">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4">معلومات الخدمة</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">اسم الخدمة</label>
                        <p class="fw-bold mb-0">{{ $service->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">نوع الخدمة</label>
                        <p class="mb-0">
                            <span class="badge bg-light text-dark rounded-pill px-3">{{ $service->service_type }}</span>
                        </p>
                    </div>
                    <div class="col-12">
                        <label class="text-muted small mb-1">الوصف</label>
                        <p class="mb-0">{{ $service->description ?? '—' }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">تاريخ الإنشاء</label>
                        <p class="mb-0">{{ $service->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small mb-1">آخر تحديث</label>
                        <p class="mb-0">{{ $service->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Service Details -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <h5 class="fw-bold mb-4">تفاصيل الخدمة</h5>
                @if($service->serviceDetails->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-3 border-0">#</th>
                                    <th class="px-3 py-3 border-0">اسم التفصيل</th>
                                    <th class="px-3 py-3 border-0 text-center">أيام العمل</th>
                                    <th class="px-3 py-3 border-0 text-center">عدد الأيام</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($service->serviceDetails as $index => $detail)
                                    <tr>
                                        <td class="px-3 py-3">{{ $index + 1 }}</td>
                                        <td class="px-3 py-3 fw-bold">{{ $detail->name }}</td>
                                        <td class="px-3 py-3 text-center">
                                            @if($detail->has_work_days)
                                                <span class="badge bg-success rounded-pill">نعم</span>
                                            @else
                                                <span class="badge bg-secondary rounded-pill">لا</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($detail->has_work_days && $detail->work_days)
                                                <span class="badge bg-primary rounded-pill px-3">{{ $detail->work_days }} يوم</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        لا توجد تفاصيل مضافة لهذه الخدمة
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics -->
        <div class="col-lg-4">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-4">إحصائيات</h5>
                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-xl">
                        <div>
                            <p class="text-muted small mb-1">عدد التفاصيل</p>
                            <h4 class="fw-bold mb-0">{{ $service->serviceDetails->count() }}</h4>
                        </div>
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-list-ul fs-4"></i>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded-xl">
                        <div>
                            <p class="text-muted small mb-1">عدد الفواتير</p>
                            <h4 class="fw-bold mb-0">{{ $service->invoices()->count() }}</h4>
                        </div>
                        <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-file-earmark-text fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>

            @if($service->service_details)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                    <h5 class="fw-bold mb-3">بيانات JSON</h5>
                    <pre class="bg-light p-3 rounded" style="font-size: 0.85rem; max-height: 300px; overflow-y: auto;">{{ json_encode($service->service_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </div>
    </div>
@endsection
