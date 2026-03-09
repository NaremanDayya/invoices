@extends('layouts.master')

@section('title', 'التحديثات المالية')
@section('page_title', 'التحديثات المالية')
@section('page_subtitle', 'عرض وإدارة جميع التحديثات المالية')

@push('styles')
<style>
    .update-card {
        border-left: 4px solid var(--primary);
        transition: all 0.3s;
    }
    .update-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: translateX(-5px);
    }
    .update-type-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
</style>
@endpush

@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#financialUpdateModal">
            <i class="bi bi-plus-lg"></i>
            <span>تحديث مالي جديد</span>
        </button>
    </div>
@endsection

@section('content')
    <!-- Filters -->
    <form method="GET" action="{{ route('financial-updates.index') }}" class="mb-4">
        <div class="bg-white rounded-xl border border-gray-100 p-3 d-flex align-items-center gap-3">
            <select name="type" class="form-select border-0 bg-light rounded-xl" style="width: 200px;" onchange="this.form.submit()">
                <option value="">جميع الأنواع</option>
                <option value="payment_received" {{ request('type') == 'payment_received' ? 'selected' : '' }}>دفعة مستلمة</option>
                <option value="payment_delayed" {{ request('type') == 'payment_delayed' ? 'selected' : '' }}>تأخير في الدفع</option>
                <option value="invoice_adjustment" {{ request('type') == 'invoice_adjustment' ? 'selected' : '' }}>تعديل فاتورة</option>
                <option value="credit_note" {{ request('type') == 'credit_note' ? 'selected' : '' }}>إشعار دائن</option>
                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>عام</option>
            </select>
            
            <input type="date" name="start_date" class="form-control rounded-xl" value="{{ request('start_date') }}" placeholder="من تاريخ">
            <input type="date" name="end_date" class="form-control rounded-xl" value="{{ request('end_date') }}" placeholder="إلى تاريخ">
            
            <button type="submit" class="btn btn-primary rounded-xl px-4">
                <i class="bi bi-filter me-2"></i>تصفية
            </button>
            
            @if(request()->hasAny(['type', 'start_date', 'end_date']))
                <a href="{{ route('financial-updates.index') }}" class="btn btn-outline-secondary rounded-xl">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Updates List -->
    <div class="row">
        @forelse($updates as $update)
            <div class="col-12 mb-3">
                <div class="card update-card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h5 class="mb-2 fw-bold">{{ $update->title }}</h5>
                                <div class="d-flex gap-3 align-items-center mb-2">
                                    <span class="update-type-badge" style="background: #e0f2fe; color: #0369a1;">
                                        <i class="bi bi-tag me-1"></i>
                                        {{ $update->update_type }}
                                    </span>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        {{ $update->update_date->format('Y-m-d') }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="bi bi-person me-1"></i>
                                        {{ $update->creator->name ?? 'غير معروف' }}
                                    </small>
                                </div>
                                
                                @if($update->description)
                                    <p class="mb-2 text-muted">{{ $update->description }}</p>
                                @endif
                                
                                <div class="d-flex gap-3 small">
                                    @if($update->invoice)
                                        <a href="{{ route('invoices.show', $update->invoice_id) }}" class="text-decoration-none">
                                            <i class="bi bi-file-earmark-text me-1"></i>
                                            فاتورة: {{ $update->invoice->number }}
                                        </a>
                                    @endif
                                    
                                    @if($update->client)
                                        <span class="text-muted">
                                            <i class="bi bi-building me-1"></i>
                                            {{ $update->client->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="text-end">
                                @if($update->amount)
                                    <div class="badge bg-success fs-6 mb-2">
                                        {{ number_format($update->amount, 2) }} ر.س
                                    </div>
                                @endif
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <button class="dropdown-item" onclick="deleteUpdate({{ $update->id }})">
                                                <i class="bi bi-trash text-danger me-2"></i>حذف
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">لا توجد تحديثات مالية</h5>
                    <p class="text-muted">ابدأ بإضافة تحديث مالي جديد</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($updates->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $updates->links() }}
        </div>
    @endif

    <!-- Financial Update Modal -->
    <x-financial-update-modal />
@endsection

@push('scripts')
<script>
function deleteUpdate(updateId) {
    if (!confirm('هل أنت متأكد من حذف هذا التحديث؟')) return;
    
    fetch(`/api/financial-updates/${updateId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            window.location.reload();
        } else {
            alert('حدث خطأ أثناء الحذف');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال بالخادم');
    });
}
</script>
@endpush
