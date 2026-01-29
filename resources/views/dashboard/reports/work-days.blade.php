@extends('layouts.master')

@section('title', 'تقرير أيام العمل')
@section('page_title', 'التقارير - كشف أيام العمل')
@section('page_subtitle', 'عرض أيام العمل لجميع الموظفين المصنفين تنازلياً')

@section('page_actions')
    <button class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold" onclick="window.print()">
        <i class="bi bi-printer me-2"></i>
        <span>طباعة</span>
    </button>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-muted small mb-1">إجمالي أيام العمل لجميع الموظفين</p>
                <h3 class="fw-bold mb-0 text-primary-accent">{{ $employees->sum('work_days') }} يوم</h3>
            </div>
        </div>

        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">اسم الموظف</th>
                                <th class="px-4 py-3 border-0">العميل</th>
                                <th class="px-4 py-3 border-0 text-center">نوع الملف</th>
                                <th class="px-4 py-3 border-0 text-center">أيام العمل</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td class="px-4 py-3 fw-bold">{{ $employee->name }}</td>
                                    <td class="px-4 py-3">{{ $employee->client->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center small text-muted">{{ $employee->file_type }}</td>
                                    <td class="px-4 py-3 text-center fw-bold text-primary">{{ $employee->work_days }} يوم</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">لا يوجد بيانات لعرضها</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
