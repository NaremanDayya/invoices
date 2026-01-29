@extends('layouts.master')

@section('title', 'تقرير المدراء')
@section('page_title', 'التقارير - كشف المدراء')
@section('page_subtitle', 'عرض إحصائيات المدراء حسب العملاء')

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
                <p class="text-muted small mb-1">إجمالي عدد المدراء</p>
                <h3 class="fw-bold mb-0 text-primary-accent">{{ $totalManagers }}</h3>
            </div>
        </div>

        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">العميل</th>
                                <th class="px-4 py-3 border-0 text-center">عدد المدراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($managersData as $data)
                                <tr>
                                    <td class="px-4 py-3 fw-bold">{{ $data->client->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center fw-bold text-primary">{{ $data->total_managers }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-5 text-muted">لا يوجد بيانات لعرضها</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
