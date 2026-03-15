@extends('layouts.master')

@section('title', 'التحديثات المالية')
@section('page_title', 'التحديثات المالية')
@section('page_subtitle', 'عرض وإدارة جميع التحديثات المالية')

@push('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .stat-mini-card {
        background: white;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #edf2f7;
        transition: all 0.3s;
    }
    .stat-mini-card:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
        transform: translateY(-2px);
    }
    .stat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .stat-info h3 {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0;
        color: #1a202c;
    }
    .stat-info p {
        font-size: 0.85rem;
        color: #718096;
        margin: 0;
        font-weight: 500;
    }
    .table-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #edf2f7;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .custom-table {
        width: 100%;
        margin-bottom: 0;
    }
    .custom-table th {
        background: #f8fafc;
        padding: 18px 15px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #4a5568;
        border-bottom: 1px solid #edf2f7;
        text-align: right;
    }
    .custom-table td {
        padding: 18px 15px;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #2d3748;
        border-bottom: 1px solid #f7fafc;
    }
    .custom-table tbody tr:hover {
        background: #f8fafc;
    }
    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        background: white;
    }
    .btn-action:hover {
        background: #f8fafc;
        border-color: #cbd5e0;
        transform: translateY(-2px);
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
    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $updates->total() }}</h3>
                <p>إجمالي التحديثات</p>
            </div>
            <div class="stat-icon-box" style="background: #e6fffa; color: #319795;">
                <i class="bi bi-cash-coin"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $updates->where('update_type', 'payment_received')->count() }}</h3>
                <p>دفعات مستلمة</p>
            </div>
            <div class="stat-icon-box" style="background: #d1fae5; color: #059669;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ number_format($updates->sum('amount'), 0) }}</h3>
                <p>إجمالي المبالغ (ر.س)</p>
            </div>
            <div class="stat-icon-box" style="background: #dbeafe; color: #2563eb;">
                <i class="bi bi-currency-dollar"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $updates->where('status', 'active')->count() }}</h3>
                <p>تحديثات نشطة</p>
            </div>
            <div class="stat-icon-box" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-star-fill"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('financial-updates.index') }}" id="filterForm">
        <div class="bg-white rounded-xl border border-gray-100 p-3 mb-4 d-flex align-items-center gap-3">
            <div class="search-box ms-0" style="width: 300px; background: #fcfcfc; border: 1px solid #f0f0f0;">
                <i class="bi bi-search text-muted"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="البحث في التحديثات..." style="font-size: 0.85rem;">
            </div>
            
            <select name="type" class="form-select border-0 bg-light rounded-xl" style="width: 200px; font-size: 0.85rem;">
                <option value="">جميع الأنواع</option>
                <option value="payment_received" {{ request('type') == 'payment_received' ? 'selected' : '' }}>دفعة مستلمة</option>
                <option value="payment_delayed" {{ request('type') == 'payment_delayed' ? 'selected' : '' }}>تأخير في الدفع</option>
                <option value="invoice_adjustment" {{ request('type') == 'invoice_adjustment' ? 'selected' : '' }}>تعديل فاتورة</option>
                <option value="credit_note" {{ request('type') == 'credit_note' ? 'selected' : '' }}>إشعار دائن</option>
                <option value="discount_applied" {{ request('type') == 'discount_applied' ? 'selected' : '' }}>خصم مطبق</option>
                <option value="penalty_applied" {{ request('type') == 'penalty_applied' ? 'selected' : '' }}>غرامة مطبقة</option>
                <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>عام</option>
            </select>
            
            <select name="client_id" class="form-select border-0 bg-light rounded-xl" style="width: 200px; font-size: 0.85rem;">
                <option value="">جميع العملاء</option>
                @foreach(\App\Models\Client::all() as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
            
            <input type="date" name="start_date" class="form-control rounded-xl" style="width: 170px; font-size: 0.85rem;" value="{{ request('start_date') }}" placeholder="من تاريخ">
            <input type="date" name="end_date" class="form-control rounded-xl" style="width: 170px; font-size: 0.85rem;" value="{{ request('end_date') }}" placeholder="إلى تاريخ">
            
            <button type="submit" class="btn btn-primary rounded-xl px-4">
                <i class="bi bi-filter me-2"></i>تصفية
            </button>
            
            @if(request()->hasAny(['search', 'type', 'client_id', 'start_date', 'end_date']))
                <a href="{{ route('financial-updates.index') }}" class="btn btn-outline-secondary rounded-xl">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>

    <!-- Updates Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>التاريخ</th>
                        <th>العنوان</th>
                        <th>النوع</th>
                        <th>العميل</th>
                        <th>الفاتورة</th>
                        <th>المبلغ</th>
                        <th>الوصف</th>
                        <th>المستخدم</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($updates as $update)
                        <tr>
                            <td>
                                <span class="text-muted small">{{ $update->update_date->format('Y-m-d') }}</span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ $update->title }}</span>
                            </td>
                            <td>
                                @php
                                    $typeColors = [
                                        'payment_received' => ['bg' => '#d1fae5', 'text' => '#065f46'],
                                        'payment_delayed' => ['bg' => '#fee2e2', 'text' => '#991b1b'],
                                        'invoice_adjustment' => ['bg' => '#dbeafe', 'text' => '#1e40af'],
                                        'credit_note' => ['bg' => '#fef3c7', 'text' => '#92400e'],
                                        'discount_applied' => ['bg' => '#e0e7ff', 'text' => '#3730a3'],
                                        'penalty_applied' => ['bg' => '#fce7f3', 'text' => '#9f1239'],
                                        'general' => ['bg' => '#f3f4f6', 'text' => '#374151'],
                                    ];
                                    $color = $typeColors[$update->update_type] ?? $typeColors['general'];
                                @endphp
                                <span class="badge" style="background: {{ $color['bg'] }}; color: {{ $color['text'] }}; font-size: 0.7rem;">
                                    {{ $update->update_type }}
                                </span>
                            </td>
                            <td>
                                @if($update->client)
                                    <span class="text-muted">{{ $update->client->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($update->invoice)
                                    <a href="{{ route('invoices.show', $update->invoice_id) }}" class="text-decoration-none">
                                        {{ $update->invoice->number }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($update->amount)
                                    <span class="fw-bold text-success">{{ number_format($update->amount, 2) }} ر.س</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($update->description)
                                    <span class="text-muted small" title="{{ $update->description }}">
                                        {{ Str::limit($update->description, 50) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ $update->creator->name ?? 'غير معروف' }}</span>
                            </td>
                            <td class="text-center">
                                <button class="btn-action text-danger" onclick="deleteUpdate({{ $update->id }})" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                                    <div class="mb-3" style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-inbox fs-2 text-muted"></i>
                                    </div>
                                    <h6 class="text-muted mb-1">لا توجد تحديثات مالية</h6>
                                    <p class="text-muted small">ابدأ بإضافة تحديث مالي جديد</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($updates->hasPages())
        <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center" style="background: #f8fafc;">
            <div class="text-muted small">
                عرض {{ $updates->firstItem() ?? 0 }} إلى {{ $updates->lastItem() ?? 0 }} من {{ $updates->total() ?? 0 }} تحديث
            </div>
            {{ $updates->links() }}
        </div>
    @endif

    <!-- Financial Update Modal -->
    <x-financial-update-modal :clients="$clients" />
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
