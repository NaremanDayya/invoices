@extends('layouts.master')

@section('title', 'تقرير العمال - حماية الأجور')
@section('page_title', 'التقارير - كشف العمال (حماية الأجور)')
@section('page_subtitle', 'عرض قائمة العمال المسجلين في نظام حماية الأجور')

@section('page_actions')
    <button class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold" onclick="window.print()">
        <i class="bi bi-printer me-2"></i>
        <span>طباعة</span>
    </button>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted small mb-1">إجمالي العمال</p>
                    <h3 class="fw-bold mb-0 text-primary-accent">{{ $employees->count() }}</h3>
                </div>
                <div class="bg-primary-soft p-3 rounded-circle">
                    <i class="bi bi-shield-check text-primary-accent fs-3"></i>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">اسم العامل</th>
                                <th class="px-4 py-3 border-0">العميل</th>
                                <th class="px-4 py-3 border-0 text-center">أيام العمل</th>
                                <th class="px-4 py-3 border-0 text-center">تاريخ المباشرة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td class="px-4 py-3 fw-bold">{{ $employee->name }}</td>
                                    <td class="px-4 py-3 text-muted">{{ $employee->client->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center fw-bold text-primary">{{ $employee->work_days }} يوم</td>
                                    <td class="px-4 py-3 text-center text-muted small">{{ $employee->start_date }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">لا يوجد عمال لعرضهم</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
