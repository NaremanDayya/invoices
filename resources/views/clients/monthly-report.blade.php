@extends('layouts.master')

@section('title', 'التقرير الشهري - ' . $client->name)
@section('page_title', 'التقرير الشهري')
@section('page_subtitle', 'تقرير شهري مفصل لفواتير العميل')

@push('styles')
<style>
    .report-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border: 1px solid #edf2f7;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .stat-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .stat-box.success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    .stat-box.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .stat-box.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .stat-box h3 {
        font-size: 2rem;
        font-weight: 800;
        margin: 10px 0;
    }
    .stat-box p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }
    .invoice-row {
        border-bottom: 1px solid #f0f0f0;
        padding: 15px 0;
    }
    .invoice-row:last-child {
        border-bottom: none;
    }
    .payment-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .payment-badge.paid {
        background: #def7ec;
        color: #03543f;
    }
    .payment-badge.partially_paid {
        background: #fef3c7;
        color: #92400e;
    }
    .payment-badge.pending {
        background: #fde8e8;
        color: #9b1c1c;
    }
</style>
@endpush

@section('page_actions')
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-danger rounded-xl px-4 py-2 fw-bold" onclick="exportMonthlyReportPDF()">
            <i class="bi bi-file-pdf me-2"></i>تصدير PDF
        </button>
        <button type="button" class="btn btn-success rounded-xl px-4 py-2 fw-bold" onclick="exportMonthlyReportExcel()">
            <i class="bi bi-file-excel me-2"></i>تصدير Excel
        </button>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-arrow-right me-2"></i>رجوع
        </a>
    </div>
@endsection

@section('content')
    <!-- Client & Period Info -->
    <div class="report-card">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($client->logo)
                        <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name }}" class="rounded" style="width: 60px; height: 60px; object-fit: contain;">
                    @endif
                    <div>
                        <h4 class="fw-bold mb-1">{{ $client->name }}</h4>
                        <p class="text-muted mb-0">
                            <i class="bi bi-calendar3 me-2"></i>
                            الفترة: {{ \Carbon\Carbon::parse($period['start'])->locale('ar')->translatedFormat('d F Y') }} - 
                            {{ \Carbon\Carbon::parse($period['end'])->locale('ar')->translatedFormat('d F Y') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <form method="GET" action="{{ route('clients.monthly-report', $client) }}" class="d-inline-flex gap-2">
                    <input type="month" name="month" value="{{ $month }}" class="form-control" style="width: auto;">
                    <button type="submit" class="btn btn-primary">عرض</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-box info">
                <p>عدد الفواتير</p>
                <h3>{{ $summary['total_invoices'] }}</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <p>إجمالي المبلغ</p>
                <h3>{{ number_format($summary['total_invoiced'], 0) }}</h3>
                <p>ر.س</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box success">
                <p>المبلغ المدفوع</p>
                <h3>{{ number_format($summary['total_paid'], 0) }}</h3>
                <p>ر.س</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box warning">
                <p>المبلغ المتبقي</p>
                <h3>{{ number_format($summary['total_remaining'], 0) }}</h3>
                <p>ر.س</p>
            </div>
        </div>
    </div>

    <!-- Invoice Breakdown -->
    <div class="report-card">
        <h5 class="fw-bold mb-4">
            <i class="bi bi-file-text me-2"></i>
            تفاصيل الفواتير
        </h5>

        @if($invoices->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">رقم الفاتورة</th>
                            <th class="text-center">التاريخ</th>
                            <th class="text-center">المبلغ الإجمالي</th>
                            <th class="text-center">المدفوع</th>
                            <th class="text-center">المتبقي</th>
                            <th class="text-center">إشعارات دائنة</th>
                            <th class="text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr>
                            <td class="text-center">
                                <strong class="text-primary">{{ $invoice['number'] }}</strong>
                            </td>
                            <td class="text-center">
                                <i class="bi bi-calendar3 me-1 text-muted"></i>
                                {{ $invoice['date'] }}
                            </td>
                            <td class="text-center">
                                <strong>{{ number_format($invoice['total_amount'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-center">
                                <strong class="text-success">{{ number_format($invoice['paid_amount'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-center">
                                <strong class="{{ $invoice['remaining_balance'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($invoice['remaining_balance'], 0) }}
                                </strong> ر.س
                            </td>
                            <td class="text-center">
                                @if($invoice['credit_notes'] > 0)
                                    <strong class="text-warning">{{ number_format($invoice['credit_notes'], 0) }}</strong> ر.س
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    // Check if invoice is paid, partially paid, or pending based on amounts
                                    if ($invoice['remaining_balance'] <= 0) {
                                        $statusClass = 'paid';
                                        $statusLabel = 'مدفوعة';
                                    } elseif ($invoice['paid_amount'] > 0) {
                                        $statusClass = 'partially_paid';
                                        $statusLabel = 'مدفوعة جزئياً';
                                    } else {
                                        $statusClass = 'pending';
                                        $statusLabel = 'معلقة';
                                    }
                                @endphp
                                <span class="payment-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end">الإجماليات:</td>
                            <td class="text-end">
                                <strong class="fs-5 text-primary">{{ number_format($summary['total_invoiced'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-end">
                                <strong class="fs-5 text-success">{{ number_format($summary['total_paid'], 0) }}</strong> ر.س
                            </td>
                            <td class="text-end">
                                <strong class="fs-5 {{ $summary['total_remaining'] > 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($summary['total_remaining'], 0) }}
                                </strong> ر.س
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h5 class="text-muted mt-3">لا توجد فواتير في هذا الشهر</h5>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function exportMonthlyReportPDF() {
        if (typeof html2pdf === 'undefined') {
            alert('جاري تحميل مكتبة PDF، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
            return;
        }

        const clientName   = '{{ $client->name }}';
        const clientLogo   = '{{ $client->logo ? asset("storage/" . $client->logo) : "" }}';
        const companyLogo  = '{{ asset("assets/img/logo.png") }}';
        const month        = '{{ $month }}';
        const periodStart  = '{{ \Carbon\Carbon::parse($period["start"])->locale("ar")->translatedFormat("d F Y") }}';
        const periodEnd    = '{{ \Carbon\Carbon::parse($period["end"])->locale("ar")->translatedFormat("d F Y") }}';
        const today        = new Date().toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
        const todayShort   = new Date().toISOString().split('T')[0];

        const summary = {
            total_invoices:  {{ $summary['total_invoices'] }},
            total_invoiced:  {{ $summary['total_invoiced'] }},
            total_paid:      {{ $summary['total_paid'] }},
            total_remaining: {{ $summary['total_remaining'] }}
        };

        const clientLogoHtml = clientLogo
            ? `<img src="${clientLogo}" style="height:50px;width:auto;object-fit:contain;border-radius:8px;" onerror="this.style.display='none'" />`
            : `<div style="width:50px;height:50px;background:#e2e8f0;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#475569;">${clientName.charAt(0)}</div>`;

        // Build table rows from the visible table
        let tableRows = '';
        document.querySelectorAll('.table tbody tr').forEach((row, i) => {
            const cells = row.querySelectorAll('td');
            if (!cells.length) return;

            const bg = i % 2 === 0 ? '#ffffff' : '#f8fafc';
            const invNumber   = cells[0]?.innerText.trim() || '-';
            const date        = cells[1]?.innerText.trim() || '-';
            const totalAmount = cells[2]?.innerText.trim() || '-';
            const paidAmount  = cells[3]?.innerText.trim() || '-';
            const remaining   = cells[4]?.innerText.trim() || '-';
            const creditNotes = cells[5]?.innerText.trim() || '-';
            const status      = cells[6]?.innerText.trim() || '-';

            let statusBg = '#e2e8f0', statusColor = '#334155';
            if (status.includes('مدفوعة') && !status.includes('جزئ')) { statusBg='#d1fae5'; statusColor='#065f46'; }
            else if (status.includes('جزئ')) { statusBg='#fef3c7'; statusColor='#92400e'; }
            else if (status.includes('معلقة')) { statusBg='#fee2e2'; statusColor='#991b1b'; }

            const td = 'padding:8px 10px;border-bottom:1px solid #e2e8f0;font-size:11px;vertical-align:middle;text-align:center;';
            tableRows += `
            <tr style="background:${bg};">
                <td style="${td}color:#10a37f;font-weight:700;">${invNumber}</td>
                <td style="${td}color:#64748b;">${date}</td>
                <td style="${td}font-weight:600;color:#1e293b;">${totalAmount}</td>
                <td style="${td}color:#059669;font-weight:600;">${paidAmount}</td>
                <td style="${td}color:#dc2626;font-weight:600;">${remaining}</td>
                <td style="${td}color:#d97706;">${creditNotes}</td>
                <td style="${td}"><span style="background:${statusBg};color:${statusColor};padding:3px 12px;border-radius:12px;font-size:10px;font-weight:600;">${status}</span></td>
            </tr>`;
        });

        function fmtNum(n) { return new Intl.NumberFormat('ar-SA').format(n); }

        const html = `<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<title>التقرير الشهري - ${clientName}</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Tahoma','Arial',sans-serif; direction:rtl; background:#fff; color:#1e293b; font-size:12px; padding:16px; }
.pdf-header { background:linear-gradient(135deg,#1e4a46,#2d6a65); color:white; padding:20px 28px; border-radius:12px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; }
.header-right { text-align:right; }
.header-right h1 { font-size:22px; font-weight:700; margin-bottom:6px; }
.header-right p { font-size:12px; opacity:0.85; }
.logos { display:flex; align-items:center; gap:16px; }
.divider { width:1px; height:45px; background:rgba(255,255,255,0.3); }
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:16px; }
.stat-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px; text-align:center; }
.stat-box .sl { font-size:10px; color:#64748b; margin-bottom:4px; }
.stat-box .sv { font-size:20px; font-weight:700; }
.totals-bar { background:linear-gradient(135deg,#1e4a46,#2d6a65); border-radius:10px; padding:14px 24px; display:flex; justify-content:space-around; align-items:center; color:white; margin-bottom:16px; }
.fi { text-align:center; }
.fi .fl { font-size:10px; opacity:0.8; margin-bottom:3px; }
.fi .fv { font-size:17px; font-weight:700; }
.fi .fv.gold { color:#fbbf24; }
.fi .fv.green { color:#6ee7b7; }
.fi .fv.red { color:#fca5a5; }
table { width:100%; border-collapse:collapse; font-size:11px; }
thead th { background:#1e4a46; color:#fff; padding:10px 10px; font-weight:600; white-space:nowrap; font-size:11px; text-align:center; }
tbody td { padding:8px 10px; border-bottom:1px solid #e2e8f0; vertical-align:middle; text-align:center; }
.pdf-footer { margin-top:20px; padding:14px 24px; background:#f1f5f9; border-radius:10px; display:flex; justify-content:space-between; align-items:center; color:#475569; font-size:11px; }
</style>
</head>
<body>
<div class="pdf-header">
  <div class="header-right">
    <h1>التقرير الشهري للعميل</h1>
    <p style="margin-bottom:4px;">العميل: ${clientName}</p>
    <p>الفترة: ${periodStart} - ${periodEnd}</p>
  </div>
  <div class="logos">
    ${clientLogoHtml}
    <div class="divider"></div>
    <img src="${companyLogo}" style="height:42px;" onerror="this.style.display='none'">
  </div>
</div>

<div class="stats-grid">
  <div class="stat-box"><div class="sl">عدد الفواتير</div><div class="sv" style="color:#0284c7;">${summary.total_invoices}</div></div>
  <div class="stat-box"><div class="sl">إجمالي المبلغ</div><div class="sv" style="color:#1e293b;">${fmtNum(summary.total_invoiced)} ر.س</div></div>
  <div class="stat-box"><div class="sl">المبلغ المدفوع</div><div class="sv" style="color:#059669;">${fmtNum(summary.total_paid)} ر.س</div></div>
  <div class="stat-box"><div class="sl">المبلغ المتبقي</div><div class="sv" style="color:#dc2626;">${fmtNum(summary.total_remaining)} ر.س</div></div>
</div>

<div class="totals-bar">
  <div class="fi"><div class="fl">إجمالي الفواتير</div><div class="fv gold">${fmtNum(summary.total_invoiced)} ر.س</div></div>
  <div style="width:1px;height:36px;background:rgba(255,255,255,0.2);"></div>
  <div class="fi"><div class="fl">المدفوع</div><div class="fv green">${fmtNum(summary.total_paid)} ر.س</div></div>
  <div style="width:1px;height:36px;background:rgba(255,255,255,0.2);"></div>
  <div class="fi"><div class="fl">المتبقي</div><div class="fv red">${fmtNum(summary.total_remaining)} ر.س</div></div>
</div>

<table>
  <thead>
    <tr>
      <th>رقم الفاتورة</th>
      <th>التاريخ</th>
      <th>المبلغ الإجمالي</th>
      <th>المدفوع</th>
      <th>المتبقي</th>
      <th>إشعارات دائنة</th>
      <th>الحالة</th>
    </tr>
  </thead>
  <tbody>${tableRows}</tbody>
</table>

<div class="pdf-footer">
  <span style="font-weight:700;color:#1e4a46;">نظام إدارة الفواتير</span>
  <span>التقرير الشهري — ${clientName} — ${month}</span>
  <span>تاريخ التصدير: ${today}</span>
</div>
</body>
</html>`;

        const container = document.createElement('div');
        container.innerHTML = html;
        document.body.appendChild(container);

        html2pdf().set({
            margin: [10, 10, 10, 10],
            filename: `التقرير_الشهري_${clientName}_${month}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true, logging: false },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        }).from(container).save().then(() => {
            document.body.removeChild(container);
            if (window.toastr) toastr.success('تم تصدير التقرير الشهري إلى PDF بنجاح');
        }).catch(err => {
            console.error('PDF export error:', err);
            document.body.removeChild(container);
            alert('حدث خطأ أثناء تصدير PDF');
        });
    }

    function exportMonthlyReportExcel() {
        if (typeof XLSX === 'undefined') {
            alert('جاري تحميل مكتبة Excel، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
            return;
        }

        const clientName = '{{ $client->name }}';
        const month      = '{{ $month }}';

        const table = document.querySelector('.table');
        if (!table) {
            alert('لم يتم العثور على جدول للتصدير');
            return;
        }

        const tempTable = table.cloneNode(true);
        tempTable.querySelectorAll('.no-print, .btn, .dropdown').forEach(el => el.remove());

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
        XLSX.utils.book_append_sheet(wb, ws, 'التقرير الشهري');
        XLSX.writeFile(wb, `التقرير_الشهري_${clientName}_${month}.xlsx`);

        if (window.toastr) toastr.success('تم تصدير التقرير الشهري إلى Excel بنجاح');
    }
</script>
@endpush
