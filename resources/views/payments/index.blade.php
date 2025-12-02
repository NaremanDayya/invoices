@extends('layouts.master')

@section('title', 'إدارة المدفوعات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0" style="color: var(--primary);">
            <i class="fas fa-credit-card me-2"></i>
            إدارة المدفوعات
        </h2>
        <a href="{{ route('payments.create') }}" class="btn" style="background: var(--primary); color: white;">
            <i class="fas fa-plus me-2"></i>
            إضافة دفعة جديدة
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted mb-2">إجمالي المدفوعات</h6>
                            <h3 class="mb-0" style="color: var(--primary);">{{ $stats['total'] }}</h3>
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
                            <h3 class="mb-0" style="color: #10b981;">{{ $stats['completed'] }}</h3>
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
                            <h3 class="mb-0" style="color: #f59e0b;">{{ $stats['pending'] }}</h3>
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
                            <h3 class="mb-0" style="color: #ef4444;">{{ $stats['cancelled'] }}</h3>
                        </div>
                        <div class="bg-light rounded-circle p-3">
                            <i class="fas fa-times-circle" style="color: #ef4444;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('payments.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="ابحث برقم الدفع أو الوصف..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">جميع الحالات</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغى</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="fas fa-search me-1"></i>
                            بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: var(--light);">
                    <tr>
                        <th class="border-0">رقم الدفعة</th>
                        <th class="border-0">العميل</th>
                        <th class="border-0">رقم الفاتورة</th>
                        <th class="border-0">تاريخ الدفع</th>
                        <th class="border-0">المبلغ</th>
                        <th class="border-0">طريقة الدفع</th>
                        <th class="border-0">الحالة</th>
                        <th class="border-0">رقم المرجع</th>
                        <th class="border-0 text-center">الإجراءات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($payments as $payment)
                        <tr>
                            <td class="fw-bold">{{ $payment->number }}</td>
                            <td>
                                <div class="fw-bold">{{ $payment->invoice->client->name }}</div>
                                <small class="text-muted">{{ $payment->invoice->client->contact_person ?? '---' }}</small>
                            </td>
                            <td class="fw-bold">{{ $payment->invoice->number }}</td>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td class="fw-bold" style="color: var(--primary);">{{ $payment->formatted_amount }}</td>
                            <td>{!! $payment->method_badge !!}</td>
                            <td>{!! $payment->status_badge !!}</td>
                            <td>{{ $payment->reference_number ?? '---' }}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('payments.show', $payment) }}" class="btn btn-sm btn-outline-primary" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-outline-warning" title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>

{{--                                    @if($payment->status != 'completed')--}}
{{--                                        <form action="{{ route('payments.confirm', $payment) }}" method="POST" style="display: inline;">--}}
{{--                                            @csrf--}}
{{--                                            <button type="submit" class="btn btn-sm btn-outline-success" title="تأكيد الدفع">--}}
{{--                                                <i class="fas fa-check"></i>--}}
{{--                                            </button>--}}
{{--                                        </form>--}}
{{--                                    @endif--}}
                                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف" onclick="return confirm('هل أنت متأكد من الحذف؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <br>
                                لا توجد مدفوعات
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
