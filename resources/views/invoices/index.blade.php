
@extends('layouts.master')

@section('title', 'إدارة الفواتير')
@push('styles')
    <style>
        .modal-backdrop {
            z-index: 1040;
        }
        .modal {
            z-index: 1050;
        }
    </style>
@endpush
@section('content')
    <div>
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0" style="color: var(--primary);">
                <i class="fas fa-file-invoice me-2"></i>
                إدارة الفواتير
            </h2>
            <div class="d-flex gap-2">
                @include('components.export-dropdown')
            <button type="button" class="btn" style="background: var(--primary); color: white;" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                <i class="fas fa-plus me-2"></i>
                فاتورة جديدة
            </button>
        </div>
        </div>
        <!-- Hidden content for PDF export -->
        <div id="export-invoice-content" class="export-content">
            <!-- PDF Header -->
            <div class="pdf-header">
                <div class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                    <div class="d-flex flex-column align-items-center text-center mx-auto">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                        <h2 class="header-text">تقرير الفواتير</h2>
                        <div class="report-info">
                            <p>تاريخ التقرير: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
                            <p>إجمالي الفواتير: {{ $invoices->total() ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Summary -->
            <div class="summary-box">
                <h5>ملخص الإحصائيات:</h5>
                <div class="row">
                    <div class="col-3">
                        <p><strong>إجمالي الفواتير:</strong> {{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="col-3">
                        <p><strong>مدفوعة:</strong> {{ $stats['paid'] ?? 0 }}</p>
                    </div>
                    <div class="col-3">
                        <p><strong>قيد الانتظار:</strong> {{ $stats['pending'] ?? 0 }}</p>
                    </div>
                    <div class="col-3">
                        <p><strong>متأخرة:</strong> {{ $stats['late'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards & Filters (same as before) -->
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

        <!-- Filters Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">بحث سريع</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            class="block w-full pr-10 pl-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                            placeholder="ابحث في الفواتير..."
                            wire:model.live="search"
                        >
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">حالة السداد</label>
                    <select
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                        wire:model.live="statusFilter"
                    >
                        <option value="">كل الحالات</option>
                        <option value="paid">مدفوعة</option>
                        <option value="pending">قيد الانتظار</option>
                        <option value="overdue">متأخرة</option>
                    </select>
                </div>

                <!-- Client Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العميل</label>
                    <select
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                        wire:model.live="clientFilter"
                    >
                        <option value="">كل العملاء</option>
                        @foreach($clients as $client)
                            <option value="{{ $client }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                    <input
                        type="text"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                        wire:model.live="startDate" id="start_date" placeholder="من تاريخ"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                    <input
                        type="text"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                        wire:model.live="endDate" id="end_date" placeholder="إلى تاريخ"
                    >
                </div>

                <!-- Reset Button -->
                <div>
                    <button
                        class="w-full px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 font-medium"
                        wire:click="resetFilters"
                    >
                        <i class="fas fa-refresh text-gray-500"></i>
                        <span>إعادة تعيين</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive" id="invoices-table-container">
                    <table class="table table-hover mb-0" id="invoices-table">
                        <thead style="background: var(--light);">
                        <tr>
                            <th class="border-0">رقم الفاتورة</th>
                            <th class="border-0">العميل</th>
                            <th class="border-0">تاريخ الإصدار</th>
                            <th class="border-0">أيام تأخير الإصدار</th>
                            <th class="border-0">نوع الخدمة</th>
                            <th class="border-0">إجمالي العمالة</th>
                            <th class="border-0">أيام العمل</th>
                            <th class="border-0">المبلغ قبل الضريبة</th>
                            <th class="border-0">الضريبة</th>
                            <th class="border-0">المبلغ الإجمالي</th>
                            <th class="border-0">فرق المبلغ</th>
                            <th class="border-0">حالة السداد</th>
                            <th class="border-0">تاريخ السداد</th>
                            <th class="border-0">أيام تأخير السداد</th>
                            <th class="border-0">حالة الفاتورة</th>
                            <th class="border-0 text-center">الإجراءات</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoices as $invoice)
                            <tr>
                                {{-- رقم الفاتورة --}}
                                <td>{{ $invoice->number }}</td>

                                {{-- العميل --}}
                                <td>
                                    <div class="fw-bold">{{ $invoice->client->name ?? 'غير معروف' }}</div>
                                </td>

                                {{-- تاريخ الإصدار --}}
                                <td>{{ $invoice->generation_date ? \Carbon\Carbon::parse($invoice->generation_date)->format('Y-m-d') : '—' }}</td>

                                {{-- أيام تأخير الإصدار --}}
                                <td>
                                    @php
                                        $delayDays = $invoice->last_generation_date
                                            ? \Carbon\Carbon::parse($invoice->generation_date)->diffInDays($invoice->last_generation_date)
                                            : 0;
                                    @endphp
                                    {{ $delayDays }}
                                </td>

                                {{-- نوع الخدمة --}}
                                <td>{{ $invoice->service->name ?? '—' }}</td>

                                {{-- إجمالي العمالة --}}
                                <td>
                                    <span title="Workers">W: {{ $invoice->total_workers }}</span> |
                                    <span title="Supervisors">S: {{ $invoice->total_supervisors }}</span> |
                                    <span title="Managers">M: {{ $invoice->total_managers }}</span> |
                                    <span title="Users">U: {{ $invoice->total_users }}</span>
                                </td>
                                {{-- أيام العمل --}}
                                <td>{{ $invoice->work_days }}</td>

                                {{-- المبلغ قبل الضريبة --}}
                                <td>{{ number_format($invoice->base_price, 2) }} ﷼</td>

                                {{-- الضريبة --}}
                                <td>{{ number_format($invoice->tax_amount, 2) }} ﷼</td>

                                {{-- المبلغ الإجمالي --}}
                                <td>{{ number_format($invoice->total_price, 2) }} ﷼</td>

                                {{-- فرق المبلغ --}}
                                <td>{{ number_format($invoice->price_difference ?? 0, 2) }} ﷼</td>

                                {{-- حالة السداد --}}
                                <td>
                                    @php
                                        $statusColors = [
                                            'paid' => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'check-circle'],
                                            'pending' => ['bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'clock'],
                                            'late' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'exclamation-triangle']
                                        ];
                                        $status = $statusColors[$invoice->payment_status] ?? $statusColors['pending'];
                                    @endphp
                                    <span class="badge rounded-pill" style="background: {{ $status['bg'] }}; color: {{ $status['color'] }};">
                <i class="fas fa-{{ $status['icon'] }} me-1"></i>
                {{ $invoice->payment_status === 'paid' ? 'مدفوعة' : ($invoice->payment_status === 'pending' ? 'قيد الانتظار' : 'متأخرة') }}
            </span>
                                </td>

                                {{-- تاريخ السداد --}}
                                <td>{{ $invoice->payment_date ? \Carbon\Carbon::parse($invoice->payment_date)->format('Y-m-d') : '—' }}</td>

                                {{-- أيام تأخير السداد --}}
                                <td>
                                    @php
                                        $lateDays = ($invoice->payment_date && $invoice->due_date)
                                            ? \Carbon\Carbon::parse($invoice->due_date)->diffInDays($invoice->payment_date)
                                            : 0;
                                    @endphp
                                    {{ $lateDays }}
                                </td>

                                {{-- حالة الفاتورة (حسب نوعها) --}}
                                <td>{{ $invoice->invoice_status ?? '—' }}</td>

                                {{-- الإجراءات --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Credit Note Button -->
                                        <button class="btn btn-sm btn-outline-warning credit-note-btn"
                                                title="إشعار دائن"
                                                data-invoice-id="{{ $invoice->id }}"
                                                data-invoice-number="{{ $invoice->number }}"
                                                data-total-amount="{{ $invoice->total_price }}">
                                            <i class="fas fa-file-invoice-dollar"></i>
                                        </button>

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

                    <!-- Hidden content for PDF export -->
                    <div id="export-invoice-content" class="export-content" style="display: none;">
                        <!-- PDF Header -->
                        <div class="pdf-header">
                            <div class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                                <div class="d-flex flex-column align-items-center text-center mx-auto">
                                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                                    <h2 class="header-text">تقرير الفواتير</h2>
                                    <div class="report-info">
                                        <p>تاريخ التقرير: {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</p>
                                        <p>إجمالي الفواتير: {{ $invoices->total() ?? 0 }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Summary -->
                        <div class="summary-box">
                            <h5>ملخص الإحصائيات:</h5>
                            <div class="row">
                                <div class="col-3">
                                    <p><strong>إجمالي الفواتير:</strong> {{ $stats['total'] ?? 0 }}</p>
                                </div>
                                <div class="col-3">
                                    <p><strong>مدفوعة:</strong> {{ $stats['paid'] ?? 0 }}</p>
                                </div>
                                <div class="col-3">
                                    <p><strong>قيد الانتظار:</strong> {{ $stats['pending'] ?? 0 }}</p>
                                </div>
                                <div class="col-3">
                                    <p><strong>متأخرة:</strong> {{ $stats['late'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- PDF Footer -->
                        <div class="pdf-footer">
                            <p>جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- Credit Note Modal -->
        <div class="modal fade" id="creditNoteModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            إضافة إشعار دائن
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="creditNoteForm" method="POST" action="{{ route('invoices.add-credit-note') }}">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="invoice_id" id="invoice_id">

                            <!-- Invoice Info -->
                            <div class="alert alert-info">
                                <div class="d-flex justify-content-between">
                                    <strong>رقم الفاتورة:</strong>
                                    <span id="invoice_number_display"></span>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <strong>المبلغ الإجمالي:</strong>
                                    <span id="total_amount_display"></span> ﷼
                                </div>
                            </div>

                            <!-- Credit Amount -->
                            <div class="mb-3">
                                <label for="credit_amount" class="form-label">مبلغ الإشعار الدائن <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" id="credit_amount" name="credit_amount" required>
                                <div class="form-text">أدخل المبلغ المراد إضافته كإشعار دائن</div>
                            </div>

                            <!-- Credit Type -->
                            <div class="mb-3">
                                <label for="credit_note_type" class="form-label">نوع الإشعار الدائن <span class="text-danger">*</span></label>
                                <select class="form-select" id="credit_note_type" name="credit_note_type" required>
                                    <option value="">اختر النوع</option>
                                    <option value="credit_note">إشعار دائن (لنا)</option>
                                    <option value="indebted_poems">قصائد مديونة (للشركة)</option>
                                </select>
                            </div>

                            <!-- Reason -->
                            <div class="mb-3">
                                <label for="credit_reason" class="form-label">سبب الإشعار الدائن <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="credit_reason" name="credit_reason" rows="3" required placeholder="أدخل سبب إضافة الإشعار الدائن..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>
                                حفظ الإشعار الدائن
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Create Invoice Modal -->
        <div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2"></i>
                            إضافة فاتورة جديدة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="createInvoiceForm" method="POST" action="{{ route('invoices.store') }}">
                        @csrf
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">

                            <!-- Client Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <i class="fas fa-user me-2"></i>
                                    معلومات العميل
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <label class="form-label">اختر العميل <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="clientSearchInput" placeholder="ابحث عن عميل..." autocomplete="off">
                                                <input type="hidden" name="client_id" id="selectedClientId" required>
                                                <div id="clientDropdown" class="list-group position-absolute w-100 shadow" style="display:none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                    <!-- Options will be populated by JS -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">البريد الإلكتروني</label>
                                            <input type="email" class="form-control bg-light" id="clientEmail" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">الهاتف</label>
                                            <input type="text" class="form-control bg-light" id="clientPhone" readonly>
                                        </div>
                                        <div class="col-md-12 mt-2">
                                            <label class="form-label">العنوان</label>
                                            <textarea class="form-control bg-light" id="clientAddress" rows="2" readonly></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Information -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <i class="fas fa-file-invoice me-2"></i>
                                    معلومات الفاتورة
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">رقم الفاتورة <span class="text-danger">*</span></label>
                                            <input type="text" name="number" class="form-control" value="" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">تاريخ الإصدار <span class="text-danger">*</span></label>
                                            <input type="date" name="generation_date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">تاريخ الاستحقاق <span class="text-danger">*</span></label>
                                            <input type="date" name="last_generation_date" class="form-control" value="{{ now()->addDays(30)->format('Y-m-d') }}" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">نوع الخدمة <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="text" class="form-control" id="serviceSearchInput" placeholder="ابحث عن خدمة..." autocomplete="off">
                                                <input type="hidden" name="service_id" id="selectedServiceId" required>
                                                <div id="serviceDropdown" class="list-group position-absolute w-100 shadow" style="display:none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                    <!-- Options will be populated by JS -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Workforce Details -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <i class="fas fa-users me-2"></i>
                                    تفاصيل العمالة
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <label class="form-label">عدد العمال <span class="text-danger">*</span></label>
                                            <input type="number" name="total_workers" id="total_workers" class="form-control" min="0" value="0" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">عدد المشرفين <span class="text-danger">*</span></label>
                                            <input type="number" name="total_supervisors" id="total_supervisors" class="form-control" min="0" value="0" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">عدد المدراء <span class="text-danger">*</span></label>
                                            <input type="number" name="total_managers" id="total_managers" class="form-control" min="0" value="0" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">عدد المستخدمين <span class="text-danger">*</span></label>
                                            <input type="number" name="total_users" id="total_users" class="form-control" min="0" value="0" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">إجمالي العمالة</label>
                                            <input type="text" id="total_workforce_display" class="form-control bg-light fw-bold" value="0" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Financial Details -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <i class="fas fa-calculator me-2"></i>
                                    التفاصيل المالية
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-4">
                                            <label class="form-label">أيام العمل <span class="text-danger">*</span></label>
                                            <input type="number" name="work_days" id="work_days" class="form-control" min="1" value="1" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">الأجر اليومي (﷼) <span class="text-danger">*</span></label>
                                            <input type="number" name="daily_rate" id="daily_rate" class="form-control" min="0" step="0.01" value="0" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">نسبة الضريبة (%) <span class="text-danger">*</span></label>
                                            <input type="number" name="tax_rate" id="tax_rate" class="form-control" min="0" max="100" step="0.1" value="15" required>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">المبلغ قبل الضريبة (﷼)</label>
                                            <input type="text" id="subtotal_display" class="form-control bg-light fw-bold" value="0.00" readonly>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">قيمة الضريبة (﷼)</label>
                                            <input type="text" id="tax_amount_display" class="form-control bg-light fw-bold" value="0.00" readonly>
                                        </div>
                                        <div class="col-md-4 mb-2">
                                            <label class="form-label">المبلغ الإجمالي (﷼)</label>
                                            <input type="text" id="total_amount_display" class="form-control bg-light fw-bold" value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment & Status -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <i class="fas fa-credit-card me-2"></i>
                                    حالة السداد
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">حالة السداد <span class="text-danger">*</span></label>
                                            <select name="payment_status" class="form-select" required>
                                                <option value="pending">قيد الانتظار</option>
                                                <option value="paid">مدفوعة</option>
                                                <option value="overdue">متأخرة</option>
                                                <option value="late">متأخرة (متابعة)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">تاريخ السداد</label>
                                            <input type="date" name="payment_date" class="form-control">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">حالة الفاتورة <span class="text-danger">*</span></label>
                                            <select name="invoice_status" id="invoice_status" class="form-select" required onchange="toggleCustomStatus()">
                                                <option value="">اختر حالة الفاتورة</option>
                                                <option value="رواتب">رواتب</option>
                                                <option value="عمولات">عمولات</option>
                                                <option value="عمل اضافي">عمل اضافي</option>
                                                <option value="رواتب-احتضان قانوني">رواتب-احتضان قانوني</option>
                                                <option value="مصاريف قانونية- احتضان قانوني">مصاريف قانونية- احتضان قانوني</option>
                                                <option value="يوزرات">يوزرات</option>
                                                <option value="ملغية">ملغية</option>
                                                <option value="ملغية -احتضان قانوني">ملغية -احتضان قانوني</option>
                                                <option value="بروموتر">بروموتر</option>
                                                <option value="زيارة مستقلة">زيارة مستقلة</option>
                                                <option value="other">أخرى (أضف حالة جديدة)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 mb-3" id="custom_status_container" style="display: none;">
                                            <label class="form-label">الحالة المخصصة <span class="text-danger">*</span></label>
                                            <input type="text" name="custom_status" class="form-control" placeholder="أدخل الحالة الجديدة للفاتورة">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div class="card">
                                <div class="card-header bg-light">
                                    <i class="fas fa-sticky-note me-2"></i>
                                    ملاحظات إضافية
                                </div>
                                <div class="card-body">
                                    <textarea name="notes" class="form-control" rows="3" placeholder="أي ملاحظات إضافية حول الفاتورة..."></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                حفظ الفاتورة
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Add Client Modal -->
        <div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-user-plus me-2"></i>
                            إضافة عميل جديد
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addClientForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">اسم العميل <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">الهاتف</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">العنوان</label>
                                <textarea name="address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                حفظ العميل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Service Modal -->
        <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-plus-circle me-2"></i>
                            إضافة خدمة جديدة
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addServiceForm" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">اسم الخدمة <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                                <div class="invalid-feedback" id="serviceNameError"></div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">الوصف</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                حفظ الخدمة
                            </button>
                        </div>
                    </form>
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
    </div>
@endsection
@push('scripts')
            @include('components.export-scripts')

            <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupExportDropdown('exportDropdown', 'invoices-table-container', 'invoices-table', 'تقرير_الفواتير');

            // Get modal element
            const creditNoteModalElement = document.getElementById('creditNoteModal');
            if (!creditNoteModalElement) {
                console.error('Credit note modal element not found');
                return;
            }

            const creditNoteModal = new bootstrap.Modal(creditNoteModalElement);
            const creditNoteForm = document.getElementById('creditNoteForm');

            // Handle credit note button click using event delegation
            document.addEventListener('click', function(e) {
                if (e.target.closest('.credit-note-btn')) {
                    const button = e.target.closest('.credit-note-btn');
                    const invoiceId = button.getAttribute('data-invoice-id');
                    const invoiceNumber = button.getAttribute('data-invoice-number');
                    const totalAmount = button.getAttribute('data-total-amount');

                    console.log('Button clicked:', { invoiceId, invoiceNumber, totalAmount }); // Debug log

                    // Set form values
                    document.getElementById('invoice_id').value = invoiceId;
                    document.getElementById('invoice_number_display').textContent = invoiceNumber;
                    document.getElementById('total_amount_display').textContent = totalAmount;

                    // Set max amount for credit (can't exceed total amount)
                    document.getElementById('credit_amount').max = totalAmount;
                    document.getElementById('credit_amount').placeholder = `الحد الأقصى: ${totalAmount} ﷼`;

                    // Reset form
                    if (creditNoteForm) {
                        creditNoteForm.reset();
                    }

                    // Show modal
                    creditNoteModal.show();
                }
            });

            // Handle form submission
            if (creditNoteForm) {
                creditNoteForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);

                    // Add loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';
                    submitBtn.disabled = true;

                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                showAlert('success', data.message);
                                creditNoteModal.hide();

                                // Reload page to see changes
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                showAlert('error', data.message || 'حدث خطأ أثناء الحفظ');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showAlert('error', 'حدث خطأ أثناء الاتصال بالخادم');
                        })
                        .finally(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                });
            }
// Create Invoice Modal Functionality
            document.addEventListener('DOMContentLoaded', function() {
                // Get create invoice modal element
                const createInvoiceModalElement = document.getElementById('createInvoiceModal');
                if (createInvoiceModalElement) {
                    const createInvoiceModal = new bootstrap.Modal(createInvoiceModalElement);
                    const createInvoiceForm = document.getElementById('createInvoiceForm');

                    // Update client info when client is selected
                    const clientSelect = document.getElementById('clientSelect');
                    if (clientSelect) {
                        clientSelect.addEventListener('change', function() {
                            const selectedOption = this.options[this.selectedIndex];
                            document.getElementById('clientEmail').value = selectedOption.getAttribute('data-email') || '';
                            document.getElementById('clientPhone').value = selectedOption.getAttribute('data-phone') || '';
                            document.getElementById('clientAddress').value = selectedOption.getAttribute('data-address') || '';
                        });
                    }

                    // Workforce calculation
                    function calculateTotalWorkforce() {
                        const workers = parseInt(document.getElementById('total_workers').value) || 0;
                        const supervisors = parseInt(document.getElementById('total_supervisors').value) || 0;
                        const managers = parseInt(document.getElementById('total_managers').value) || 0;
                        const users = parseInt(document.getElementById('total_users').value) || 0;

                        const total = workers + supervisors + managers + users;
                        document.getElementById('total_workforce_display').value = total;

                        calculateFinancials();
                    }
// Inline create: Clients
                    document.addEventListener('DOMContentLoaded', function() {
                        const addClientForm = document.getElementById('addClientForm');
                        if (addClientForm) {
                            addClientForm.addEventListener('submit', async function (e) {
                                e.preventDefault();

                                const formData = new FormData(this);
                                const submitBtn = this.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;

                                // Add loading state
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';
                                submitBtn.disabled = true;

                                // Clear previous errors
                                document.getElementById('nameError').textContent = '';

                                try {
                                    const resp = await fetch("{{ route('invoices.add-client') }}", {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    });

                                    const data = await resp.json();

                                    if (data.success) {
                                        // Append to select and select it
                                        const clientSelect = document.getElementById('clientSelect');
                                        const opt = document.createElement('option');
                                        opt.value = data.client.id;
                                        opt.textContent = data.client.name;
                                        opt.setAttribute('data-email', data.client.email || '');
                                        opt.setAttribute('data-phone', data.client.phone || '');
                                        opt.setAttribute('data-address', data.client.address || '');
                                        clientSelect.appendChild(opt);
                                        clientSelect.value = data.client.id;

                                        // Trigger change event to update client info
                                        clientSelect.dispatchEvent(new Event('change'));

                                        // Reset form
                                        addClientForm.reset();

                                        // Close modal
                                        const modalEl = document.getElementById('addClientModal');
                                        const modal = bootstrap.Modal.getInstance(modalEl);
                                        modal.hide();

                                        // Show success message
                                        showAlert('success', data.message || 'تم إضافة العميل بنجاح');
                                    } else {
                                        // Handle validation errors
                                        if (data.errors) {
                                            if (data.errors.name) {
                                                document.getElementById('nameError').textContent = data.errors.name[0];
                                            }
                                        } else {
                                            showAlert('error', data.message || 'تعذر إضافة العميل');
                                        }
                                    }
                                } catch (err) {
                                    console.error('Error:', err);
                                    showAlert('error', 'حدث خطأ أثناء إضافة العميل');
                                } finally {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                }
                            });
                        }

                        // Inline create: Services
                        const addServiceForm = document.getElementById('addServiceForm');
                        if (addServiceForm) {
                            addServiceForm.addEventListener('submit', async function (e) {
                                e.preventDefault();

                                const formData = new FormData(this);
                                const submitBtn = this.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;

                                // Add loading state
                                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';
                                submitBtn.disabled = true;

                                // Clear previous errors
                                document.getElementById('serviceNameError').textContent = '';

                                try {
                                    const resp = await fetch("{{ route('invoices.add-service') }}", {
                                        method: 'POST',
                                        headers: {
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        },
                                        body: formData
                                    });

                                    const data = await resp.json();

                                    if (data.success) {
                                        // Append to select and select it
                                        const serviceSelect = document.getElementById('serviceSelect');
                                        const opt = document.createElement('option');
                                        opt.value = data.service.id;
                                        opt.textContent = data.service.name;
                                        serviceSelect.appendChild(opt);
                                        serviceSelect.value = data.service.id;

                                        // Reset form
                                        addServiceForm.reset();

                                        // Close modal
                                        const modalEl = document.getElementById('addServiceModal');
                                        const modal = bootstrap.Modal.getInstance(modalEl);
                                        modal.hide();

                                        // Show success message
                                        showAlert('success', data.message || 'تم إضافة الخدمة بنجاح');
                                    } else {
                                        // Handle validation errors
                                        if (data.errors) {
                                            if (data.errors.name) {
                                                document.getElementById('serviceNameError').textContent = data.errors.name[0];
                                            }
                                        } else {
                                            showAlert('error', data.message || 'تعذر إضافة الخدمة');
                                        }
                                    }
                                } catch (err) {
                                    console.error('Error:', err);
                                    showAlert('error', 'حدث خطأ أثناء إضافة الخدمة');
                                } finally {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                }
                            });
                        }
                    });
                    // Financial calculation
                    function calculateFinancials() {
                        const totalWorkforce = parseInt(document.getElementById('total_workforce_display').value) || 0;
                        const workDays = parseInt(document.getElementById('work_days').value) || 0;
                        const dailyRate = parseFloat(document.getElementById('daily_rate').value) || 0;
                        const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;

                        const subtotal = totalWorkforce * workDays * dailyRate;
                        const taxAmount = (subtotal * taxRate) / 100;
                        const total = subtotal + taxAmount;

                        document.getElementById('subtotal_display').value = subtotal.toFixed(2);
                        document.getElementById('tax_amount_display').value = taxAmount.toFixed(2);
                        document.getElementById('total_amount_display').value = total.toFixed(2);
                    }

                    // Event listeners for workforce inputs
                    ['total_workers', 'total_supervisors', 'total_managers', 'total_users'].forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.addEventListener('input', calculateTotalWorkforce);
                        }
                    });

                    // Event listeners for financial inputs
                    ['work_days', 'daily_rate', 'tax_rate'].forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.addEventListener('input', calculateFinancials);
                        }
                    });

                    // Handle form submission
                    if (createInvoiceForm) {
                        createInvoiceForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            const formData = new FormData(this);

                            // Add loading state
                            const submitBtn = this.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> جاري الحفظ...';
                            submitBtn.disabled = true;

                            fetch(this.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        showAlert('success', data.message || 'تم إضافة الفاتورة بنجاح');
                                        createInvoiceModal.hide();

                                        // Reset form
                                        createInvoiceForm.reset();

                                        // Reload page to see changes
                                        setTimeout(() => {
                                            window.location.reload();
                                        }, 1500);
                                    } else {
                                        showAlert('error', data.message || 'حدث خطأ أثناء حفظ الفاتورة');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    showAlert('error', 'حدث خطأ أثناء الاتصال بالخادم');
                                })
                                .finally(() => {
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                });
                        });
                    }

                    // Reset form when modal is hidden
                    createInvoiceModalElement.addEventListener('hidden.bs.modal', function () {
                        createInvoiceForm.reset();
                        calculateTotalWorkforce();
                        calculateFinancials();
                    });

                    // Initialize calculations
                    calculateTotalWorkforce();
                    calculateFinancials();
                }

                // Toggle custom status function
                window.toggleCustomStatus = function() {
                    const invoiceStatus = document.getElementById('invoice_status');
                    const customStatusContainer = document.getElementById('custom_status_container');

                    if (invoiceStatus && customStatusContainer) {
                        if (invoiceStatus.value === 'other') {
                            customStatusContainer.style.display = 'block';
                        } else {
                            customStatusContainer.style.display = 'none';
                        }
                    }
                };
            });
            function showAlert(type, message) {
                // Create a better alert system
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
                alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
                document.body.appendChild(alertDiv);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 5000);
            }
        });
    </script>
    <script>
        function toggleCustomStatus() {
            const invoiceStatus = document.getElementById('invoice_status');
            const customStatusContainer = document.getElementById('custom_status_container');

            if (invoiceStatus.value === 'other') {
                customStatusContainer.style.display = 'block';
            } else {
                customStatusContainer.style.display = 'none';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleCustomStatus();
        });
    </script>

    <script>
        // Autocomplete Logic
        const clients = @json($clients);
        const services = @json($services);

        // Client Autocomplete
        const clientInput = document.getElementById('clientSearchInput');
        const clientDropdown = document.getElementById('clientDropdown');
        const selectedClientId = document.getElementById('selectedClientId');

        // Initialize client input if editing (optional, but good practice)
        // For create modal, it starts empty.

        clientInput.addEventListener('input', function() {
            const search = this.value.toLowerCase();
            clientDropdown.innerHTML = '';

            if (search.length < 1) {
                clientDropdown.style.display = 'none';
                return;
            }

            const filtered = clients.filter(c => c.name.toLowerCase().includes(search));

            if (filtered.length > 0) {
                filtered.forEach(c => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = c.name;
                    item.onclick = (e) => {
                        e.preventDefault();
                        selectClient(c);
                    };
                    clientDropdown.appendChild(item);
                });
            }

            // Add "Add New" option
            const addNewItem = document.createElement('a');
            addNewItem.href = '#';
            addNewItem.className = 'list-group-item list-group-item-action text-success fw-bold';
            addNewItem.innerHTML = `<i class="fas fa-plus-circle me-1"></i> إضافة عميل جديد: "${this.value}"`;
            addNewItem.onclick = (e) => {
                e.preventDefault();
                openAddClientModal(this.value);
            };
            clientDropdown.appendChild(addNewItem);

            clientDropdown.style.display = 'block';
        });

        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (clientInput && !clientInput.contains(e.target) && !clientDropdown.contains(e.target)) {
                clientDropdown.style.display = 'none';
            }
            if (serviceInput && !serviceInput.contains(e.target) && !serviceDropdown.contains(e.target)) {
                serviceDropdown.style.display = 'none';
            }
        });

        function selectClient(client) {
            clientInput.value = client.name;
            selectedClientId.value = client.id;
            clientDropdown.style.display = 'none';

            // Update info fields
            document.getElementById('clientEmail').value = client.email || '';
            document.getElementById('clientPhone').value = client.phone || '';
            document.getElementById('clientAddress').value = client.address || '';
        }

        function openAddClientModal(name) {
            const modalEl = document.getElementById('addClientModal');
            const modal = new bootstrap.Modal(modalEl);
            modalEl.querySelector('[name="name"]').value = name;
            modal.show();
            clientDropdown.style.display = 'none';
        }

        // Service Autocomplete
        const serviceInput = document.getElementById('serviceSearchInput');
        const serviceDropdown = document.getElementById('serviceDropdown');
        const selectedServiceId = document.getElementById('selectedServiceId');

        serviceInput.addEventListener('input', function() {
            const search = this.value.toLowerCase();
            serviceDropdown.innerHTML = '';

            if (search.length < 1) {
                serviceDropdown.style.display = 'none';
                return;
            }

            const filtered = services.filter(s => s.name.toLowerCase().includes(search));

            if (filtered.length > 0) {
                filtered.forEach(s => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = s.name;
                    item.onclick = (e) => {
                        e.preventDefault();
                        selectService(s);
                    };
                    serviceDropdown.appendChild(item);
                });
            }

            // Add "Add New" option
            const addNewItem = document.createElement('a');
            addNewItem.href = '#';
            addNewItem.className = 'list-group-item list-group-item-action text-success fw-bold';
            addNewItem.innerHTML = `<i class="fas fa-plus-circle me-1"></i> إضافة خدمة جديدة: "${this.value}"`;
            addNewItem.onclick = (e) => {
                e.preventDefault();
                openAddServiceModal(this.value);
            };
            serviceDropdown.appendChild(addNewItem);

            serviceDropdown.style.display = 'block';
        });

        function selectService(service) {
            serviceInput.value = service.name;
            selectedServiceId.value = service.id;
            serviceDropdown.style.display = 'none';
        }

        function openAddServiceModal(name) {
            const modalEl = document.getElementById('addServiceModal');
            const modal = new bootstrap.Modal(modalEl);
            modalEl.querySelector('[name="name"]').value = name;
            modal.show();
            serviceDropdown.style.display = 'none';
        }

        // Workforce calculation
        const workersInput = document.getElementById('total_workers');
        const supervisorsInput = document.getElementById('total_supervisors');
        const managersInput = document.getElementById('total_managers');
        const usersInput = document.getElementById('total_users');
        const workforceDisplay = document.getElementById('total_workforce_display');

        function calculateTotalWorkforce() {
            const workers = parseInt(workersInput.value) || 0;
            const supervisors = parseInt(supervisorsInput.value) || 0;
            const managers = parseInt(managersInput.value) || 0;
            const users = parseInt(usersInput.value) || 0;

            const total = workers + supervisors + managers + users;
            workforceDisplay.value = total;

            // Trigger financial calculation
            calculateFinancials();
        }

        // Financial calculation
        const workDaysInput = document.getElementById('work_days');
        const dailyRateInput = document.getElementById('daily_rate');
        const taxRateInput = document.getElementById('tax_rate');
        const amountDiffInput = document.getElementById('amount_difference');

        const subtotalDisplay = document.getElementById('subtotal_display');
        const taxDisplay = document.getElementById('tax_amount_display');
        const totalDisplay = document.getElementById('total_amount_display');

        function calculateFinancials() {
            const totalWorkforce = parseInt(workforceDisplay.value) || 0;
            const workDays = parseInt(workDaysInput.value) || 0;
            const dailyRate = parseFloat(dailyRateInput.value) || 0;
            const taxRate = parseFloat(taxRateInput.value) || 0;
            const amountDiff = parseFloat(amountDiffInput ? amountDiffInput.value : 0) || 0;

            const subtotal = totalWorkforce * workDays * dailyRate;
            const taxAmount = (subtotal * taxRate) / 100;
            const total = subtotal + taxAmount + amountDiff;

            subtotalDisplay.value = subtotal.toFixed(2);
            taxDisplay.value = taxAmount.toFixed(2);
            totalDisplay.value = total.toFixed(2);
        }

        // Event listeners for workforce inputs
        if(workersInput) {
            [workersInput, supervisorsInput, managersInput, usersInput].forEach(input => {
                input.addEventListener('input', calculateTotalWorkforce);
            });
        }

        // Event listeners for financial inputs
        if(workDaysInput) {
            [workDaysInput, dailyRateInput, taxRateInput].forEach(input => {
                input.addEventListener('input', calculateFinancials);
            });
            if(amountDiffInput) amountDiffInput.addEventListener('input', calculateFinancials);
        }

        // Initialize calculations
        if(workersInput) calculateTotalWorkforce();
        if(workDaysInput) calculateFinancials();

        // Inline create: Clients
        const addClientForm = document.getElementById('addClientForm');
        if(addClientForm) {
            addClientForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(addClientForm);
                try {
                    const resp = await fetch("{{ route('invoices.add-client') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    const data = await resp.json();
                    if (data.success) {
                        // Add to local list and select it
                        clients.push(data.client); // Update local array
                        selectClient(data.client); // Select it

                        // reset form
                        addClientForm.reset();

                        // close modal
                        const modalEl = document.getElementById('addClientModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();

                        // notify
                        if (window.toastr) toastr.success(data.message || 'تم إضافة العميل بنجاح');
                    } else {
                        if (window.toastr) toastr.error(data.message || 'تعذر إضافة العميل');
                    }
                } catch (err) {
                    if (window.toastr) toastr.error('حدث خطأ أثناء إضافة العميل');
                    console.error(err);
                }
            });
        }

        // Inline create: Services
        const addServiceForm = document.getElementById('addServiceForm');
        if(addServiceForm) {
            addServiceForm.addEventListener('submit', async function (e) {
                e.preventDefault();
                const formData = new FormData(addServiceForm);
                try {
                    const resp = await fetch("{{ route('invoices.add-service') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    });
                    const data = await resp.json();
                    if (data.success) {
                        // Add to local list and select it
                        services.push(data.service); // Update local array
                        selectService(data.service); // Select it

                        // reset form
                        addServiceForm.reset();

                        // close modal
                        const modalEl = document.getElementById('addServiceModal');
                        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                        modal.hide();

                        // notify
                        if (window.toastr) toastr.success(data.message || 'تم إضافة الخدمة بنجاح');
                    } else {
                        if (window.toastr) toastr.error(data.message || 'تعذر إضافة الخدمة');
                    }
                } catch (err) {
                    if (window.toastr) toastr.error('حدث خطأ أثناء إضافة الخدمة');
                    console.error(err);
                }
            });
        }
    </script>
@endpush
