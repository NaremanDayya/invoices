@extends('layouts.master')

@section('title', 'إدارة الفواتير')
@section('page_title', 'الفواتير')
@section('page_subtitle', 'إدارة جميع الفواتير الصادرة')

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

        /* Table Styling */
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
        .inv-number {
            color: #10a37f;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
        }
        .customer-info .name {
            font-weight: 700;
            display: block;
            margin-bottom: 2px;
        }
        .customer-info .phone {
            font-size: 0.75rem;
            color: #a0aec0;
        }
        .emp-count {
            width: 35px;
            height: 35px;
            background: #e6fffa;
            color: #319795;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .status-badge.paid { background: #def7ec; color: #03543f; }
        .status-badge.pending { background: #fef3c7; color: #92400e; }
        .status-badge.late { background: #fde8e8; color: #9b1c1c; }
        .status-badge.cancelled { background: #f3f4f6; color: #4b5563; }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #718096;
            transition: all 0.2s;
            border: 1px solid #edf2f7;
        }
        .btn-action:hover {
            background: var(--primary-accent);
            color: #1e4a46;
            border-color: var(--primary-accent);
        }
    </style>
@endpush

@section('page_actions')
    <div class="d-flex gap-2">
        <div class="dropdown">
            <button class="btn btn-outline-success rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2 dropdown-toggle"
                    type="button" id="exportDropdownInvoices" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i>
                <span>تصدير</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdownInvoices">
                <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportInvoicesToPDF()">
                        <i class="bi bi-file-pdf text-danger me-2"></i>تصدير PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportInvoicesToExcel()">
                        <i class="bi bi-file-excel text-success me-2"></i>تصدير Excel
                    </a>
                </li>
            </ul>
        </div>
        <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
            <i class="bi bi-plus-lg"></i>
            <span>فاتورة جديدة</span>
        </button>
    </div>
@endsection

@section('content')
    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['paid'] ?? 3 }}</h3>
                <p>مدفوعة</p>
            </div>
            <div class="stat-icon-box" style="background: #e6fffa; color: #319795;">
                <i class="bi bi-check-all"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['pending'] ?? 2 }}</h3>
                <p>معلقة</p>
            </div>
            <div class="stat-icon-box" style="background: #fffaf0; color: #dd6b20;">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3 class="text-danger">{{ $stats['late'] ?? 2 }}</h3>
                <p>متأخرة</p>
            </div>
            <div class="stat-icon-box" style="background: #fff5f5; color: #e53e3e;">
                <i class="bi bi-clock-history"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['cancelled'] ?? 1 }}</h3>
                <p>ملغاة</p>
            </div>
            <div class="stat-icon-box" style="background: #f7fafc; color: #4a5568;">
                <i class="bi bi-trash"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('invoices.index') }}" id="filterForm">
        <div class="bg-white rounded-xl border border-gray-100 p-3 mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="search-box ms-0" style="width: 300px; background: #fcfcfc; border: 1px solid #f0f0f0;">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الفاتورة أو اسم العميل..." style="font-size: 0.85rem;" onchange="this.form.submit()">
                </div>
                <select name="status" class="form-select border-0 bg-light rounded-xl" style="width: 150px; font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">جميع الحالات</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>متأخرة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
        </div>
    </form>

    <!-- Invoices Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>العميل</th>
                        <th>الخدمة</th>
                        <th>تاريخ الإصدار</th>
                        <th>المبلغ</th>
                        <th>الضريبة</th>
                        <th>الإجمالي</th>
                        <th class="text-center">عدد الموظفين</th>
                        <th class="text-center">أيام العمل</th>
                        <th class="text-center">أيام التأخير</th>
                        <th class="text-center">إشعارات دائنة</th>
                        <th>الحالة</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Logic for actual or fake data
                        $items = $invoices->isEmpty() ? collect([]) : $invoices;
                        if($items->isEmpty()){
                            for($i=1; $i<=5; $i++){
                                $items->push((object)[
                                    'id' => $i,
                                    'number' => 'INV-00'.($i),
                                    'client' => (object)['name' => $i == 1 ? 'شركة النور للمقاولات' : ($i == 2 ? 'مؤسسة الفجر التجارية' : 'شركة البناء الحديث'), 'phone' => '966501234567+'],
                                    'base_price' => 150000,
                                    'tax_amount' => 22500,
                                    'total_price' => 172500,
                                    'total_workers' => 25 + ($i*10),
                                    'work_days' => 21,
                                    'payment_status' => $i == 1 ? 'paid' : ($i == 2 ? 'pending' : 'late'),
                                    'price_difference' => $i % 2 == 0 ? 10000 : 0
                                ]);
                            }
                        }
                    @endphp
                    @foreach($items as $invoice)
                    <tr>
                        <td><span class="inv-number">{{ $invoice->number }}</span></td>
                        <td>
                            <div class="customer-info">
                                <span class="name">{{ $invoice->client->name ?? '—' }}</span>
                                <span class="phone text-muted" dir="ltr">{{ $invoice->client->phone ?? '' }}</span>
                            </div>
                        </td>
                        <td>
                            @if(isset($invoice->service_details_data) && is_array($invoice->service_details_data) && count($invoice->service_details_data) > 0)
                                <span class="badge bg-light text-dark rounded-pill px-3" data-bs-toggle="tooltip" data-bs-html="true" title="
                                    @foreach($invoice->service_details_data as $detail)
                                        <strong>{{ $detail['label'] ?? '' }}:</strong> {{ $detail['value'] ?? '' }}<br>
                                    @endforeach
                                ">
                                    {{ $invoice->service->name ?? '—' }}
                                    <i class="bi bi-info-circle-fill ms-1"></i>
                                </span>
                            @else
                                <span class="badge bg-light text-dark rounded-pill px-3">{{ $invoice->service->name ?? '—' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="small">{{ isset($invoice->generation_date) ? $invoice->generation_date->format('Y-m-d') : '—' }}</div>
                        </td>
                        <td>{{ number_format($invoice->base_price, 0) }} ر.س</td>
                        <td>{{ number_format($invoice->tax_amount, 0) }} ر.س</td>
                        <td class="fw-bold">{{ number_format($invoice->total_price, 0) }} ر.س</td>
                        <td class="text-center">
                            <span class="emp-count">{{ $invoice->total_workers ?? 0 }}</span>
                        </td>
                        <td class="text-center">{{ $invoice->work_days ?? 0 }} يوم</td>
                        <td class="text-center">
                            @php
                                $issueLate = isset($invoice->issue_delay_days) && $invoice->issue_delay_days > 0;
                                $paymentLate = isset($invoice->late_days) && $invoice->late_days > 0;
                            @endphp

                            @if($issueLate || $paymentLate)
                                <div class="d-flex flex-column gap-1 align-items-center">
                                    @if($issueLate)
                                        <span class="badge bg-danger text-white rounded-pill px-3 d-flex align-items-center gap-1" title="تأخير في الإصدار">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            {{ $invoice->issue_delay_days }} يوم (إصدار)
                                        </span>
                                    @endif
                                    @if($paymentLate)
                                        <span class="badge bg-success text-white rounded-pill px-3 d-flex align-items-center gap-1" title="تأخير في الدفع من العميل">
                                            <i class="bi bi-cash-coin"></i>
                                            {{ $invoice->late_days }} يوم (دفع)
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if(isset($invoice->credit_notes_count) && $invoice->credit_notes_count > 0)
                                <a href="{{ route('invoices.show', $invoice->id) }}#credit-notes" class="badge rounded-pill bg-warning text-dark px-2 text-decoration-none" style="cursor: pointer;" title="عرض الإشعارات الدائنة">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ $invoice->credit_notes_count }}
                                </a>
                            @elseif(isset($invoice->price_difference) && $invoice->price_difference > 0)
                                <span class="badge rounded-pill bg-warning text-dark px-2">
                                    <i class="bi bi-info-circle me-1"></i>
                                    {{ number_format($invoice->price_difference, 0) }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
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
                                @if($invoice->payment_status == 'late')
                                    <div class="small fw-normal opacity-75">متأخر 5 يوم</div>
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('invoices.show', $invoice->id) }}" class="btn-action" title="عرض"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn-action" title="تعديل"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn-action text-warning" title="إشعار دائن" onclick="openCreditNoteModal({{ $invoice->id }}, '{{ $invoice->number }}', {{ $invoice->total_price }})"><i class="bi bi-file-earmark-text"></i></button>
                                <button type="button" class="btn-action text-danger" title="حذف" onclick="confirmDelete({{ $invoice->id }})"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteInvoiceModal" tabindex="-1" aria-hidden="true">
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
                    <p class="mb-3">هل أنت متأكد من حذف هذه الفاتورة؟</p>
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>تحذير:</strong> لا يمكن التراجع عن هذا الإجراء. سيتم حذف الفاتورة وجميع البيانات المرتبطة بها.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary rounded-xl" data-bs-dismiss="modal">إلغاء</button>
                    <form id="deleteInvoiceForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-xl">
                            <i class="bi bi-trash me-2"></i>حذف الفاتورة
                        </button>
                    </form>
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

                            <!-- Dynamic Service Details Section -->
                            <div class="card mb-4" id="serviceDetailsSection" style="display: none;">
                                <div class="card-header bg-light">
                                    <i class="fas fa-list me-2"></i>
                                    <span id="serviceDetailsSectionTitle">تفاصيل الخدمة</span>
                                </div>
                                <div class="card-body">
                                    <div id="serviceDetailsContainer" class="row g-3">
                                        <!-- Service details will be populated dynamically based on selected service -->
                                    </div>
                                    
                                    <!-- Workforce Summary -->
                                    <div class="row mt-4 pt-3 border-top">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">إجمالي العمالة</label>
                                            <input type="text" id="total_workforce_display" class="form-control bg-light fw-bold" value="0" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold">إجمالي أيام العمل</label>
                                            <input type="text" id="total_work_days_display" class="form-control bg-light fw-bold" value="0" readonly>
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
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">المبلغ قبل الضريبة (﷼) <span class="text-danger">*</span></label>
                                            <input type="number" name="base_price" id="subtotal_display" class="form-control" value="0" step="0.01" min="0" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">نسبة الضريبة (%) <span class="text-danger">*</span></label>
                                            <input type="number" name="tax_rate" id="tax_rate" class="form-control" value="15" step="0.1" min="0" max="100" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">قيمة الضريبة (﷼)</label>
                                            <input type="text" id="tax_amount_display" class="form-control bg-light fw-bold" value="0.00" readonly>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">المبلغ الإجمالي (﷼)</label>
                                            <input type="text" id="total_amount_display" class="form-control bg-light fw-bold" value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment & Status -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">حالة الفاتورة <span class="text-danger">*</span></label>
                                            <div class="position-relative">
                                                <input type="text" id="statusSearchInput" class="form-control" placeholder="ابحث عن حالة الفاتورة..." autocomplete="off" required>
                                                <input type="hidden" name="invoice_status" id="selectedStatusId" required>
                                                <div id="statusDropdown" class="list-group position-absolute w-100 shadow" style="display:none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                                    <!-- Options will be populated by JS -->
                                                </div>
                                            </div>
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

            // Load service details
            loadServiceDetails(service.id);
        }

        async function loadServiceDetails(serviceId) {
            try {
                // Fetch service details from server
                const response = await fetch(`/services/${serviceId}/details`);
                const serviceDetails = await response.json();

                const detailsSection = document.getElementById('serviceDetailsSection');
                const detailsContainer = document.getElementById('serviceDetailsContainer');
                const sectionTitle = document.getElementById('serviceDetailsSectionTitle');

                detailsContainer.innerHTML = '';

                if (serviceDetails && serviceDetails.length > 0) {
                    sectionTitle.textContent = 'تفاصيل الخدمة: ' + serviceInput.value;

                    serviceDetails.forEach((detail, index) => {
                        if (detail.has_work_days == 1 || detail.has_work_days === true) {
                            // Show workforce count and work days side by side
                            const detailHtml = `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">${detail.name}</h6>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="form-label">العدد</label>
                                            <input type="number" name="service_details[${detail.id}][count]"
                                                   class="form-control service-detail-count"
                                                   placeholder="العدد"
                                                   min="0"
                                                   value="0"
                                                   data-detail-id="${detail.id}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label">أيام العمل</label>
                                            <input type="number" name="service_details[${detail.id}][days]"
                                                   class="form-control service-detail-days"
                                                   placeholder="${detail.work_days || '0'} يوم"
                                                   min="0"
                                                   value="${detail.work_days || 0}"
                                                   data-detail-id="${detail.id}">
                                            <input type="hidden" name="service_details[${detail.id}][name]" value="${detail.name}">
                                            <input type="hidden" name="service_details[${detail.id}][has_work_days]" value="1">
                                            <input type="hidden" name="service_details[${detail.id}][work_days]" value="${detail.work_days || 0}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                            detailsContainer.insertAdjacentHTML('beforeend', detailHtml);
                        } else {
                            // Show single field for non-workforce details
                            const detailHtml = `
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">${detail.name}</h6>
                                    <div class="mb-3">
                                        <label class="form-label">القيمة</label>
                                        <input type="text" name="service_details[${detail.id}][value]"
                                               class="form-control"
                                               placeholder="أدخل القيمة">
                                        <input type="hidden" name="service_details[${detail.id}][name]" value="${detail.name}">
                                        <input type="hidden" name="service_details[${detail.id}][has_work_days]" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                            detailsContainer.insertAdjacentHTML('beforeend', detailHtml);
                        }
                    });

                    detailsSection.style.display = 'block';

                    // Add event listeners for dynamic calculation
                    addServiceDetailEventListeners();
                } else {
                    detailsSection.style.display = 'none';
                }
            } catch (error) {
                console.error('Error loading service details:', error);
                detailsSection.style.display = 'none';
            }
        }

        function addServiceDetailEventListeners() {
            // Add input event listeners to service detail fields for real-time calculation
            document.querySelectorAll('.service-detail-count, .service-detail-days').forEach(input => {
                input.addEventListener('input', calculateServiceDetailsTotal);
            });
        }

        function calculateServiceDetailsTotal() {
            let totalWorkforce = 0;
            let totalWorkDays = 0;

            // Group by detail ID to calculate properly
            const detailCounts = {};
            const detailDays = {};

            // Collect counts
            document.querySelectorAll('.service-detail-count').forEach(input => {
                const detailId = input.getAttribute('data-detail-id');
                detailCounts[detailId] = parseInt(input.value) || 0;
            });

            // Collect days
            document.querySelectorAll('.service-detail-days').forEach(input => {
                const detailId = input.getAttribute('data-detail-id');
                detailDays[detailId] = parseInt(input.value) || 0;
            });

            // Calculate totals
            Object.keys(detailCounts).forEach(detailId => {
                const count = detailCounts[detailId];
                const days = detailDays[detailId] || 0;

                totalWorkforce += count;
                totalWorkDays += count * days;
            });

            // Update the main workforce display
            const workforceDisplay = document.getElementById('total_workforce_display');
            if (workforceDisplay) {
                workforceDisplay.value = totalWorkforce;
            }

            // Update total work days display
            const workDaysDisplay = document.getElementById('total_work_days_display');
            if (workDaysDisplay) {
                workDaysDisplay.value = totalWorkDays;
            }

            // Note: Subtotal is manually entered by user, not auto-calculated
            // User enters the base_price directly in the financial section
        }

        function openAddServiceModal(name) {
            const modalEl = document.getElementById('addServiceModal');
            const modal = new bootstrap.Modal(modalEl);
            modalEl.querySelector('[name="name"]').value = name;
            modal.show();
            serviceDropdown.style.display = 'none';
        }

        // Invoice Status Autocomplete
        const invoiceStatuses = @json($invoiceStatuses ?? []);
        const statusInput = document.getElementById('statusSearchInput');
        const statusDropdown = document.getElementById('statusDropdown');
        const selectedStatusId = document.getElementById('selectedStatusId');

        if (statusInput) {
            statusInput.addEventListener('input', function() {
                const search = this.value.toLowerCase();
                statusDropdown.innerHTML = '';

                if (search.length < 1) {
                    statusDropdown.style.display = 'none';
                    return;
                }

                const filtered = invoiceStatuses.filter(s =>
                    s.name.toLowerCase().includes(search) ||
                    (s.name_en && s.name_en.toLowerCase().includes(search))
                );

                if (filtered.length > 0) {
                    filtered.forEach(s => {
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action d-flex align-items-center gap-2';

                        const iconHtml = s.icon ? `<i class="bi bi-${s.icon}"></i>` : '';
                        const colorBadge = `<span class="badge rounded-pill" style="background-color: ${s.color}; width: 20px; height: 20px;"></span>`;

                        item.innerHTML = `${colorBadge} ${iconHtml} <span>${s.name}</span>`;
                        item.onclick = (e) => {
                            e.preventDefault();
                            selectStatus(s);
                        };
                        statusDropdown.appendChild(item);
                    });
                    statusDropdown.style.display = 'block';
                } else {
                    statusDropdown.style.display = 'none';
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (statusInput && !statusInput.contains(e.target) && !statusDropdown.contains(e.target)) {
                    statusDropdown.style.display = 'none';
                }
            });
        }

        function selectStatus(status) {
            statusInput.value = status.name;
            selectedStatusId.value = status.name;
            statusDropdown.style.display = 'none';
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

            // Calculate total work days
            const workersDays = parseInt(document.getElementById('workers_days')?.value) || 0;
            const supervisorsDays = parseInt(document.getElementById('supervisors_days')?.value) || 0;
            const managersDays = parseInt(document.getElementById('managers_days')?.value) || 0;
            const usersDays = parseInt(document.getElementById('users_days')?.value) || 0;

            const totalWorkDays = (workers * workersDays) + (supervisors * supervisorsDays) +
                                  (managers * managersDays) + (users * usersDays);

            const totalWorkDaysDisplay = document.getElementById('total_work_days_display');
            if (totalWorkDaysDisplay) {
                totalWorkDaysDisplay.value = totalWorkDays;
            }

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
            const subtotal = parseFloat(subtotalDisplay.value) || 0;
            const taxRate = parseFloat(document.getElementById('tax_rate').value) || 0;

            const taxAmount = (subtotal * taxRate) / 100;
            const total = subtotal + taxAmount;

            taxDisplay.value = taxAmount.toFixed(2);
            totalDisplay.value = total.toFixed(2);
        }

        // Event listeners for workforce inputs
        if(workersInput) {
            [workersInput, supervisorsInput, managersInput, usersInput].forEach(input => {
                input.addEventListener('input', calculateTotalWorkforce);
            });

            // Add listeners for work days inputs
            const workDaysInputs = document.querySelectorAll('.work-days-input');
            workDaysInputs.forEach(input => {
                input.addEventListener('input', calculateTotalWorkforce);
            });
        }

        // Event listeners for financial inputs
        if(subtotalDisplay) {
            subtotalDisplay.addEventListener('input', calculateFinancials);
            document.getElementById('tax_rate').addEventListener('input', calculateFinancials);
        }

        // Initialize calculations
        if(workersInput) calculateTotalWorkforce();
        if(workDaysInput) calculateFinancials();

        // Credit Note Modal Function
        window.openCreditNoteModal = function(invoiceId, invoiceNumber, totalAmount) {
            document.getElementById('invoice_id').value = invoiceId;
            document.getElementById('invoice_number_display').textContent = invoiceNumber;
            document.getElementById('total_amount_display').textContent = new Intl.NumberFormat('ar-SA').format(totalAmount);
            const modal = new bootstrap.Modal(document.getElementById('creditNoteModal'));
            modal.show();
        };

        // Delete Confirmation Function
        window.confirmDelete = function(invoiceId) {
            const form = document.getElementById('deleteInvoiceForm');
            form.action = `/invoices/${invoiceId}`;
            const modal = new bootstrap.Modal(document.getElementById('deleteInvoiceModal'));
            modal.show();
        };

        // Custom Status Toggle
        window.toggleCustomStatus = function() {
            const select = document.getElementById('invoice_status');
            const container = document.getElementById('custom_status_container');
            if (select.value === 'other') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        };

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

        // Export to PDF Function
        function exportInvoicesToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape orientation

            // Title (using English for now since Arabic needs special font)
            doc.setFontSize(18);
            doc.text('Invoices Report - تقرير الفواتير', doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });

            doc.setFontSize(10);
            const today = new Date();
            const dateStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
            doc.text('Report Date: ' + dateStr, doc.internal.pageSize.getWidth() / 2, 22, { align: 'center' });

            // Get table data
            const invoices = @json($invoices->items());

            const tableData = invoices.map(invoice => {
                const statusMap = {
                    'paid': 'Paid',
                    'pending': 'Pending',
                    'late': 'Late',
                    'overdue': 'Overdue',
                    'cancelled': 'Cancelled'
                };

                return [
                    invoice.number || '',
                    invoice.client?.name || '',
                    invoice.generation_date || '',
                    parseFloat(invoice.total_price || 0).toFixed(2) + ' SAR',
                    parseFloat(invoice.paid_amount || 0).toFixed(2) + ' SAR',
                    parseFloat(invoice.remaining_amount || 0).toFixed(2) + ' SAR',
                    statusMap[invoice.payment_status] || invoice.payment_status
                ];
            });

            // Add table
            doc.autoTable({
                head: [['Invoice #', 'Client', 'Issue Date', 'Total Amount', 'Paid Amount', 'Remaining', 'Status']],
                body: tableData,
                startY: 30,
                styles: {
                    font: 'helvetica',
                    fontSize: 9,
                    halign: 'center'
                },
                headStyles: {
                    fillColor: [30, 74, 70],
                    textColor: 255,
                    fontStyle: 'bold'
                },
                alternateRowStyles: {
                    fillColor: [245, 245, 245]
                }
            });

            // Save PDF
            doc.save('invoices_' + new Date().toISOString().split('T')[0] + '.pdf');

            if (window.toastr) toastr.success('Invoices exported to PDF successfully');
        }

        // Export to Excel Function
        function exportInvoicesToExcel() {
            const invoices = @json($invoices->items());

            const statusMap = {
                'paid': 'مدفوعة',
                'pending': 'قيد الانتظار',
                'late': 'متأخرة',
                'overdue': 'متأخرة',
                'cancelled': 'ملغاة'
            };

            const excelData = invoices.map(invoice => ({
                'رقم الفاتورة': invoice.number || '',
                'العميل': invoice.client?.name || '',
                'الخدمة': invoice.service?.name || '',
                'تاريخ الإصدار': invoice.generation_date || '',
                'تاريخ الاستحقاق': invoice.last_generation_date || '',
                'إجمالي العمالة': (invoice.total_workers || 0) + (invoice.total_supervisors || 0) + (invoice.total_managers || 0) + (invoice.total_users || 0),
                'أيام العمل': invoice.work_days || 0,
                'المبلغ الأساسي': parseFloat(invoice.base_price || 0).toFixed(2),
                'الضريبة': parseFloat(invoice.tax_amount || 0).toFixed(2),
                'المبلغ الإجمالي': parseFloat(invoice.total_price || 0).toFixed(2),
                'المبلغ المدفوع': parseFloat(invoice.paid_amount || 0).toFixed(2),
                'المبلغ المتبقي': parseFloat(invoice.remaining_amount || 0).toFixed(2),
                'حالة السداد': statusMap[invoice.payment_status] || invoice.payment_status,
                'حالة الفاتورة': invoice.invoice_status || ''
            }));

            const ws = XLSX.utils.json_to_sheet(excelData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'الفواتير');

            // Set column widths
            ws['!cols'] = [
                { wch: 15 }, { wch: 25 }, { wch: 20 }, { wch: 15 }, { wch: 15 },
                { wch: 12 }, { wch: 12 }, { wch: 15 }, { wch: 12 }, { wch: 15 },
                { wch: 15 }, { wch: 15 }, { wch: 15 }, { wch: 20 }
            ];

            XLSX.writeFile(wb, 'invoices_' + new Date().toISOString().split('T')[0] + '.xlsx');

            if (window.toastr) toastr.success('Invoices exported to Excel successfully');
        }
    </script>
@endpush
