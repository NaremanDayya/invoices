@extends('layouts.master')

@section('title', 'جميع الصلاحيات')
@section('page_title', 'جميع الصلاحيات')
@section('page_subtitle', 'عرض جميع الصلاحيات المتاحة في النظام')

@section('content')
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">الصلاحيات المتاحة في النظام</h4>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-right me-2"></i>
            العودة للأدوار
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th class="fw-bold">#</th>
                    <th class="fw-bold">اسم الصلاحية (عربي)</th>
{{--                    <th class="fw-bold">الاسم التقني (إنجليزي)</th>--}}
                    <th class="fw-bold">الوصف</th>
                    <th class="fw-bold text-center">الأدوار المرتبطة</th>
                </tr>
            </thead>
            <tbody>
                @foreach($permissions as $index => $permission)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-shield-check text-primary me-2"></i>
                            <span class="fw-semibold">{{ $permission->display_name_ar }}</span>
                        </div>
                    </td>
{{--                    <td>--}}
{{--                        <code class="bg-light px-2 py-1 rounded">{{ $permission->name }}</code>--}}
{{--                    </td>--}}
                    <td>
                        <small class="text-muted">{{ $permission->description ?? 'لا يوجد وصف' }}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge bg-info rounded-pill">
                            {{ $permission->roles->count() }} دور
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="alert alert-info mt-4 border-0 rounded-3">
        <div class="d-flex align-items-start">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
            <div>
                <h6 class="alert-heading fw-bold mb-2">ملاحظة هامة</h6>
                <p class="mb-0 small">
                    الصلاحيات المذكورة أعلاه هي الصلاحيات الأساسية في النظام.
                    دور <strong>المدير</strong> يمتلك جميع هذه الصلاحيات تلقائياً ولا يمكن تعديلها.
                    يمكنك تخصيص الصلاحيات للأدوار الأخرى حسب الحاجة.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
