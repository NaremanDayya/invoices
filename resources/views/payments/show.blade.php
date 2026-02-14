@extends('layouts.master')

@section('title', 'تفاصيل الدفعة')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">تفاصيل الدفعة #{{ $payment->number }}</h2>
            <p class="text-muted">معلومات كاملة عن الدفعة</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payments.index') }}" class="btn btn-secondary rounded-xl">
                <i class="bi bi-arrow-right me-2"></i>رجوع للقائمة
            </a>
            <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-primary rounded-xl">
                <i class="bi bi-pencil me-2"></i>تعديل
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Payment Details -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-gradient-to-r from-blue-50 to-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-receipt text-primary me-2"></i>معلومات الدفعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">رقم الدفعة</label>
                                <p class="fw-bold mb-0">{{ $payment->number }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">تاريخ الدفع</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-calendar3 text-primary me-1"></i>
                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}
                                    @if($payment->late_days > 0)
                                        <span class="badge bg-danger ms-2">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>متأخر {{ $payment->late_days }} يوم
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">المبلغ المدفوع</label>
                                <p class="fw-bold mb-0 text-success fs-4">
                                    {{ number_format($payment->amount, 0) }} ر.س
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">طريقة الدفع</label>
                                <p class="fw-bold mb-0">
                                    @php
                                        $methods = [
                                            'direct_bank_transfer' => ['icon' => 'bank', 'label' => 'تحويل بنكي مباشر'],
                                            'bank_wage_protection_transfer' => ['icon' => 'shield-check', 'label' => 'تحويل بنكي حماية الأجور'],
                                            'cash' => ['icon' => 'cash-coin', 'label' => 'نقدي'],
                                            'bank_transfer' => ['icon' => 'bank', 'label' => 'تحويل بنكي'],
                                            'check' => ['icon' => 'receipt', 'label' => 'شيك'],
                                            'credit_card' => ['icon' => 'credit-card', 'label' => 'بطاقة ائتمان'],
                                            'other' => ['icon' => 'three-dots', 'label' => 'أخرى'],
                                        ];
                                        $method = $methods[$payment->payment_method] ?? ['icon' => 'question-circle', 'label' => $payment->payment_method];
                                    @endphp
                                    <i class="bi bi-{{ $method['icon'] }} text-primary me-1"></i>
                                    {{ $method['label'] }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">حالة الدفع</label>
                                <p class="mb-0">
                                    @php
                                        $statusConfig = [
                                            'pending' => ['class' => 'bg-warning text-dark', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                                            'completed' => ['class' => 'bg-success text-white', 'icon' => 'check-circle', 'label' => 'مكتمل'],
                                            'cancelled' => ['class' => 'bg-danger text-white', 'icon' => 'x-circle', 'label' => 'ملغى'],
                                        ];
                                        $status = $statusConfig[$payment->status] ?? ['class' => 'bg-secondary', 'icon' => 'info-circle', 'label' => $payment->status];
                                    @endphp
                                    <span class="badge {{ $status['class'] }} rounded-pill px-3 py-2">
                                        <i class="bi bi-{{ $status['icon'] }} me-1"></i>{{ $status['label'] }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        @if($payment->reference_number)
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">رقم المرجع</label>
                                <p class="fw-bold mb-0">{{ $payment->reference_number }}</p>
                            </div>
                        </div>
                        @endif
                        @if($payment->bank_name)
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">اسم البنك</label>
                                <p class="fw-bold mb-0">{{ $payment->bank_name }}</p>
                            </div>
                        </div>
                        @endif
                        @if($payment->employees_count)
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">عدد الموظفين</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-people-fill text-primary me-1"></i>
                                    {{ $payment->employees_count }} موظف
                                </p>
                            </div>
                        </div>
                        @endif
                        @if($payment->work_days)
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">أيام العمل</label>
                                <p class="fw-bold mb-0">
                                    <i class="bi bi-calendar-check text-primary me-1"></i>
                                    {{ $payment->work_days }} يوم
                                </p>
                            </div>
                        </div>
                        @endif
                        @if($payment->notes)
                        <div class="col-12">
                            <div class="info-item">
                                <label class="text-muted small mb-1">ملاحظات</label>
                                <p class="mb-0">{{ $payment->notes }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Information -->
            @if($payment->invoice)
            <div class="card mb-4">
                <div class="card-header bg-gradient-to-r from-indigo-50 to-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-file-earmark-text text-indigo me-2"></i>معلومات الفاتورة المرتبطة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">رقم الفاتورة</label>
                                <p class="fw-bold mb-0">
                                    <a href="{{ route('invoices.show', $payment->invoice->id) }}" class="text-primary text-decoration-none">
                                        #{{ $payment->invoice->number }}
                                        <i class="bi bi-box-arrow-up-left ms-1"></i>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">إجمالي الفاتورة</label>
                                <p class="fw-bold mb-0">{{ number_format($payment->invoice->total_price, 0) }} ر.س</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">المبلغ المدفوع</label>
                                <p class="fw-bold mb-0 text-success">{{ number_format($payment->invoice->paid_amount, 0) }} ر.س</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="text-muted small mb-1">المبلغ المتبقي</label>
                                <p class="fw-bold mb-0 text-warning">{{ number_format($payment->invoice->remaining_amount, 0) }} ر.س</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Client Information -->
            @if($payment->invoice && $payment->invoice->client)
            <div class="card mb-4">
                <div class="card-header bg-gradient-to-r from-green-50 to-white border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-person text-success me-2"></i>معلومات العميل
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-circle bg-success bg-opacity-10 text-success mx-auto mb-2" style="width: 80px; height: 80px; line-height: 80px; border-radius: 50%; font-size: 2rem;">
                            {{ mb_substr($payment->invoice->client->name, 0, 1) }}
                        </div>
                        <h6 class="fw-bold mb-0">{{ $payment->invoice->client->name }}</h6>
                    </div>
                    <hr>
                    <div class="space-y-2">
                        @if($payment->invoice->client->email)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-envelope text-muted me-2"></i>
                            <small>{{ $payment->invoice->client->email }}</small>
                        </div>
                        @endif
                        @if($payment->invoice->client->phone)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-telephone text-muted me-2"></i>
                            <small>{{ $payment->invoice->client->phone }}</small>
                        </div>
                        @endif
                        @if($payment->invoice->client->address)
                        <div class="d-flex align-items-start mb-2">
                            <i class="bi bi-geo-alt text-muted me-2 mt-1"></i>
                            <small>{{ $payment->invoice->client->address }}</small>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-light border-bottom">
                    <h5 class="mb-0 fw-bold">
                        <i class="bi bi-gear text-secondary me-2"></i>الإجراءات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-outline-primary rounded-xl">
                            <i class="bi bi-pencil me-2"></i>تعديل الدفعة
                        </a>
                        @if($payment->status === 'pending')
                        <form action="{{ route('payments.confirm', $payment->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success rounded-xl w-100">
                                <i class="bi bi-check-circle me-2"></i>تأكيد الدفعة
                            </button>
                        </form>
                        @endif
                        <button type="button" class="btn btn-outline-danger rounded-xl" onclick="confirmDelete({{ $payment->id }})">
                            <i class="bi bi-trash me-2"></i>حذف الدفعة
                        </button>
                        <a href="{{ route('payments.print', $payment->id) }}" target="_blank" class="btn btn-outline-secondary rounded-xl">
                            <i class="bi bi-printer me-2"></i>طباعة الإيصال
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deletePaymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    تأكيد الحذف
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-3">هل أنت متأكد من حذف هذه الدفعة؟</p>
                <div class="alert alert-warning mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>تحذير:</strong> سيتم تحديث حالة الفاتورة المرتبطة تلقائياً بعد الحذف.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-xl" data-bs-dismiss="modal">إلغاء</button>
                <form id="deletePaymentForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-xl">
                        <i class="bi bi-trash me-2"></i>حذف الدفعة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(paymentId) {
        const form = document.getElementById('deletePaymentForm');
        form.action = `/payments/${paymentId}`;
        const modal = new bootstrap.Modal(document.getElementById('deletePaymentModal'));
        modal.show();
    }
</script>
@endpush

@section('styles')
<style>
    .card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    .info-item {
        padding: 0.5rem 0;
    }
    .avatar-circle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>
@endsection
