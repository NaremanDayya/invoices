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
        .service-detail-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 6px 4px;
        }

        .service-detail-name {
            font-size: 0.7rem;
            color: #6b7280;
            margin-bottom: 2px;
            white-space: nowrap;
        }

        .service-detail-value {
            font-size: 0.85rem;
            font-weight: 600;
            color: #111827;
            line-height: 1.1;
        }

        .service-detail-sub {
            font-size: 0.7rem;
            color: #9ca3af;
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
        @if(auth()->user()->hasPermission('add_invoices'))
        <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#createInvoiceModal">
            <i class="bi bi-plus-lg"></i>
            <span>فاتورة جديدة</span>
        </button>
        @endif
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
                        <th>تفاصيل الخدمة</th>
                        <th>تاريخ الإصدار</th>
                        <th>المبلغ</th>
                        <th>الضريبة</th>
                        <th>الإجمالي</th>
                        <th class="text-center">عدد الموظفين</th>
                        <th class="text-center">أيام العمل</th>
                        <th class="text-center">أيام التأخير</th>
                        <th class="text-center">إشعارات دائنة</th>
                        <th class="text-center">فترة الاستجابة</th>
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
                                    'type' => 'regular',
                                    'client' => (object)['name' => $i == 1 ? 'شركة النور للمقاولات' : ($i == 2 ? 'مؤسسة الفجر التجارية' : 'شركة البناء الحديث'), 'phone' => '966501234567+'],
                                    'service' => (object)['name' => 'خدمة الحراسة'],
                                    'service_details_data' => [],
                                    'generation_date' => \Carbon\Carbon::now(),
                                    'base_price' => 150000,
                                    'tax_amount' => 22500,
                                    'tax_rate' => 15,
                                    'total_price' => 172500,
                                    'paid_amount' => $i == 1 ? 172500 : 0,
                                    'total_workers' => 25 + ($i*10),
                                    'total_supervisors' => 0,
                                    'total_managers' => 0,
                                    'total_users' => 0,
                                    'work_days' => 21,
                                    'workers_days' => 21,
                                    'supervisors_days' => 0,
                                    'managers_days' => 0,
                                    'users_days' => 0,
                                    'employees_count' => 0,
                                    'work_days_count' => 0,
                                    'payment_status' => $i == 1 ? 'paid' : ($i == 2 ? 'pending' : 'late'),
                                    'price_difference' => $i % 2 == 0 ? 10000 : 0,
                                    'issue_delay_days' => 0,
                                    'late_days' => $i == 3 ? 5 : 0,
                                    'credit_notes_count' => 0
                                ]);
                            }
                        }
                    @endphp
                    @foreach($items as $invoice)
                    <tr>
                        <td>
                            <div class="d-flex flex-column gap-1">
                                <span class="inv-number">{{ $invoice->number }}</span>
                                @if($invoice->type === 'salary_invoice')
                                    <span class="badge bg-purple-100 text-purple-800" style="background: #f3e8ff; color: #7c3aed; font-size: 0.7rem; width: fit-content;">
                                        <i class="bi bi-cash-stack"></i> فاتورة رواتب
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="customer-info d-flex align-items-center gap-2">
                                @if($invoice->client && $invoice->client->logo)
                                    <img src="{{ asset('storage/' . $invoice->client->logo) }}" alt="{{ $invoice->client->name }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                @endif
                                <div>
                                    <span class="name">{{ $invoice->client->name ?? '—' }}</span>
                                    <span class="phone text-muted" dir="ltr">{{ $invoice->client->phone ?? '' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="align-middle">
                    <span class="fw-semibold text-dark">
                        {{ $invoice->service->name ?? '—' }}
                    </span>
                        </td>
                        <td class="align-middle">
                            @php
                                $details = collect($invoice->service_details_data ?? [])
                                    ->filter(fn ($d) => !empty($d['name']));
                            @endphp

                            @if($details->count())
                                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                    @foreach($details as $detail)
                                        @php
                                            $cleanName = trim(str_replace('عدد', '', $detail['name']));
                                        @endphp

                                        <div style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 2px 8px; text-align: center; min-width: 55px;">
                                            <div style="font-size: 0.7rem; color: #6c757d; line-height: 1.2; white-space: nowrap;">
                                                {{ $cleanName }}
                                            </div>
                                            <div style="font-size: 0.85rem; font-weight: 500; line-height: 1.3;">
                                                {{ $detail['value'] ?? ($detail['count'] ?? '—') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">—</span>
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
                                <a href="{{ route('invoices.credit-notes.index', $invoice->id) }}" class="badge rounded-pill bg-warning text-dark px-2 text-decoration-none" style="cursor: pointer;" title="عرض الإشعارات الدائنة">
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
                        <td class="text-center">
                            @if($invoice->type === 'salary_invoice' && $invoice->approved_at)
                                <span class="badge bg-info text-white">
                                    <i class="bi bi-clock-history me-1"></i>
                                    {{ $invoice->response_period_formatted }}
                                </span>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'paid' => ['class' => 'paid', 'label' => 'مدفوعة', 'icon' => 'check-circle-fill'],
                                    'partially_paid' => ['class' => 'partially-paid', 'label' => 'مدفوعة جزئياً', 'icon' => 'check-circle'],
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
                                @if(auth()->user()->hasPermission('add_invoices'))
                                    <a href="{{ route('invoices.edit', $invoice->id) }}" class="btn-action" title="تعديل"><i class="bi bi-pencil"></i></a>
                                @endif
                                @if($invoice->type !== 'salary_invoice' && auth()->user()->hasPermission('import_invoice_employees'))
                                    <button type="button" class="btn-action text-purple-600" title="استيراد رواتب" onclick="openSalaryImportModal({{ $invoice->id }})" style="color: #7c3aed;"><i class="bi bi-upload"></i></button>
                                @endif
                                @if(auth()->user()->hasPermission('add_invoice_payment'))
                                    <button type="button" class="btn-action text-success" title="إضافة دفعة" onclick="openPaymentModal({{ $invoice->id }}, '{{ $invoice->number }}', {{ $invoice->total_price }}, {{ $invoice->paid_amount ?? 0 }})"><i class="bi bi-cash-coin"></i></button>
                                @endif
                                @if(auth()->user()->hasPermission('add_credit_note'))
                                    <button type="button" class="btn-action text-warning" title="إشعار دائن" onclick="openCreditNoteModal({{ $invoice->id }}, '{{ $invoice->number }}', {{ $invoice->total_price }}, {{ $invoice->base_price }}, {{ $invoice->tax_rate }}, {{ $invoice->employees_count ?? 0 }}, {{ $invoice->work_days_count ?? 0 }})"><i class="bi bi-file-earmark-text"></i></button>
                                @endif
                                @if(auth()->user()->hasPermission('add_invoices'))
                                    <button type="button" class="btn-action text-danger" title="حذف" onclick="confirmDelete({{ $invoice->id }})"><i class="bi bi-trash"></i></button>
                                @endif
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

    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl rounded-3">
                <div class="modal-header bg-gradient-success text-white p-4">
                    <h5 class="modal-title fw-bold d-flex align-items-center">
                        <i class="bi bi-cash-coin ms-2"></i>
                        إضافة دفعة للفاتورة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form id="paymentForm" method="POST">
                    @csrf
                    <input type="hidden" name="invoice_id" id="payment_invoice_id">

                    <div class="modal-body p-4">
                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-info-circle-fill fs-4 ms-2"></i>
                                <div>
                                    <strong>معلومات الفاتورة:</strong>
                                    <div class="mt-2">
                                        <span class="badge bg-primary me-2">رقم الفاتورة: <span id="payment_invoice_number"></span></span>
                                        <span class="badge bg-success me-2">الإجمالي: <span id="payment_total_amount"></span> ر.س</span>
                                        <span class="badge bg-warning text-dark">المدفوع: <span id="payment_paid_amount"></span> ر.س</span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-danger">المتبقي: <span id="payment_remaining_amount"></span> ر.س</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">المبلغ المدفوع <span class="text-danger">*</span></label>
                                <input type="number" name="amount" id="payment_amount" class="form-control" step="0.01" min="0.01" required>
                                <small class="text-muted">الحد الأقصى: <span id="payment_max_amount"></span> ر.س</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">تاريخ الدفع <span class="text-danger">*</span></label>
                                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">طريقة الدفع <span class="text-danger">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="cash">نقدي</option>
                                    <option value="check">شيك</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">رقم المرجع</label>
                                <input type="text" name="reference_number" class="form-control" placeholder="رقم التحويل أو الشيك">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية..."></textarea>
                            </div>

                            <div class="col-12">
                                <div class="alert alert-success border-0 mb-0">
                                    <i class="bi bi-check-circle-fill ms-2"></i>
                                    <strong>حالة الفاتورة بعد الدفع:</strong>
                                    <span id="payment_status_preview" class="fw-bold"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle ms-1"></i>
                            تسجيل الدفعة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Invoice Modal -->

    <div class="modal fade" id="createInvoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-4">
                    <h5 class="modal-title fw-bold text-white flex items-center gap-2">
                        <i class="bi bi-file-earmark-plus-fill"></i>
                        إضافة فاتورة جديدة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createInvoiceForm" method="POST" action="{{ route('invoices.store') }}">
                    @csrf
                    <div class="modal-body p-4 bg-slate-50" style="max-height: 70vh; overflow-y: auto;">

                        <!-- Client Information -->
                        <div class="bg-white rounded-2xl shadow-sm p-4 mb-4 border border-slate-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="bi bi-person-fill text-emerald-600"></i>
                                <h6 class="fw-bold text-slate-700 mb-0">معلومات العميل</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-600">اختر العميل <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" id="clientSearchInput" placeholder="ابحث عن عميل..." autocomplete="off">
                                        <input type="hidden" name="client_id" id="selectedClientId" required>
                                        <div id="clientDropdown" class="list-group position-absolute w-100 shadow rounded-xl mt-1 border border-slate-200" style="display:none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                            <!-- Options will be populated by JS -->
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">البريد الإلكتروني</label>
                                    <input type="email" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50" id="clientEmail" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">الهاتف</label>
                                    <input type="text" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50" id="clientPhone" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-600">العنوان</label>
                                    <textarea class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50" id="clientAddress" rows="2" readonly></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-slate-600">الرقم الضريبي</label>
                                    <input type="text" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50" id="clientTaxNumber" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Invoice Information -->
                        <div class="bg-white rounded-2xl shadow-sm p-4 mb-4 border border-slate-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="bi bi-file-earmark-text-fill text-emerald-600"></i>
                                <h6 class="fw-bold text-slate-700 mb-0">معلومات الفاتورة</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">رقم الفاتورة <span class="text-danger">*</span></label>
                                    <input type="text" name="number" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('number') is-invalid @enderror" value="{{ old('number') }}" required>
                                    @error('number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">تاريخ الإصدار <span class="text-danger">*</span></label>
                                    <input type="date" name="generation_date" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('generation_date') is-invalid @enderror" value="{{ old('generation_date', now()->format('Y-m-d')) }}" required>
                                    @error('generation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">تاريخ الاستحقاق <span class="text-danger">*</span></label>
                                    <input type="date" name="last_generation_date" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('last_generation_date') is-invalid @enderror" value="{{ old('last_generation_date', now()->addDays(30)->format('Y-m-d')) }}" required>
                                    @error('last_generation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">نوع الخدمة <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" id="serviceSearchInput" placeholder="ابحث عن خدمة..." autocomplete="off">
                                        <input type="hidden" name="service_id" id="selectedServiceId" required>
                                        <div id="serviceDropdown" class="list-group position-absolute w-100 shadow rounded-xl mt-1 border border-slate-200" style="display:none; z-index: 1000; max-height: 200px; overflow-y: auto;">
                                            <!-- Options will be populated by JS -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Service Details Section -->
                        <div class="bg-white rounded-2xl shadow-sm p-4 mb-4 border border-slate-100" id="serviceDetailsSection" style="display: none;">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="bi bi-list-check text-emerald-600"></i>
                                <h6 class="fw-bold text-slate-700 mb-0" id="serviceDetailsSectionTitle">تفاصيل الخدمة</h6>
                            </div>
                            <div id="serviceDetailsContainer" class="row g-3">
                                <!-- Service details will be populated dynamically based on selected service -->
                            </div>

                            <!-- Workforce Summary -->
                            <div class="row mt-4 pt-4 border-t border-slate-200">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">إجمالي العمالة</label>
                                    <input type="text" id="total_workforce_display" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50 fw-bold" value="0" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-slate-600">إجمالي أيام العمل</label>
                                    <input type="text" id="total_work_days_display" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50 fw-bold" value="0" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Details -->
                        <div class="bg-white rounded-2xl shadow-sm p-4 mb-4 border border-slate-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="bi bi-calculator-fill text-emerald-600"></i>
                                <h6 class="fw-bold text-slate-700 mb-0">التفاصيل المالية</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label small fw-bold text-slate-600">المبلغ قبل الضريبة (﷼) <span class="text-danger">*</span></label>
                                    <input type="number" name="base_price" id="subtotal_display" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('base_price') is-invalid @enderror" value="{{ old('base_price', 0) }}" step="0.01" min="0" required>
                                    @error('base_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label small fw-bold text-slate-600">نسبة الضريبة (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="tax_rate" id="tax_rate" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('tax_rate') is-invalid @enderror" value="{{ old('tax_rate', 15) }}" step="0.1" min="0" max="100" required>
                                    @error('tax_rate')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label small fw-bold text-slate-600">قيمة الضريبة (﷼)</label>
                                    <input type="text" id="tax_amount_display" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50 fw-bold" value="0.00" readonly>
                                </div>
                                <div class="col-md-6 col-lg-3">
                                    <label class="form-label small fw-bold text-slate-600">المبلغ الإجمالي (﷼)</label>
                                    <input type="text" id="total_amount_display" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm bg-slate-50 fw-bold" value="0.00" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Payment & Status -->
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-600">حالة الفاتورة <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <select id="statusSelect2" name="invoice_status" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required>
                                    <option value="">اختر حالة الفاتورة...</option>
                                    @foreach($invoiceStatuses as $status)
                                        <option value="{{ $status->name }}" data-color="{{ $status->color }}" data-icon="{{ $status->icon }}">
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="bg-white rounded-2xl shadow-sm p-4 border border-slate-100">
                            <div class="flex items-center gap-2 mb-4">
                                <i class="bi bi-sticky-fill text-emerald-600"></i>
                                <h6 class="fw-bold text-slate-700 mb-0">ملاحظات إضافية</h6>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <textarea name="notes" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 @error('notes') is-invalid @enderror" rows="3" placeholder="أي ملاحظات إضافية حول الفاتورة...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer border-t border-slate-100 p-3 bg-white">
                        <button type="button" class="btn btn-light rounded-xl px-4 font-bold text-slate-600" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl px-6 py-2 fw-bold shadow-lg shadow-emerald-500/30 border-0 hover:scale-105 transition-transform">
                            <i class="bi bi-save-fill me-2"></i>
                            حفظ الفاتورة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <!-- Add Client Modal -->
    <div class="modal fade" id="addClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-4">
                    <h5 class="modal-title fw-bold text-white flex items-center gap-2">
                        <i class="bi bi-person-plus-fill"></i>
                        إضافة عميل جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addClientForm" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">اسم العميل <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required placeholder="أدخل اسم العميل">
                                <div class="invalid-feedback" id="nameError"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="example@email.com">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">الهاتف</label>
                                <input type="text" name="phone" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="05xxxxxxxx">
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">الرقم الضريبي <span class="text-danger">*</span></label>
                                <input type="text" name="tax_number" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required placeholder="أدخل 15 رقم" maxlength="15" pattern="[0-9]{15}">
                                <small class="text-muted">يجب أن يكون 15 رقم بالضبط</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">العنوان الوطني</label>
                                <textarea name="address" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" rows="3" placeholder="العنوان الوطني"></textarea>
                            </div>

                            <!-- Additional fields from your original createClientModal -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">يوم الدفع</label>
                                <input type="number" name="default_payment_day" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="1" max="31" value="1">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">أيام السماح</label>
                                <input type="number" name="grace_period_days" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" min="0" value="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-100 p-3 bg-white">
                        <button type="button" class="btn btn-light rounded-xl px-4 font-bold text-slate-600" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl px-6 py-2 fw-bold shadow-lg shadow-emerald-500/30 border-0 hover:scale-105 transition-transform">
                            <i class="bi bi-save-fill me-2"></i>
                            حفظ العميل
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        <!-- Add Service Modal -->
    <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-4">
                    <h5 class="modal-title fw-bold text-white flex items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        إضافة خدمة جديدة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addServiceForm" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">اسم الخدمة <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required placeholder="أدخل اسم الخدمة">
                                <div class="invalid-feedback" id="serviceNameError"></div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">الوصف</label>
                                <textarea name="description" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" rows="3" placeholder="أدخل وصف الخدمة (اختياري)"></textarea>
                            </div>

                            <!-- Additional fields that might be needed for services -->
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">سعر الوحدة (﷼)</label>
                                <input type="number" name="unit_price" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" step="0.01" min="0" placeholder="0.00">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">نوع الوحدة</label>
                                <select name="unit_type" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500">
                                    <option value="hour">ساعة</option>
                                    <option value="day">يوم</option>
                                    <option value="unit">وحدة</option>
                                    <option value="person">شخص</option>
                                    <option value="project">مشروع</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-100 p-3 bg-white">
                        <button type="button" class="btn btn-light rounded-xl px-4 font-bold text-slate-600" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl px-6 py-2 fw-bold shadow-lg shadow-emerald-500/30 border-0 hover:scale-105 transition-transform">
                            <i class="bi bi-save-fill me-2"></i>
                            حفظ الخدمة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted">
                عرض {{ $invoices->firstItem() ?? 0 }} إلى {{ $invoices->lastItem() ?? 0 }} من {{ $invoices->total() ?? 0 }} فاتورة
            </div>
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Include Credit Note Modal -->
    @include('partials.credit-note-modal')
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.18.5/package/dist/xlsx.full.min.js"></script>
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
                            document.getElementById('clientTaxNumber').value = selectedOption.getAttribute('data-taxNumber) || '';
                        });
                    }

                    // Workforce calculation
                    function calculateFinancials() {
                        const subtotalInput = document.getElementById('subtotal_display');
                        const taxRateInput = document.getElementById('tax_rate');
                        const taxDisplay = document.getElementById('tax_amount_display');
                        const totalDisplay = document.getElementById('total_amount_display');

                        if (!subtotalInput || !taxRateInput || !taxDisplay || !totalDisplay) return;

                        const subtotal = parseFloat(subtotalInput.value) || 0;
                        const taxRate = parseFloat(taxRateInput.value) || 15; // Default 15%

                        const taxAmount = (subtotal * taxRate) / 100;
                        const total = subtotal + taxAmount;

                        taxDisplay.value = taxAmount.toFixed(0);
                        totalDisplay.value = total.toFixed(0);
                    }// Inline create: Clients
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
                                        opt.setAttribute('data-taxNumber', data.client.tax_number || '');
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
                    // Financial calculation for modal
                    function calculateFinancials() {
                        const subtotalInput = document.getElementById('subtotal_display');
                        const taxRateInput = document.getElementById('tax_rate');
                        const taxAmountDisplay = document.getElementById('tax_amount_display');
                        const totalAmountDisplay = document.getElementById('total_amount_display');

                        if (!subtotalInput || !taxRateInput) return;

                        const subtotal = parseFloat(subtotalInput.value) || 0;
                        const taxRate = parseFloat(taxRateInput.value) || 0;

                        const taxAmount = (subtotal * taxRate) / 100;
                        const total = subtotal + taxAmount;

                        if (taxAmountDisplay) taxAmountDisplay.value = taxAmount.toFixed(0);
                        if (totalAmountDisplay) totalAmountDisplay.value = total.toFixed(0);
                    }

                    // Event listeners for workforce inputs
                    ['total_workers', 'total_supervisors', 'total_managers', 'total_users'].forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.addEventListener('input', calculateTotalWorkforce);
                        }
                    });

                    // Event listeners for financial inputs - including base_price (subtotal_display)
                    ['subtotal_display', 'tax_rate'].forEach(id => {
                        const element = document.getElementById(id);
                        if (element) {
                            element.addEventListener('input', calculateFinancials);
                        }
                    });

                    // Initialize calculation on modal open
                    const createInvoiceModalEl = document.getElementById('createInvoiceModal');
                    if (createInvoiceModalEl) {
                        createInvoiceModalEl.addEventListener('shown.bs.modal', function() {
                            calculateFinancials();
                        });
                    }

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
            document.getElementById('clientTaxNumber').value = client.tax_number || '';

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
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Select2 for invoice status
            $('#statusSelect2').select2({
                theme: 'bootstrap-5',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    },
                    searching: function() {
                        return "جاري البحث...";
                    }
                },
                placeholder: "ابحث عن حالة الفاتورة...",
                allowClear: true,
                width: '100%',
                templateResult: function(state) {
                    if (!state.id) return state.text;

                    const color = $(state.element).data('color');
                    const icon = $(state.element).data('icon');

                    const $state = $(
                        `<span>
                    <span class="badge rounded-pill me-2" style="background-color: ${color}; width: 12px; height: 12px;"></span>
                    ${icon ? `<i class="bi bi-${icon} me-2"></i>` : ''}
                    ${state.text}
                </span>`
                    );
                    return $state;
                },
                templateSelection: function(state) {
                    if (!state.id) return state.text;

                    const color = $(state.element).data('color');
                    const icon = $(state.element).data('icon');

                    const $selection = $(
                        `<span>
                    <span class="badge rounded-pill me-2" style="background-color: ${color}; width: 12px; height: 12px;"></span>
                    ${icon ? `<i class="bi bi-${icon} me-2"></i>` : ''}
                    ${state.text}
                </span>`
                    );
                    return $selection;
                }
            });

            // Initialize when modal opens
            const createInvoiceModalEl = document.getElementById('createInvoiceModal');
            if (createInvoiceModalEl) {
                createInvoiceModalEl.addEventListener('shown.bs.modal', function() {
                    $('#statusSelect2').select2('destroy');
                    $('#statusSelect2').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#createInvoiceModal')
                    });
                });
            }

            // Reset Select2 when modal is hidden
            createInvoiceModalEl.addEventListener('hidden.bs.modal', function() {
                $('#statusSelect2').val(null).trigger('change');
            });
        });
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
            const taxRateInput = document.getElementById('tax_rate');
            const taxRate = taxRateInput ? parseFloat(taxRateInput.value) || 0 : 15;

            const taxAmount = (subtotal * taxRate) / 100;
            const total = subtotal + taxAmount;

            if(taxDisplay) taxDisplay.value = taxAmount.toFixed(0);
            if(totalDisplay) totalDisplay.value = total.toFixed(0);
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
            const taxRateInput = document.getElementById('tax_rate');
            if(taxRateInput) {
                taxRateInput.addEventListener('input', calculateFinancials);
            }
        }

        // Initialize calculations
        if(workersInput) calculateTotalWorkforce();
        if(subtotalDisplay) calculateFinancials();

        // Delete Confirmation Function
        window.confirmDelete = function(invoiceId) {
            const form = document.getElementById('deleteInvoiceForm');
            form.action = `/invoices/${invoiceId}`;
            const modal = new bootstrap.Modal(document.getElementById('deleteInvoiceModal'));
            modal.show();
        };

        // Payment Modal Function
        let currentPaymentData = {
            invoiceId: 0,
            totalAmount: 0,
            paidAmount: 0,
            remainingAmount: 0
        };

        window.openPaymentModal = function(invoiceId, invoiceNumber, totalAmount, paidAmount) {
            currentPaymentData = {
                invoiceId: invoiceId,
                totalAmount: parseFloat(totalAmount),
                paidAmount: parseFloat(paidAmount),
                remainingAmount: parseFloat(totalAmount) - parseFloat(paidAmount)
            };

            document.getElementById('payment_invoice_id').value = invoiceId;
            document.getElementById('payment_invoice_number').textContent = invoiceNumber;
            document.getElementById('payment_total_amount').textContent = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(totalAmount);
            document.getElementById('payment_paid_amount').textContent = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(paidAmount);
            document.getElementById('payment_remaining_amount').textContent = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(currentPaymentData.remainingAmount);
            document.getElementById('payment_max_amount').textContent = new Intl.NumberFormat('ar-SA', {minimumFractionDigits: 2}).format(currentPaymentData.remainingAmount);

            const paymentAmountInput = document.getElementById('payment_amount');
            paymentAmountInput.max = currentPaymentData.remainingAmount;
            paymentAmountInput.value = '';

            updatePaymentStatus(0);

            const form = document.getElementById('paymentForm');
            form.action = `/invoices/${invoiceId}/payments`;
            form.reset();

            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        };

        function updatePaymentStatus(paymentAmount) {
            const newPaidAmount = currentPaymentData.paidAmount + parseFloat(paymentAmount || 0);
            const statusPreview = document.getElementById('payment_status_preview');

            if (newPaidAmount >= currentPaymentData.totalAmount) {
                statusPreview.textContent = 'مدفوعة بالكامل';
                statusPreview.className = 'fw-bold text-success';
            } else if (newPaidAmount > 0) {
                statusPreview.textContent = 'مدفوعة جزئياً';
                statusPreview.className = 'fw-bold text-warning';
            } else {
                statusPreview.textContent = 'معلقة';
                statusPreview.className = 'fw-bold text-danger';
            }
        }

        document.getElementById('payment_amount')?.addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            if (amount > currentPaymentData.remainingAmount) {
                this.value = currentPaymentData.remainingAmount.toFixed(0);
            }
            updatePaymentStatus(parseFloat(this.value) || 0);
        });

        document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const paymentAmount = parseFloat(document.getElementById('payment_amount').value) || 0;

            if (paymentAmount <= 0) {
                alert('يرجى إدخال مبلغ الدفعة');
                return false;
            }

            if (paymentAmount > currentPaymentData.remainingAmount) {
                alert('مبلغ الدفعة لا يمكن أن يكون أكبر من المبلغ المتبقي');
                return false;
            }

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> جاري الحفظ...';
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
                    alert(data.message || 'تم تسجيل الدفعة بنجاح');
                    window.location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء تسجيل الدفعة');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('حدث خطأ أثناء الاتصال بالخادم');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

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

        // Export to PDF Function with html2pdf.js
        function exportInvoicesToPDF() {
            if (typeof html2pdf === 'undefined') {
                alert('جاري تحميل مكتبة PDF، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const companyLogo = '{{ asset("assets/img/logo.png") }}';
            const today = new Date().toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
            const todayShort = new Date().toISOString().split('T')[0];
            
            const stats = {
                paid: {{ $stats['paid'] ?? 0 }},
                pending: {{ $stats['pending'] ?? 0 }},
                late: {{ $stats['late'] ?? 0 }},
                cancelled: {{ $stats['cancelled'] ?? 0 }},
                total: {{ $invoices->total() ?? 0 }}
            };

            // Build table rows from visible table
            let tableRows = '';
            document.querySelectorAll('.custom-table tbody tr').forEach((row, i) => {
                const cells = row.querySelectorAll('td');
                if (!cells.length) return;

                const bg = i % 2 === 0 ? '#ffffff' : '#f8fafc';
                // Extract invoice number cleanly (avoid salary badge text)
                const invNumberEl = cells[0]?.querySelector('.inv-number');
                const invNumber = invNumberEl ? invNumberEl.innerText.trim() : (cells[0]?.innerText.trim().split('\n')[0] || '-');
                const isSalary = cells[0]?.querySelector('.badge') !== null;
                const clientName = cells[1]?.querySelector('.name')?.innerText.trim() || cells[1]?.innerText.trim() || '-';
                const serviceName = cells[2]?.innerText.trim().replace(/\s+/g, ' ') || '-';
                const issueDate = cells[4]?.innerText.trim() || '-';
                const basePrice = cells[5]?.innerText.trim() || '-';
                const taxAmount = cells[6]?.innerText.trim() || '-';
                const totalPrice = cells[7]?.innerText.trim() || '-';
                const empCount = cells[8]?.innerText.trim() || '-';
                const workDays = cells[9]?.innerText.trim() || '-';
                // Status is at index 13 (after: inv#, client, service, details, date, base, tax, total, emp, days, delay, credit, response, status, actions)
                const statusText = cells[13]?.innerText.trim().split('\n')[0].trim() || '-';

                let statusBg = '#e2e8f0', statusColor = '#334155';
                if (statusText.includes('مدفوعة')) { statusBg='#d1fae5'; statusColor='#065f46'; }
                else if (statusText.includes('معلقة')) { statusBg='#fef3c7'; statusColor='#92400e'; }
                else if (statusText.includes('متأخرة')) { statusBg='#fee2e2'; statusColor='#991b1b'; }
                else if (statusText.includes('ملغاة')) { statusBg='#f3f4f6'; statusColor='#4b5563'; }

                const td = 'padding:7px 8px;border-bottom:1px solid #e2e8f0;font-size:10px;vertical-align:middle;';
                const salaryTag = isSalary ? `<span style="background:#f3e8ff;color:#7c3aed;font-size:8px;padding:1px 5px;border-radius:6px;margin-right:4px;">رواتب</span>` : '';
                tableRows += `
                <tr style="background:${bg};">
                    <td style="${td}text-align:center;color:#10a37f;font-weight:700;">${invNumber}${salaryTag}</td>
                    <td style="${td}font-weight:600;color:#1e293b;">${clientName}</td>
                    <td style="${td}color:#475569;">${serviceName}</td>
                    <td style="${td}text-align:center;color:#64748b;">${issueDate}</td>
                    <td style="${td}text-align:right;color:#2563eb;">${basePrice}</td>
                    <td style="${td}text-align:right;color:#d97706;">${taxAmount}</td>
                    <td style="${td}text-align:right;font-weight:700;color:#059669;">${totalPrice}</td>
                    <td style="${td}text-align:center;"><span style="background:#e6fffa;color:#319795;padding:2px 7px;border-radius:50%;font-weight:700;font-size:9px;">${empCount}</span></td>
                    <td style="${td}text-align:center;color:#64748b;">${workDays}</td>
                    <td style="${td}text-align:center;"><span style="background:${statusBg};color:${statusColor};padding:3px 10px;border-radius:12px;font-size:9px;font-weight:600;">${statusText}</span></td>
                </tr>`;
            });

            const html = `<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<title>تقرير الفواتير</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Tahoma','Arial',sans-serif; direction:rtl; background:#fff; color:#1e293b; font-size:12px; padding:16px; word-spacing:normal; letter-spacing:normal; }
.pdf-header { background:linear-gradient(135deg,#1e4a46,#2d6a65); color:white; padding:18px 24px; border-radius:12px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; }
.pdf-header-title { text-align:right; }
.pdf-header-title h1 { font-size:22px; font-weight:700; margin-bottom:6px; }
.pdf-header-title p { font-size:12px; opacity:0.85; }
.logo-box { display:flex; align-items:center; }
.stats-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:10px; margin-bottom:14px; }
.stat-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center; }
.stat-box .sl { font-size:10px; color:#64748b; margin-bottom:4px; }
.stat-box .sv { font-size:18px; font-weight:700; }
table { width:100%; border-collapse:collapse; font-size:10px; }
thead th { background:#1e4a46; color:#fff; padding:9px 8px; font-weight:600; white-space:nowrap; font-size:10px; }
tbody td { padding:7px 8px; border-bottom:1px solid #e2e8f0; vertical-align:middle; }
.pdf-footer { margin-top:16px; padding:12px 20px; background:#f8fafc; border-radius:8px; display:flex; justify-content:space-between; align-items:center; color:#64748b; font-size:10px; }
</style>
</head>
<body>
<div class="pdf-header">
  <div class="pdf-header-title">
    <h1>تقرير الفواتير</h1>
    <p>نظام إدارة الفواتير — تقرير شامل لجميع الفواتير</p>
  </div>
  <div class="logo-box">
    <img src="${companyLogo}" style="height:42px;" onerror="this.style.display='none'">
  </div>
</div>

<div class="stats-grid">
  <div class="stat-box"><div class="sl">إجمالي الفواتير</div><div class="sv" style="color:#0284c7;">${stats.total}</div></div>
  <div class="stat-box"><div class="sl">مدفوعة</div><div class="sv" style="color:#059669;">${stats.paid}</div></div>
  <div class="stat-box"><div class="sl">معلقة</div><div class="sv" style="color:#d97706;">${stats.pending}</div></div>
  <div class="stat-box"><div class="sl">متأخرة</div><div class="sv" style="color:#dc2626;">${stats.late}</div></div>
  <div class="stat-box"><div class="sl">ملغاة</div><div class="sv" style="color:#6b7280;">${stats.cancelled}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th style="text-align:center;">رقم الفاتورة</th>
      <th style="text-align:right;">العميل</th>
      <th style="text-align:right;">الخدمة</th>
      <th style="text-align:center;">تاريخ الإصدار</th>
      <th style="text-align:right;">المبلغ الأساسي</th>
      <th style="text-align:right;">الضريبة</th>
      <th style="text-align:right;">الإجمالي</th>
      <th style="text-align:center;">الموظفين</th>
      <th style="text-align:center;">أيام العمل</th>
      <th style="text-align:center;">الحالة</th>
    </tr>
  </thead>
  <tbody>${tableRows}</tbody>
</table>

<div class="pdf-footer">
  <span style="font-weight:700;color:#1e4a46;">نظام إدارة الفواتير</span>
  <span>تقرير الفواتير — تاريخ التصدير: ${today}</span>
</div>
</body>
</html>`;

            const container = document.createElement('div');
            container.innerHTML = html;
            document.body.appendChild(container);

            html2pdf().set({
                margin: [8, 8, 8, 8],
                filename: `تقرير_الفواتير_${todayShort}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
            }).from(container).save().then(() => {
                document.body.removeChild(container);
                if (window.toastr) toastr.success('تم تصدير الفواتير إلى PDF بنجاح');
            });
        }

        // Export to Excel Function
        function exportInvoicesToExcel() {
            if (typeof XLSX === 'undefined') {
                alert('جاري تحميل مكتبة Excel، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const todayShort = new Date().toISOString().split('T')[0];

            // Clone the visible table
            const originalTable = document.querySelector('.custom-table');
            const clonedTable = originalTable.cloneNode(true);

            // Remove last header (actions)
            clonedTable.querySelectorAll('thead tr').forEach(row => {
                const cells = row.querySelectorAll('th');
                if (cells.length) cells[cells.length - 1].remove();
            });

            // Clean body rows
            clonedTable.querySelectorAll('tbody tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (!cells.length) return;

                // Flatten invoice number cell (remove badge)
                const invNum = cells[0]?.querySelector('.inv-number');
                if (invNum) {
                    const isSal = cells[0].querySelector('.badge') !== null;
                    cells[0].innerHTML = invNum.innerText.trim() + (isSal ? ' (رواتب)' : '');
                }

                // Flatten client cell
                const nameEl = cells[1]?.querySelector('.name');
                if (nameEl) cells[1].innerHTML = nameEl.innerText.trim();

                // Flatten service details cell — keep plain text only
                if (cells[3]) cells[3].innerText = cells[3].innerText.trim().replace(/\s+/g, ' ');

                // Remove last cell (actions)
                cells[cells.length - 1].remove();
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(clonedTable);

            // Auto-size columns
            const range = XLSX.utils.decode_range(ws['!ref']);
            const colWidths = [];
            for (let C = range.s.c; C <= range.e.c; C++) {
                let maxLen = 10;
                for (let R = range.s.r; R <= range.e.r; R++) {
                    const cell = ws[XLSX.utils.encode_cell({ r: R, c: C })];
                    if (cell && cell.v) maxLen = Math.max(maxLen, String(cell.v).length + 4);
                }
                colWidths.push({ wch: Math.min(maxLen, 40) });
            }
            ws['!cols'] = colWidths;

            XLSX.utils.book_append_sheet(wb, ws, 'الفواتير');
            XLSX.writeFile(wb, `تقرير_الفواتير_${todayShort}.xlsx`);

            if (window.toastr) toastr.success('تم تصدير الفواتير إلى Excel بنجاح');
        }
    </script>
@endpush

@include('partials.salary-invoice-import-modal')
