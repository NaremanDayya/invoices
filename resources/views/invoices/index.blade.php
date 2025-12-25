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

        /* Column Selection Modal Styles */
        .columns-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .columns-modal.active {
            display: flex;
        }

        .columns-modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .columns-modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .columns-modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .columns-modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.25rem;
        }

        .columns-modal-body {
            padding: 1.5rem;
            max-height: 400px;
            overflow-y: auto;
        }

        .columns-search {
            position: relative;
            margin-bottom: 1rem;
        }

        .columns-search input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .columns-search i {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .columns-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .column-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .column-item:hover {
            background-color: #f3f4f6;
        }

        .column-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            width: 100%;
            font-weight: 500;
        }

        .column-checkbox input[type="checkbox"] {
            width: 1.125rem;
            height: 1.125rem;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            cursor: pointer;
        }

        .column-checkbox input[type="checkbox"]:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .columns-modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .columns-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-select-all {
            background: none;
            border: none;
            color: #4f46e5;
            font-weight: 600;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .btn-select-all:hover {
            background-color: #eef2ff;
        }

        .btn-cancel {
            background: none;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background-color: #f9fafb;
        }

        .btn-apply {
            background: #4f46e5;
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .btn-apply:hover {
            background: #4338ca;
        }

        /* Action buttons styling */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-purple {
            background: #8b5cf6;
            color: white;
        }

        .btn-purple:hover {
            background: #7c3aed;
        }

        .btn-dark {
            background: #374151;
            color: white;
        }

        .btn-dark:hover {
            background: #1f2937;
        }

        .btn-gray {
            background: #6b7280;
            color: white;
        }

        .btn-gray:hover {
            background: #4b5563;
        }

        .btn-dropdown {
            background: #8b5cf6;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-dropdown:hover {
            background: #7c3aed;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 180px;
            margin-top: 4px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            z-index: 10;
        }

        .dropdown.active .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            width: 100%;
            border: none;
            background: none;
            text-align: right;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
        }

        /* Counter badge */
        #invoiceCounter {
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        #invoiceCounter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.15);
        }

        /* Print styles */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                background: #fff;
                font-size: 14px;
                color: #000;
            }

            #print-area .table-actions,
            #print-area .table-filters,
            #print-area .btn,
            #print-area select,
            #print-area input,
            #print-area .search-box,
            #print-area .no-print {
                display: none !important;
            }

            #print-area table.table {
                width: 100% !important;
                border-collapse: collapse;
                font-weight: bold;
            }

            #print-area table.table th,
            #print-area table.table td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }

            #print-area table.table thead {
                background-color: #f0f0f0;
            }

            #print-area .pdf-footer {
                display: block !important;
            }

            .no-print {
                display: none !important;
            }

            .pdf-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding: 15px;
                border-bottom: 2px solid #333;
            }
        }

        .pdf-header {
            display: none;
        }

        .pdf-header .header-logo {
            max-height: 180px !important;
            max-width: 300px !important;
            height: auto !important;
            width: auto !important;
            object-fit: contain !important;
        }

        .pdf-footer {
            display: none;
        }
    </style>
@endpush

@section('content')
    <div id="print-area">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0" style="color: var(--primary);">
                <i class="fas fa-file-invoice me-2"></i>
                إدارة الفواتير
            </h2>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <!-- Invoice Counter -->
                <div id="invoiceCounter" class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                    <div class="flex items-center gap-2">
                        <div class="bg-blue-100 p-1 rounded-full">
                            <i class="fas fa-receipt text-blue-600 text-sm"></i>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-blue-900">الفواتير المعروضة</div>
                            <div class="text-lg font-bold text-blue-700">
                                <span id="displayedCount">0</span>
                                <span class="text-sm font-normal">/</span>
                                <span id="totalCount" class="text-sm font-normal">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create Invoice Button -->
                <button type="button" class="btn btn-primary no-print" data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
                    <i class="fas fa-plus me-2"></i>
                    فاتورة جديدة
                </button>

                <!-- Columns Selection -->
                <button id="columnsBtn" class="btn btn-purple no-print" onclick="openColumnsModal()">
                    <i class="fas fa-columns"></i>
                    اختيار الأعمدة
                    <span id="columnsBadge" class="bg-white text-purple-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold">17</span>
                </button>

                <!-- Export Dropdown -->
                <div class="export-options no-print">
                    <div class="dropdown">
                        <button class="btn btn-dropdown" id="exportBtn" type="button">
                            تصدير البيانات
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                        <div class="dropdown-menu" id="exportDropdown">
                            <button class="dropdown-item" data-type="xlsx" onclick="exportTable('xlsx')">
                                <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                تصدير كملف Excel
                            </button>
                            <button class="dropdown-item" data-type="pdf" onclick="exportTable('pdf')">
                                <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 7V17C4 18.1046 4.89543 19 6 19H18C19.1046 19 20 18.1046 20 17V7M4 7H20M4 7L6 4H18L20 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                                تصدير كملف PDF
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Print Button -->
                <button class="btn btn-gray no-print" onclick="window.print()">
                    <i class="fas fa-print"></i>
                    طباعة
                </button>
            </div>
        </div>

        <!-- PDF Header (hidden by default) -->
        <div class="pdf-header" style="display: none;">
            <div class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                <div class="d-flex flex-column align-items-center text-center mx-auto">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                    <h2 class="header-text">تقرير الفواتير</h2>
                </div>
            </div>
        </div>

        <!-- Statistics Cards & Filters -->
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
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 no-print">
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
                            id="searchInput"
                            oninput="applyLiveFilters()"
                        >
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">حالة السداد</label>
                    <select
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200 appearance-none"
                        id="statusFilter"
                        onchange="applyLiveFilters()"
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
                        id="clientFilter"
                        onchange="applyLiveFilters()"
                    >
                        <option value="">كل العملاء</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                    <input
                        type="text"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                        id="startDate"
                        placeholder="من تاريخ"
                        onchange="applyLiveFilters()"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                    <input
                        type="text"
                        class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all duration-200"
                        id="endDate"
                        placeholder="إلى تاريخ"
                        onchange="applyLiveFilters()"
                    >
                </div>

                <!-- Reset Button -->
                <div>
                    <button
                        class="w-full px-4 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 hover:border-gray-400 active:scale-95 transition-all duration-200 flex items-center justify-center gap-2 font-medium"
                        onclick="resetFilters()"
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
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="invoicesTable">
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
                            <th class="border-0 text-center no-print">الإجراءات</th>
                        </tr>
                        </thead>
                        <tbody id="tableBody">
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
                                <td class="text-center no-print">
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
                </div>
            </div>
        </div>

        <!-- PDF Footer -->
        <div class="pdf-footer hidden border-t-2 border-gray-800 mt-8 py-4 text-center">
            <p class="text-gray-600">جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4 no-print">
            <div class="text-muted">
                عرض <span id="displayedCountText">{{ $invoices->firstItem() ?? 0 }}</span> إلى <span id="displayedCountEnd">{{ $invoices->lastItem() ?? 0 }}</span> من <span id="totalCountText">{{ $invoices->total() ?? 0 }}</span> فاتورة
            </div>
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Columns Selection Modal -->
    <div id="columnsModal" class="columns-modal">
        <div class="columns-modal-content">
            <div class="columns-modal-header">
                <h3 class="columns-modal-title">
                    <i class="fas fa-columns"></i>
                    اختيار الأعمدة للعرض
                </h3>
                <button class="columns-modal-close" onclick="closeColumnsModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="columns-modal-body">
                <div class="columns-search">
                    <input type="text" id="columnsSearch" placeholder="بحث عن عمود..." onkeyup="filterColumns()">
                    <i class="fas fa-search"></i>
                </div>
                <div class="columns-list" id="columnsList">
                    <!-- Column items will be generated here -->
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="number" checked>
                            <span class="column-name">رقم الفاتورة</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="client" checked>
                            <span class="column-name">العميل</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="generation_date" checked>
                            <span class="column-name">تاريخ الإصدار</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="generation_delay_days" checked>
                            <span class="column-name">أيام تأخير الإصدار</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="service" checked>
                            <span class="column-name">نوع الخدمة</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="total_workforce" checked>
                            <span class="column-name">إجمالي العمالة</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="work_days" checked>
                            <span class="column-name">أيام العمل</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="base_price" checked>
                            <span class="column-name">المبلغ قبل الضريبة</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="tax_amount" checked>
                            <span class="column-name">الضريبة</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="total_price" checked>
                            <span class="column-name">المبلغ الإجمالي</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="price_difference" checked>
                            <span class="column-name">فرق المبلغ</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="payment_status" checked>
                            <span class="column-name">حالة السداد</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="payment_date" checked>
                            <span class="column-name">تاريخ السداد</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="payment_delay_days" checked>
                            <span class="column-name">أيام تأخير السداد</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="invoice_status" checked>
                            <span class="column-name">حالة الفاتورة</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="columns-modal-footer">
                <div class="columns-actions">
                    <button class="btn-select-all" onclick="toggleSelectAll()">تحديد الكل</button>
                    <button class="btn-select-all" onclick="resetSelection()">إعادة تعيين</button>
                </div>
                <div>
                    <button class="btn-cancel" onclick="closeColumnsModal()">إلغاء</button>
                    <button class="btn-apply" onclick="applyColumnSelection()">
                        <i class="fas fa-check"></i>
                        تطبيق
                    </button>
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
@endsection

@push('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-latest/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        // Initialize data
        let InvoicesData = @json($invoices);
        let currentFilteredInvoices = [];

        // Flatpickr for date inputs
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#startDate", {
                locale: "ar",
                dateFormat: "Y-m-d",
                allowInput: true,
                onChange: function(selectedDates, dateStr) {
                    applyLiveFilters();
                }
            });

            flatpickr("#endDate", {
                locale: "ar",
                dateFormat: "Y-m-d",
                allowInput: true,
                onChange: function(selectedDates, dateStr) {
                    applyLiveFilters();
                }
            });

            // Initialize data
            InvoicesData = @json($invoices);
            currentFilteredInvoices = [...InvoicesData];

            // Update counters
            updateInvoiceCounter();

            // Export dropdown functionality
            const exportBtn = document.getElementById('exportBtn');
            const dropdown = document.getElementById('exportDropdown');

            if (exportBtn && dropdown) {
                exportBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    this.closest('.dropdown').classList.toggle('active');
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!exportBtn.contains(e.target) && !dropdown.contains(e.target)) {
                        document.querySelectorAll('.dropdown').forEach(drop => {
                            drop.classList.remove('active');
                        });
                    }
                });
            }

            // Initialize credit note modal
            const creditNoteModalElement = document.getElementById('creditNoteModal');
            if (creditNoteModalElement) {
                const creditNoteModal = new bootstrap.Modal(creditNoteModalElement);
                const creditNoteForm = document.getElementById('creditNoteForm');

                // Handle credit note button click
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.credit-note-btn')) {
                        const button = e.target.closest('.credit-note-btn');
                        const invoiceId = button.getAttribute('data-invoice-id');
                        const invoiceNumber = button.getAttribute('data-invoice-number');
                        const totalAmount = button.getAttribute('data-total-amount');

                        document.getElementById('invoice_id').value = invoiceId;
                        document.getElementById('invoice_number_display').textContent = invoiceNumber;
                        document.getElementById('total_amount_display').textContent = totalAmount;

                        document.getElementById('credit_amount').max = totalAmount;
                        document.getElementById('credit_amount').placeholder = `الحد الأقصى: ${totalAmount} ﷼`;

                        if (creditNoteForm) {
                            creditNoteForm.reset();
                        }

                        creditNoteModal.show();
                    }
                });

                // Handle form submission
                if (creditNoteForm) {
                    creditNoteForm.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const formData = new FormData(this);
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
                                    showAlert('success', data.message);
                                    creditNoteModal.hide();
                                    setTimeout(() => window.location.reload(), 1500);
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
            }
        });

        // Live filtering function
        function applyLiveFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const clientFilter = document.getElementById('clientFilter').value;
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            let filteredData = [...InvoicesData];

            // Apply search filter
            if (searchTerm) {
                filteredData = filteredData.filter(invoice => {
                    return (
                        (invoice.number && invoice.number.toLowerCase().includes(searchTerm)) ||
                        (invoice.client && invoice.client.name && invoice.client.name.toLowerCase().includes(searchTerm)) ||
                        (invoice.service && invoice.service.name && invoice.service.name.toLowerCase().includes(searchTerm))
                    );
                });
            }

            // Apply status filter
            if (statusFilter) {
                filteredData = filteredData.filter(invoice =>
                    invoice.payment_status === statusFilter
                );
            }

            // Apply client filter
            if (clientFilter) {
                filteredData = filteredData.filter(invoice =>
                    invoice.client_id == clientFilter
                );
            }

            // Apply date range filter
            if (startDate || endDate) {
                filteredData = filteredData.filter(invoice => {
                    const invoiceDate = invoice.generation_date;

                    if (!invoiceDate) return false;

                    if (startDate && endDate) {
                        return invoiceDate >= startDate && invoiceDate <= endDate;
                    } else if (startDate) {
                        return invoiceDate >= startDate;
                    } else if (endDate) {
                        return invoiceDate <= endDate;
                    }
                    return true;
                });
            }

            currentFilteredInvoices = filteredData;
            renderTable(currentFilteredInvoices);
            updateInvoiceCounter();
        }

        // Render table function
        function renderTable(data = currentFilteredInvoices) {
            const tbody = document.getElementById('tableBody');
            if (!tbody) return;

            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="16" class="text-center py-4">
                            <div class="empty-icon">
                                <i class="fas fa-file-invoice-dollar fa-2x text-muted"></i>
                            </div>
                            <div class="empty-text">لا توجد فواتير متاحة</div>
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach(invoice => {
                const statusColors = {
                    'paid': ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'check-circle'],
                'pending': ['bg' => '#fef3c7', 'color' => '#92400e', 'icon' => 'clock'],
                'overdue': ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'exclamation-triangle']
            };
                const status = statusColors[invoice.payment_status] || statusColors['pending'];

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${invoice.number || '—'}</td>
                    <td>
                        <div class="fw-bold">${invoice.client ? invoice.client.name : 'غير معروف'}</div>
                    </td>
                    <td>${invoice.generation_date ? new Date(invoice.generation_date).toLocaleDateString('ar-EG') : '—'}</td>
                    <td>
                        ${invoice.last_generation_date && invoice.generation_date ?
                    Math.ceil((new Date(invoice.generation_date) - new Date(invoice.last_generation_date)) / (1000 * 60 * 60 * 24)) : 0}
                    </td>
                    <td>${invoice.service ? invoice.service.name : '—'}</td>
                    <td>
                        <span title="Workers">W: ${invoice.total_workers || 0}</span> |
                        <span title="Supervisors">S: ${invoice.total_supervisors || 0}</span> |
                        <span title="Managers">M: ${invoice.total_managers || 0}</span> |
                        <span title="Users">U: ${invoice.total_users || 0}</span>
                    </td>
                    <td>${invoice.work_days || 0}</td>
                    <td>${invoice.base_price ? Number(invoice.base_price).toFixed(2) : '0.00'} ﷼</td>
                    <td>${invoice.tax_amount ? Number(invoice.tax_amount).toFixed(2) : '0.00'} ﷼</td>
                    <td>${invoice.total_price ? Number(invoice.total_price).toFixed(2) : '0.00'} ﷼</td>
                    <td>${invoice.price_difference ? Number(invoice.price_difference).toFixed(2) : '0.00'} ﷼</td>
                    <td>
                        <span class="badge rounded-pill" style="background: ${status['bg']}; color: ${status['color']};">
                            <i class="fas fa-${status['icon']} me-1"></i>
                            ${invoice.payment_status === 'paid' ? 'مدفوعة' :
                    invoice.payment_status === 'pending' ? 'قيد الانتظار' : 'متأخرة'}
                        </span>
                    </td>
                    <td>${invoice.payment_date ? new Date(invoice.payment_date).toLocaleDateString('ar-EG') : '—'}</td>
                    <td>
                        ${invoice.payment_date && invoice.due_date ?
                    Math.ceil((new Date(invoice.payment_date) - new Date(invoice.due_date)) / (1000 * 60 * 60 * 24)) : 0}
                    </td>
                    <td>${invoice.invoice_status || '—'}</td>
                    <td class="text-center no-print">
                        <div class="d-flex justify-content-center gap-2">
                            <button class="btn btn-sm btn-outline-warning credit-note-btn"
                                    title="إشعار دائن"
                                    data-invoice-id="${invoice.id}"
                                    data-invoice-number="${invoice.number}"
                                    data-total-amount="${invoice.total_price || 0}">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </button>
                            <button class="btn btn-sm" style="background: #4f46e5; color: white;" title="عرض">
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
                `;
                tbody.appendChild(row);
            });

            updateInvoiceCounter();
        }

        // Update invoice counter
        function updateInvoiceCounter() {
            const displayedCount = currentFilteredInvoices.length;
            const totalCount = InvoicesData.length;

            document.getElementById('displayedCount').textContent = displayedCount;
            document.getElementById('totalCount').textContent = totalCount;
            document.getElementById('displayedCountText').textContent = displayedCount;
            document.getElementById('totalCountText').textContent = totalCount;

            const counter = document.getElementById('invoiceCounter');
            if (displayedCount === totalCount) {
                counter.classList.remove('bg-blue-50', 'border-blue-200');
                counter.classList.add('bg-green-50', 'border-green-200');
            } else {
                counter.classList.remove('bg-green-50', 'border-green-200');
                counter.classList.add('bg-blue-50', 'border-blue-200');
            }
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('clientFilter').value = '';
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';

            currentFilteredInvoices = [...InvoicesData];
            renderTable(currentFilteredInvoices);
            updateInvoiceCounter();
        }

        // Columns Modal Functions
        function openColumnsModal() {
            document.getElementById('columnsModal').classList.add('active');
        }

        function closeColumnsModal() {
            document.getElementById('columnsModal').classList.remove('active');
        }

        function filterColumns() {
            const searchTerm = document.getElementById('columnsSearch').value.toLowerCase();
            const columnItems = document.querySelectorAll('.column-item');

            columnItems.forEach(item => {
                const columnName = item.querySelector('.column-name').textContent.toLowerCase();
                if (columnName.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('#columnsList input[type="checkbox"]');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        }

        function resetSelection() {
            const checkboxes = document.querySelectorAll('#columnsList input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function applyColumnSelection() {
            const checkboxes = document.querySelectorAll('#columnsList input[type="checkbox"]');
            const selectedColumns = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedColumns.push(checkbox.value);
                }
            });

            // Update columns badge
            document.getElementById('columnsBadge').textContent = selectedColumns.length;

            // Hide/show columns in table
            const tableHeaders = document.querySelectorAll('#invoicesTable th');
            const tableRows = document.querySelectorAll('#invoicesTable tbody tr');

            tableHeaders.forEach((header, index) => {
                const columnName = getColumnNameFromHeader(header.textContent);
                if (selectedColumns.includes(columnName)) {
                    header.style.display = '';
                    tableRows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells[index]) {
                            cells[index].style.display = '';
                        }
                    });
                } else {
                    header.style.display = 'none';
                    tableRows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells[index]) {
                            cells[index].style.display = 'none';
                        }
                    });
                }
            });

            closeColumnsModal();
        }

        function getColumnNameFromHeader(headerText) {
            const columnMap = {
                'رقم الفاتورة': 'number',
                'العميل': 'client',
                'تاريخ الإصدار': 'generation_date',
                'أيام تأخير الإصدار': 'generation_delay_days',
                'نوع الخدمة': 'service',
                'إجمالي العمالة': 'total_workforce',
                'أيام العمل': 'work_days',
                'المبلغ قبل الضريبة': 'base_price',
                'الضريبة': 'tax_amount',
                'المبلغ الإجمالي': 'total_price',
                'فرق المبلغ': 'price_difference',
                'حالة السداد': 'payment_status',
                'تاريخ السداد': 'payment_date',
                'أيام تأخير السداد': 'payment_delay_days',
                'حالة الفاتورة': 'invoice_status',
                'الإجراءات': 'actions'
            };

            return columnMap[headerText.trim()] || '';
        }

        // Export functions
        function exportTable(type) {
            const selectedColumns = getSelectedColumns();

            if (type === 'xlsx') {
                exportInvoices(selectedColumns);
            } else if (type === 'pdf') {
                exportToPDF(selectedColumns);
            }

            // Close dropdown
            document.querySelectorAll('.dropdown').forEach(drop => {
                drop.classList.remove('active');
            });
        }

        function getSelectedColumns() {
            const checkboxes = document.querySelectorAll('#columnsList input[type="checkbox"]:checked');
            return Array.from(checkboxes).map(checkbox => checkbox.value);
        }

        function exportInvoices(selectedColumns) {
            if (!selectedColumns || selectedColumns.length === 0) {
                selectedColumns = Array.from(document.querySelectorAll('#columnsList input[type="checkbox"]:checked'))
                    .map(checkbox => checkbox.value);
            }

            const columnsMap = {
                'number': 'رقم الفاتورة',
                'client': 'العميل',
                'generation_date': 'تاريخ الإصدار',
                'generation_delay_days': 'أيام تأخير الإصدار',
                'service': 'نوع الخدمة',
                'total_workforce': 'إجمالي العمالة',
                'work_days': 'أيام العمل',
                'base_price': 'المبلغ قبل الضريبة',
                'tax_amount': 'الضريبة',
                'total_price': 'المبلغ الإجمالي',
                'price_difference': 'فرق المبلغ',
                'payment_status': 'حالة السداد',
                'payment_date': 'تاريخ السداد',
                'payment_delay_days': 'أيام تأخير السداد',
                'invoice_status': 'حالة الفاتورة'
            };

            const headers = selectedColumns
                .filter(key => key !== 'actions')
                .map(key => columnsMap[key]);

            const data = currentFilteredInvoices.map(invoice => {
                const row = {};
                selectedColumns.forEach(key => {
                    let value = '';
                    switch (key) {
                        case 'number':
                            value = invoice.number || '';
                            break;
                        case 'client':
                            value = invoice.client ? invoice.client.name : 'غير معروف';
                            break;
                        case 'generation_date':
                            value = invoice.generation_date ? new Date(invoice.generation_date).toLocaleDateString('ar-EG') : '';
                            break;
                        case 'generation_delay_days':
                            const genDelay = invoice.last_generation_date && invoice.generation_date ?
                                Math.ceil((new Date(invoice.generation_date) - new Date(invoice.last_generation_date)) / (1000 * 60 * 60 * 24)) : 0;
                            value = genDelay;
                            break;
                        case 'service':
                            value = invoice.service ? invoice.service.name : '';
                            break;
                        case 'total_workforce':
                            value = `W: ${invoice.total_workers || 0}, S: ${invoice.total_supervisors || 0}, M: ${invoice.total_managers || 0}, U: ${invoice.total_users || 0}`;
                            break;
                        case 'work_days':
                            value = invoice.work_days || 0;
                            break;
                        case 'base_price':
                            value = invoice.base_price ? Number(invoice.base_price).toFixed(2) : '0.00';
                            break;
                        case 'tax_amount':
                            value = invoice.tax_amount ? Number(invoice.tax_amount).toFixed(2) : '0.00';
                            break;
                        case 'total_price':
                            value = invoice.total_price ? Number(invoice.total_price).toFixed(2) : '0.00';
                            break;
                        case 'price_difference':
                            value = invoice.price_difference ? Number(invoice.price_difference).toFixed(2) : '0.00';
                            break;
                        case 'payment_status':
                            value = invoice.payment_status === 'paid' ? 'مدفوعة' :
                                invoice.payment_status === 'pending' ? 'قيد الانتظار' : 'متأخرة';
                            break;
                        case 'payment_date':
                            value = invoice.payment_date ? new Date(invoice.payment_date).toLocaleDateString('ar-EG') : '';
                            break;
                        case 'payment_delay_days':
                            const payDelay = invoice.payment_date && invoice.due_date ?
                                Math.ceil((new Date(invoice.payment_date) - new Date(invoice.due_date)) / (1000 * 60 * 60 * 24)) : 0;
                            value = payDelay;
                            break;
                        case 'invoice_status':
                            value = invoice.invoice_status || '';
                            break;
                    }
                    row[key] = value;
                });
                return row;
            });

            const wsData = [headers, ...data.map(row => selectedColumns
                .filter(key => key !== 'actions')
                .map(key => row[key] || ''))];
            const worksheet = XLSX.utils.aoa_to_sheet(wsData);

            // Auto-fit columns
            const colWidths = wsData[0].map((_, colIndex) => {
                const maxLen = wsData.reduce((max, row) => {
                    const cell = row[colIndex] ? String(row[colIndex]) : '';
                    return Math.max(max, cell.length);
                }, 10);
                return { wch: maxLen + 2 };
            });
            worksheet['!cols'] = colWidths;

            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "الفواتير");

            XLSX.writeFile(workbook, `فواتير_${new Date().toISOString().slice(0, 10)}.xlsx`);
        }

        function exportToPDF(selectedColumns) {
            const originalTable = document.querySelector('#invoicesTable');
            const table = originalTable.cloneNode(true);
            const pdfHeader = document.querySelector('.pdf-header').cloneNode(true);
            const pdfFooter = document.querySelector('.pdf-footer').cloneNode(true);

            pdfHeader.style.display = 'block';
            pdfFooter.style.display = 'block';

            const pdfContainer = document.createElement('div');
            pdfContainer.style.padding = '20px';
            pdfContainer.appendChild(pdfHeader);
            pdfContainer.appendChild(table);
            pdfContainer.appendChild(pdfFooter);

            const headers = table.querySelectorAll('thead th');

            headers.forEach((header, index) => {
                const columnName = header.textContent.trim();
                const columnKey = getColumnNameFromHeader(columnName);

                if (header.classList.contains('no-print')) {
                    header.style.display = 'none';
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells[index]) {
                            row.cells[index].style.display = 'none';
                        }
                    });
                    return;
                }

                if (!selectedColumns.includes(columnKey)) {
                    header.style.display = 'none';
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells[index]) {
                            row.cells[index].style.display = 'none';
                        }
                    });
                }
            });

            const options = {
                margin: 10,
                filename: `تقرير_الفواتير_${new Date().toISOString().slice(0,10)}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: document.documentElement.offsetWidth
                },
                jsPDF: {
                    unit: 'mm',
                    format: [594, 420],
                    orientation: 'landscape',
                    compress: true
                }
            };

            html2pdf().set(options).from(pdfContainer).save();
        }

        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);

            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
            }, 5000);
        }

        function toggleCustomStatus() {
            const invoiceStatus = document.getElementById('invoice_status');
            const customStatusContainer = document.getElementById('custom_status_container');

            if (invoiceStatus.value === 'other') {
                customStatusContainer.style.display = 'block';
            } else {
                customStatusContainer.style.display = 'none';
            }
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

@endpush
