@extends('layouts.master')

@section('title', 'إدارة المدفوعات')
@section('page_title', 'أوامر الدفع')
@section('page_subtitle', 'متابعة جميع العمليات المالية والمقبوضات')

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
        .pay-number {
            color: #10a37f;
            font-weight: 700;
            font-family: 'Outfit', sans-serif;
        }
        .payment-amount {
            color: #38a169;
            font-weight: 700;
            font-size: 1rem;
        }
        .ref-number {
            font-family: 'Outfit', sans-serif;
            font-size: 0.8rem;
            color: #718096;
            background: #f1f5f9;
            padding: 2px 8px;
            border-radius: 4px;
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
        .status-badge.completed { background: #def7ec; color: #03543f; }
        .status-badge.pending { background: #fef3c7; color: #92400e; }
        .status-badge.failed { background: #fde8e8; color: #9b1c1c; }

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
                    type="button" id="exportDropdownPayments" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i>
                <span>تصدير</span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="exportDropdownPayments">
                <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportPaymentsToPDF()">
                        <i class="bi bi-file-pdf text-danger me-2"></i>تصدير PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="javascript:void(0)" onclick="exportPaymentsToExcel()">
                        <i class="bi bi-file-excel text-success me-2"></i>تصدير Excel
                    </a>
                </li>
            </ul>
        </div>
        <a href="{{ route('payments.create') }}" class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg"></i>
            <span>إضافة دفعة</span>
        </a>
    </div>
@endsection

@section('content')
    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['completed'] ?? 0 }}</h3>
                <p>مكتملة</p>
            </div>
            <div class="stat-icon-box" style="background: #e6fffa; color: #319795;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['pending'] ?? 0 }}</h3>
                <p>قيد الانتظار</p>
            </div>
            <div class="stat-icon-box" style="background: #fffaf0; color: #dd6b20;">
                <i class="bi bi-clock-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3 class="text-danger">{{ $stats['cancelled'] ?? 0 }}</h3>
                <p>ملغاة</p>
            </div>
            <div class="stat-icon-box" style="background: #fff5f5; color: #e53e3e;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['total_amount'] ?? 0) }}</h3>
                <p>إجمالي المبالغ</p>
            </div>
            <div class="stat-icon-box" style="background: #ebf8ff; color: #3182ce;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('payments.index') }}" id="paymentFilterForm">
        <div class="bg-white rounded-xl border border-gray-100 p-3 mb-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3 flex-grow-1">
                <div class="search-box ms-0" style="width: 300px; background: #fcfcfc; border: 1px solid #f0f0f0;">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث برقم الدفع أو العميل..." style="font-size: 0.85rem;" onchange="this.form.submit()">
                </div>
                <select name="status" class="form-select border-0 bg-light rounded-xl" style="width: 150px; font-size: 0.85rem;" onchange="this.form.submit()">
                    <option value="">جميع الحالات</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                @include('components.export-dropdown')
            </div>
        </div>
    </form>

    <!-- Payments Table -->
    <div class="table-card" id="payments-table-container">
        <div class="table-responsive">
            <table class="custom-table" id="payments-table">
                <thead>
                    <tr>
                        <th>رقم الدفعة</th>
                        <th>العميل</th>
                        <th>رقم الفاتورة</th>
                        <th>تاريخ الدفع</th>
                        <th>المبلغ</th>
                        <th>طريقة الدفع</th>
                        <th>الحالة</th>
                        <th class="text-center">رقم المرجع</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><span class="pay-number">{{ $payment->number }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $payment->invoice->client->name ?? '—' }}</div>
                        </td>
                        <td><span class="text-muted">{{ $payment->invoice->number ?? '—' }}</span></td>
                        <td>
                            <div>{{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '—' }}</div>
                            @if(isset($payment->late_days) && $payment->late_days > 0)
                                <small class="badge bg-danger mt-1">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>متأخر {{ $payment->late_days }} يوم
                                </small>
                            @endif
                        </td>
                        <td><span class="payment-amount">{{ number_format($payment->amount, 0) }} ر.س</span></td>
                        <td>
                            @php
                                $methodMap = [
                                    'bank_transfer' => 'تحويل بنكي',
                                    'direct_bank_transfer' => 'تحويل بنكي مباشر',
                                    'bank_wage_protection_transfer' => 'حماية أجور',
                                    'cash' => 'نقدي',
                                    'check' => 'شيك',
                                ];
                            @endphp
                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                <i class="bi bi-wallet2 me-1"></i>
                                {{ $methodMap[$payment->payment_method] ?? $payment->payment_method ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'completed' => ['class' => 'completed', 'label' => 'مكتملة', 'icon' => 'check-circle-fill'],
                                    'pending' => ['class' => 'pending', 'label' => 'معلقة', 'icon' => 'hourglass-split'],
                                    'failed' => ['class' => 'failed', 'label' => 'فشلت', 'icon' => 'x-circle-fill'],
                                    'cancelled' => ['class' => 'failed', 'label' => 'ملغاة', 'icon' => 'x-circle-fill'],
                                ];
                                $s = $statusMap[$payment->status] ?? $statusMap['pending'];
                            @endphp
                            <span class="status-badge {{ $s['class'] }}">
                                <i class="bi bi-{{ $s['icon'] }}"></i>
                                {{ $s['label'] }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="ref-number">{{ $payment->reference_number ?? '—' }}</span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('payments.show', $payment->id) }}" class="btn-action" title="عرض"><i class="bi bi-eye"></i></a>
                                <a href="{{ route('payments.edit', $payment->id) }}" class="btn-action" title="تعديل"><i class="bi bi-pencil"></i></a>
                                <button type="button" class="btn-action text-danger" title="حذف" onclick="confirmDeletePayment({{ $payment->id }})"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                                <div class="mb-3" style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-cash-stack fs-2 text-muted"></i>
                                </div>
                                <h6 class="text-muted mb-1">لا يوجد دفعات</h6>
                                <p class="text-muted small mb-0">لم يتم تسجيل أي دفعات بعد</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($payments->hasPages())
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted small">
                عرض {{ $payments->firstItem() ?? 0 }} إلى {{ $payments->lastItem() ?? 0 }} من {{ $payments->total() ?? 0 }} دفعة
            </div>
            {{ $payments->appends(request()->query())->links() }}
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deletePaymentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        تأكيد حذف الدفعة
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script>
        // Delete confirmation function
        function confirmDeletePayment(paymentId) {
            const form = document.getElementById('deletePaymentForm');
            form.action = `/payments/${paymentId}`;
            const modal = new bootstrap.Modal(document.getElementById('deletePaymentModal'));
            modal.show();
        }

        // Export to PDF Function (html2pdf)
        function exportPaymentsToPDF() {
            if (typeof html2pdf === 'undefined') {
                alert('جاري تحميل مكتبة PDF، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const companyLogo = '{{ asset("assets/img/logo.png") }}';
            const today = new Date().toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
            const todayShort = new Date().toISOString().split('T')[0];

            const stats = {
                completed: {{ $stats['completed'] ?? 0 }},
                pending: {{ $stats['pending'] ?? 0 }},
                cancelled: {{ $stats['cancelled'] ?? 0 }},
                total_amount: '{{ number_format($stats["total_amount"] ?? 0) }}'
            };

            // Build table rows from visible table
            let tableRows = '';
            document.querySelectorAll('.custom-table tbody tr').forEach((row, i) => {
                const cells = row.querySelectorAll('td');
                if (!cells.length || cells.length < 8) return;

                const bg = i % 2 === 0 ? '#ffffff' : '#f8fafc';
                const payNumber   = cells[0]?.innerText.trim() || '-';
                const clientName  = cells[1]?.innerText.trim() || '-';
                const invNumber   = cells[2]?.innerText.trim() || '-';
                const payDate     = cells[3]?.innerText.trim().split('\n')[0] || '-';
                const amount      = cells[4]?.innerText.trim() || '-';
                const method      = cells[5]?.innerText.trim() || '-';
                const statusText  = cells[6]?.innerText.trim() || '-';
                const refNumber   = cells[7]?.innerText.trim() || '-';

                let statusBg = '#e2e8f0', statusColor = '#334155';
                if (statusText.includes('مكتملة')) { statusBg='#d1fae5'; statusColor='#065f46'; }
                else if (statusText.includes('معلقة')) { statusBg='#fef3c7'; statusColor='#92400e'; }
                else if (statusText.includes('فشلت') || statusText.includes('ملغ')) { statusBg='#fee2e2'; statusColor='#991b1b'; }

                const td = 'padding:8px 10px;border-bottom:1px solid #e2e8f0;font-size:11px;vertical-align:middle;';
                tableRows += `
                <tr style="background:${bg};">
                    <td style="${td}text-align:center;color:#10a37f;font-weight:700;">${payNumber}</td>
                    <td style="${td}font-weight:600;color:#1e293b;">${clientName}</td>
                    <td style="${td}text-align:center;color:#64748b;">${invNumber}</td>
                    <td style="${td}text-align:center;color:#64748b;">${payDate}</td>
                    <td style="${td}text-align:right;color:#059669;font-weight:700;">${amount}</td>
                    <td style="${td}text-align:center;color:#475569;">${method}</td>
                    <td style="${td}text-align:center;"><span style="background:${statusBg};color:${statusColor};padding:3px 12px;border-radius:12px;font-size:10px;font-weight:600;">${statusText}</span></td>
                    <td style="${td}text-align:center;color:#64748b;font-size:10px;">${refNumber}</td>
                </tr>`;
            });

            const html = `<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<title>تقرير المدفوعات</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Tahoma','Arial',sans-serif; direction:rtl; background:#fff; color:#1e293b; font-size:12px; padding:16px; }
.pdf-header { background:linear-gradient(135deg,#1e4a46,#2d6a65); color:white; padding:18px 24px; border-radius:12px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; }
.pdf-header-title { text-align:right; }
.pdf-header-title h1 { font-size:22px; font-weight:700; margin-bottom:6px; }
.pdf-header-title p { font-size:12px; opacity:0.85; }
.logo-box { display:flex; align-items:center; }
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:14px; }
.stat-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center; }
.stat-box .sl { font-size:10px; color:#64748b; margin-bottom:4px; }
.stat-box .sv { font-size:18px; font-weight:700; }
table { width:100%; border-collapse:collapse; font-size:11px; }
thead th { background:#1e4a46; color:#fff; padding:9px 8px; font-weight:600; white-space:nowrap; font-size:11px; }
tbody td { padding:8px 10px; border-bottom:1px solid #e2e8f0; vertical-align:middle; }
.pdf-footer { margin-top:16px; padding:12px 20px; background:#f8fafc; border-radius:8px; display:flex; justify-content:space-between; align-items:center; color:#64748b; font-size:10px; }
</style>
</head>
<body>
<div class="pdf-header">
  <div class="pdf-header-title">
    <h1>تقرير المدفوعات</h1>
    <p>نظام إدارة الفواتير — تقرير شامل لجميع المدفوعات</p>
  </div>
  <div class="logo-box">
    <img src="${companyLogo}" style="height:42px;" onerror="this.style.display='none'">
  </div>
</div>

<div class="stats-grid">
  <div class="stat-box"><div class="sl">مكتملة</div><div class="sv" style="color:#059669;">${stats.completed}</div></div>
  <div class="stat-box"><div class="sl">قيد الانتظار</div><div class="sv" style="color:#d97706;">${stats.pending}</div></div>
  <div class="stat-box"><div class="sl">ملغاة</div><div class="sv" style="color:#dc2626;">${stats.cancelled}</div></div>
  <div class="stat-box"><div class="sl">إجمالي المبالغ</div><div class="sv" style="color:#2563eb;">${stats.total_amount} ر.س</div></div>
</div>

<table>
  <thead>
    <tr>
      <th style="text-align:center;">رقم الدفعة</th>
      <th style="text-align:right;">العميل</th>
      <th style="text-align:center;">رقم الفاتورة</th>
      <th style="text-align:center;">تاريخ الدفع</th>
      <th style="text-align:right;">المبلغ</th>
      <th style="text-align:center;">طريقة الدفع</th>
      <th style="text-align:center;">الحالة</th>
      <th style="text-align:center;">رقم المرجع</th>
    </tr>
  </thead>
  <tbody>${tableRows}</tbody>
</table>

<div class="pdf-footer">
  <span style="font-weight:700;color:#1e4a46;">نظام إدارة الفواتير</span>
  <span>تقرير المدفوعات — تاريخ التصدير: ${today}</span>
</div>
</body>
</html>`;

            const container = document.createElement('div');
            container.innerHTML = html;
            document.body.appendChild(container);

            html2pdf().set({
                margin: [8, 8, 8, 8],
                filename: `تقرير_المدفوعات_${todayShort}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            }).from(container).save().then(() => {
                document.body.removeChild(container);
                if (window.toastr) toastr.success('تم تصدير المدفوعات إلى PDF بنجاح');
            }).catch(err => {
                console.error('PDF export error:', err);
                document.body.removeChild(container);
                alert('حدث خطأ أثناء تصدير PDF');
            });
        }

        // Export to Excel Function (XLSX)
        function exportPaymentsToExcel() {
            if (typeof XLSX === 'undefined') {
                alert('جاري تحميل مكتبة Excel، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const table = document.getElementById('payments-table');
            if (!table) {
                alert('لم يتم العثور على جدول للتصدير');
                return;
            }

            const tempTable = table.cloneNode(true);

            // Remove actions column (last column)
            tempTable.querySelectorAll('tr').forEach(row => {
                const lastCell = row.querySelector('th:last-child, td:last-child');
                if (lastCell) lastCell.remove();
            });

            // Remove buttons and non-printable elements
            tempTable.querySelectorAll('.no-print, .btn, .dropdown, .btn-action').forEach(el => el.remove());

            const ws = XLSX.utils.table_to_sheet(tempTable);

            // Auto-size columns
            const wscols = [];
            const range = XLSX.utils.decode_range(ws['!ref']);
            for (let C = range.s.c; C <= range.e.c; ++C) {
                let max_length = 0;
                for (let R = range.s.r; R <= range.e.r; ++R) {
                    const cell = ws[XLSX.utils.encode_cell({c: C, r: R})];
                    if (cell && cell.v) {
                        const cell_length = cell.v.toString().length;
                        if (cell_length > max_length) max_length = cell_length;
                    }
                }
                wscols.push({wch: Math.min(max_length + 2, 50)});
            }
            ws['!cols'] = wscols;

            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'المدفوعات');

            const todayShort = new Date().toISOString().split('T')[0];
            XLSX.writeFile(wb, `تقرير_المدفوعات_${todayShort}.xlsx`);

            if (window.toastr) toastr.success('تم تصدير المدفوعات إلى Excel بنجاح');
        }
    </script>
@endpush
