@extends('layouts.master')
@section('title', 'إدارة الفواتير')
@section('content')
    <div>
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0" style="color: var(--primary);">
                <i class="fas fa-file-invoice me-2"></i>
                إدارة الفواتير
            </h2>
            <button class="btn" style="background: var(--primary); color: white;" wire:click="$set('showModal', true)">
                <i class="fas fa-plus me-2"></i>
                فاتورة جديدة
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100" style="border-left: 4px solid var(--primary);">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="card-title text-muted mb-2">إجمالي الفواتير</h6>
                                <h3 class="mb-0" style="color: var(--primary);">{{ $stats['total'] ?? 0 }}</h3>
                            </div>
                            <div class="bg-light rounded-circle p-3">
                                <i class="fas fa-receipt" style="color: var(--primary);"></i>
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
                                <h6 class="card-title text-muted mb-2">مدفوعة</h6>
                                <h3 class="mb-0" style="color: #10b981;">{{ $stats['paid'] ?? 0 }}</h3>
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
                                <h3 class="mb-0" style="color: #f59e0b;">{{ $stats['pending'] ?? 0 }}</h3>
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
                                <h6 class="card-title text-muted mb-2">متأخرة</h6>
                                <h3 class="mb-0" style="color: #ef4444;">{{ $stats['late'] ?? 0 }}</h3>
                            </div>
                            <div class="bg-light rounded-circle p-3">
                                <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="بحث في الفواتير..." wire:model.live="search">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="statusFilter">
                            <option value="">كل الحالات</option>
                            <option value="paid">مدفوعة</option>
                            <option value="pending">قيد الانتظار</option>
                            <option value="overdue">متأخرة</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" wire:model.live="clientFilter">
                            <option value="">كل العملاء</option>
                            @foreach($clients as $client)
                                <option value="{{ $client }}">{{ $client }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" placeholder="من تاريخ" wire:model.live="startDate">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" placeholder="إلى تاريخ" wire:model.live="endDate">
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                            <i class="fas fa-refresh"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: var(--light);">
                        <tr>
                            <th class="border-0">رقم الفاتورة</th>
                            <th class="border-0">العميل</th>
                            <th class="border-0">تاريخ الإصدار</th>
                            <th class="border-0">المبلغ الإجمالي</th>
                            <th class="border-0">حالة السداد</th>
                            <th class="border-0 text-center">الإجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $invoice->invoice_number }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $invoice->client_name }}</div>
                                    <small class="text-muted">{{ $invoice->client_email }}</small>
                                </td>
                                <td>{{ $invoice->invoice_date }}</td>
                                <td>
                                    <div class="fw-bold" style="color: var(--primary);">{{ number_format($invoice->total_amount, 2) }} ﷼</div>
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'paid' => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'check-circle'],
                                            'pending' => ['bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'clock'],
                                            'overdue' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'exclamation-triangle']
                                        ];
                                        $status = $statusColors[$invoice->payment_status] ?? $statusColors['pending'];
                                    @endphp
                                    <span class="badge rounded-pill" style="background: {{ $status['bg'] }}; color: {{ $status['color'] }};">
                                        <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                                        {{ $invoice->payment_status === 'paid' ? 'مدفوعة' : ($invoice->payment_status === 'pending' ? 'قيد الانتظار' : 'متأخرة') }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm" style="background: var(--primary); color: white;" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                عرض {{ $invoices->firstItem() ?? 0 }} إلى {{ $invoices->lastItem() ?? 0 }} من {{ $invoices->total() ?? 0 }} فاتورة
            </div>
            {{ $invoices->links() }}
        </div>

        <!-- Modal -->
        @if($showModal)
            <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1040;"></div>
            <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 1050; width: 90%; max-width: 800px;">
                <div class="modal-content">
                    <div class="modal-header" style="background: var(--primary); color: white;">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2"></i>
                            إضافة فاتورة جديدة
                        </h5>
                        <button type="button" class="btn-close" style="filter: invert(1);" wire:click="$set('showModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="saveInvoice">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">اسم العميل</label>
                                        <input type="text" class="form-control" wire:model="client_name">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">رقم الفاتورة</label>
                                        <input type="text" class="form-control" wire:model="invoice_number">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المبلغ الإجمالي</label>
                                        <input type="number" class="form-control" wire:model="total_amount">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">حالة السداد</label>
                                        <select class="form-control" wire:model="payment_status">
                                            <option value="pending">قيد الانتظار</option>
                                            <option value="paid">مدفوعة</option>
                                            <option value="overdue">متأخرة</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">إلغاء</button>
                        <button type="button" class="btn" style="background: var(--primary); color: white;" wire:click="saveInvoice">
                            <i class="fas fa-save me-2"></i>
                            حفظ الفاتورة
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
