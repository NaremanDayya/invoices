@extends('layouts.master')

@section('title', 'تفاصيل الفاتورة')
@section('page_title', 'تفاصيل الفاتورة')
@section('page_subtitle', 'عرض كامل تفاصيل الفاتورة')

@push('styles')
<style>
    .detail-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #edf2f7;
    }
    .detail-card h5 {
        font-weight: 700;
        margin-bottom: 20px;
        color: #2d3748;
        padding-bottom: 12px;
        border-bottom: 2px solid #edf2f7;
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid #f7fafc;
    }
    .detail-row:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: 600;
        color: #718096;
    }
    .detail-value {
        font-weight: 600;
        color: #2d3748;
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .status-badge.paid { background: #def7ec; color: #03543f; }
    .status-badge.pending { background: #fef3c7; color: #92400e; }
    .status-badge.late { background: #fde8e8; color: #9b1c1c; }
    .status-badge.cancelled { background: #f3f4f6; color: #4b5563; }
    .payment-item {
        background: #f8fafc;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 1px solid #edf2f7;
    }
    .credit-note-item {
        background: #fffaf0;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 1px solid #fbd38d;
    }
</style>
@endpush

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-pencil me-2"></i>تعديل
        </a>
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-arrow-right me-2"></i>رجوع
        </a>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- Right Column -->
        <div class="col-lg-8">
            <!-- Invoice Information -->
            <div class="detail-card">
                <h5><i class="bi bi-file-text me-2"></i>معلومات الفاتورة</h5>
                <div class="detail-row">
                    <span class="detail-label">رقم الفاتورة</span>
                    <span class="detail-value" style="color: #10a37f; font-family: 'Outfit', sans-serif;">{{ $invoice->number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">العميل</span>
                    <span class="detail-value">{{ $invoice->client->name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">الخدمة</span>
                    <span class="detail-value">{{ $invoice->service->name ?? '—' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">تاريخ الإصدار</span>
                    <span class="detail-value">{{ $invoice->generation_date->format('Y-m-d') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">تاريخ الاستحقاق</span>
                    <span class="detail-value">{{ $invoice->last_generation_date->format('Y-m-d') }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">حالة الدفع</span>
                    <span class="detail-value">
                        @php
                            $statusMap = [
                                'paid' => ['class' => 'paid', 'label' => 'مدفوعة', 'icon' => 'check-circle-fill'],
                                'pending' => ['class' => 'pending', 'label' => 'معلقة', 'icon' => 'hourglass-split'],
                                'late' => ['class' => 'late', 'label' => 'متأخرة', 'icon' => 'exclamation-circle-fill'],
                                'cancelled' => ['class' => 'cancelled', 'label' => 'ملغاة', 'icon' => 'x-circle-fill'],
                            ];
                            $s = $statusMap[$invoice->payment_status] ?? $statusMap['pending'];
                        @endphp
                        <span class="status-badge {{ $s['class'] }}">
                            <i class="bi bi-{{ $s['icon'] }}"></i>
                            {{ $s['label'] }}
                        </span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">حالة الفاتورة</span>
                    <span class="detail-value">{{ $invoice->invoice_status }}</span>
                </div>
            </div>

            <!-- Workforce Details -->
            <div class="detail-card">
                <h5><i class="bi bi-people me-2"></i>تفاصيل العمالة</h5>
                <div class="detail-row">
                    <span class="detail-label">عدد العمال</span>
                    <span class="detail-value">{{ $invoice->total_workers }} ({{ $invoice->workers_days ?? $invoice->work_days }} يوم)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">عدد المشرفين</span>
                    <span class="detail-value">{{ $invoice->total_supervisors }} ({{ $invoice->supervisors_days ?? $invoice->work_days }} يوم)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">عدد المدراء</span>
                    <span class="detail-value">{{ $invoice->total_managers }} ({{ $invoice->managers_days ?? $invoice->work_days }} يوم)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">عدد المستخدمين</span>
                    <span class="detail-value">{{ $invoice->total_users }} ({{ $invoice->users_days ?? $invoice->work_days }} يوم)</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">إجمالي العمالة</span>
                    <span class="detail-value fw-bold" style="color: #10a37f;">{{ $invoice->total_workforce }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">إجمالي أيام العمل</span>
                    <span class="detail-value fw-bold">
                        {{ 
                            ($invoice->total_workers * ($invoice->workers_days ?? $invoice->work_days)) +
                            ($invoice->total_supervisors * ($invoice->supervisors_days ?? $invoice->work_days)) +
                            ($invoice->total_managers * ($invoice->managers_days ?? $invoice->work_days)) +
                            ($invoice->total_users * ($invoice->users_days ?? $invoice->work_days))
                        }} يوم
                    </span>
                </div>
            </div>

            <!-- Payments History -->
            @if($invoice->payments->count() > 0)
            <div class="detail-card">
                <h5><i class="bi bi-cash-stack me-2"></i>سجل المدفوعات</h5>
                @foreach($invoice->payments as $payment)
                <div class="payment-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold" style="color: #10a37f;">{{ $payment->number }}</span>
                        <span class="badge {{ $payment->status === 'completed' ? 'bg-success' : ($payment->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                            {{ $payment->status === 'completed' ? 'مكتمل' : ($payment->status === 'pending' ? 'معلق' : 'ملغي') }}
                        </span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ $payment->payment_date->format('Y-m-d') }}</span>
                        <span class="fw-bold">{{ number_format($payment->amount, 2) }} ر.س</span>
                    </div>
                    <div class="text-muted small mt-1">{{ $payment->payment_method }}</div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Credit Notes -->
            @if($invoice->creditNotes->count() > 0)
            <div class="detail-card">
                <h5><i class="bi bi-file-earmark-text me-2"></i>الإشعارات الدائنة</h5>
                @foreach($invoice->creditNotes as $creditNote)
                <div class="credit-note-item">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">{{ $creditNote->number }}</span>
                        <span class="fw-bold text-warning">{{ number_format($creditNote->amount, 2) }} ر.س</span>
                    </div>
                    <div class="text-muted small">{{ $creditNote->description }}</div>
                    <div class="text-muted small mt-1">{{ $creditNote->reason }}</div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Notes -->
            @if($invoice->notes)
            <div class="detail-card">
                <h5><i class="bi bi-sticky-note me-2"></i>ملاحظات</h5>
                <p class="mb-0">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Left Column -->
        <div class="col-lg-4">
            <!-- Financial Summary -->
            <div class="detail-card">
                <h5><i class="bi bi-calculator me-2"></i>الملخص المالي</h5>
                <div class="detail-row">
                    <span class="detail-label">المبلغ الأساسي</span>
                    <span class="detail-value">{{ number_format($invoice->base_price, 2) }} ر.س</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">الضريبة ({{ $invoice->tax_rate }}%)</span>
                    <span class="detail-value">{{ number_format($invoice->tax_amount, 2) }} ر.س</span>
                </div>
                @if($invoice->amount_difference != 0)
                <div class="detail-row">
                    <span class="detail-label">فرق المبلغ</span>
                    <span class="detail-value {{ $invoice->amount_difference > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $invoice->amount_difference > 0 ? '+' : '' }}{{ number_format($invoice->amount_difference, 2) }} ر.س
                    </span>
                </div>
                @endif
                <div class="detail-row" style="border-top: 2px solid #edf2f7; padding-top: 16px; margin-top: 8px;">
                    <span class="detail-label fw-bold">الإجمالي</span>
                    <span class="detail-value fw-bold" style="font-size: 1.25rem; color: #10a37f;">
                        {{ number_format($invoice->total_price, 2) }} ر.س
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">المبلغ المدفوع</span>
                    <span class="detail-value text-success">{{ number_format($invoice->paid_amount, 2) }} ر.س</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">المبلغ المتبقي</span>
                    <span class="detail-value {{ $invoice->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($invoice->remaining_amount, 2) }} ر.س
                    </span>
                </div>
            </div>

            <!-- Client Information -->
            <div class="detail-card">
                <h5><i class="bi bi-person me-2"></i>معلومات العميل</h5>
                <div class="detail-row">
                    <span class="detail-label">الاسم</span>
                    <span class="detail-value">{{ $invoice->client->name }}</span>
                </div>
                @if($invoice->client->email)
                <div class="detail-row">
                    <span class="detail-label">البريد</span>
                    <span class="detail-value">{{ $invoice->client->email }}</span>
                </div>
                @endif
                @if($invoice->client->phone)
                <div class="detail-row">
                    <span class="detail-label">الهاتف</span>
                    <span class="detail-value" dir="ltr">{{ $invoice->client->phone }}</span>
                </div>
                @endif
                @if($invoice->client->address)
                <div class="detail-row">
                    <span class="detail-label">العنوان</span>
                    <span class="detail-value">{{ $invoice->client->address }}</span>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="detail-card">
                <h5><i class="bi bi-gear me-2"></i>الإجراءات</h5>
                <div class="d-grid gap-2">
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#creditNoteModal" onclick="openCreditNoteModal({{ $invoice->id }}, '{{ $invoice->number }}', {{ $invoice->total_price }})">
                        <i class="bi bi-file-earmark-plus me-2"></i>إضافة إشعار دائن
                    </button>
                    <a href="{{ route('payments.create') }}?invoice_id={{ $invoice->id }}" class="btn btn-success">
                        <i class="bi bi-cash-stack me-2"></i>إضافة دفعة
                    </a>
                    @if(!$invoice->is_cancelled)
                    <button class="btn btn-danger" onclick="confirmDelete({{ $invoice->id }})">
                        <i class="bi bi-trash me-2"></i>حذف الفاتورة
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Credit Notes Section -->
    <div class="row mt-4">
        <div class="col-12">
            @include('partials.credit-notes-history', ['invoice' => $invoice])
        </div>
    </div>

    <!-- Credit Note Modal -->
    @include('partials.credit-note-modal')
@endsection

@push('scripts')
<script>
function confirmDelete(invoiceId) {
    if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟ لا يمكن التراجع عن هذا الإجراء.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/invoices/${invoiceId}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
