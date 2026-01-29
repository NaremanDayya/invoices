@extends('layouts.master')

@section('title', 'تقرير الموظفين')
@section('page_title', 'التقارير - كشف الموظفين')
@section('page_subtitle', 'عرض قائمة الموظفين (غير حماية الأجور)')

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
                <p class="text-muted small mb-1">إجمالي الموظفين في الكشف</p>
                <h3 class="fw-bold mb-0 text-primary-accent">{{ $employees->count() }}</h3>
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
                                <th class="px-4 py-3 border-0">رقم الهوية</th>
                                <th class="px-4 py-3 border-0 text-center">نوع الملف</th>
                                <th class="px-4 py-3 border-0 text-center">أيام العمل</th>
                                <th class="px-4 py-3 border-0 text-center">تاريخ المباشرة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employees as $employee)
                                <tr>
                                    <td class="px-4 py-3 fw-bold">{{ $employee->name }}</td>
                                    <td class="px-4 py-3 text-muted">{{ $employee->client->name ?? '—' }}</td>
                                    <td class="px-4 py-3 small">{{ $employee->id_number }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-info-soft text-info rounded-pill px-3">{{ $employee->file_type }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center fw-bold">{{ $employee->work_days }}</td>
                                    <td class="px-4 py-3 text-center text-muted small">{{ $employee->start_date }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">لا يوجد موظفين لعرضهم</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
