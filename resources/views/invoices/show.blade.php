@extends('layouts.master')

@section('title', 'تفاصيل الفاتورة #' . $invoice->number)
@section('page_title', 'تفاصيل الفاتورة')
@section('page_subtitle', 'فاتورة رقم #' . $invoice->number . ' — ' . ($invoice->client->name ?? ''))

@push('styles')
<style>
    /* ── Base ── */
    .inv-section {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e8edf3;
        margin-bottom: 24px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
    }
    .inv-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 24px;
        border-bottom: 1px solid #f0f4f8;
        background: #fafbfc;
    }
    .inv-section-header h6 {
        margin: 0;
        font-weight: 700;
        font-size: .95rem;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .inv-section-header h6 .icon-circle {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: inline-flex; align-items: center; justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .inv-section-body { padding: 20px 24px; }

    /* ── Detail rows ── */
    .dr { display: flex; justify-content: space-between; align-items: center;
          padding: 10px 0; border-bottom: 1px solid #f1f5f9; gap: 12px; }
    .dr:last-child { border-bottom: none; padding-bottom: 0; }
    .dr:first-child { padding-top: 0; }
    .dr-label { font-size: .85rem; color: #64748b; font-weight: 500; flex-shrink: 0; }
    .dr-value { font-size: .875rem; font-weight: 600; color: #1e293b; text-align: end; }

    /* ── Status badges ── */
    .sb { display: inline-flex; align-items: center; gap: 5px;
          padding: 5px 12px; border-radius: 20px; font-size: .78rem; font-weight: 700; }
    .sb-paid       { background: #dcfce7; color: #15803d; }
    .sb-pending    { background: #fef9c3; color: #854d0e; }
    .sb-late,
    .sb-overdue    { background: #fee2e2; color: #b91c1c; }
    .sb-cancelled  { background: #f1f5f9; color: #475569; }
    .sb-approved   { background: #dbeafe; color: #1d4ed8; }
    .sb-rejected   { background: #fce7f3; color: #9d174d; }

    /* ── Stat mini-cards ── */
    .stat-mini { background: #f8fafc; border-radius: 12px; padding: 14px 16px;
                 border: 1px solid #e8edf3; text-align: center; }
    .stat-mini .stat-number { font-size: 1.5rem; font-weight: 800; line-height: 1; margin-bottom: 4px; }
    .stat-mini .stat-label  { font-size: .75rem; color: #64748b; font-weight: 500; }

    /* ── Workforce table ── */
    .wf-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .wf-table thead th {
        background: #f1f5f9; padding: 10px 14px; font-size: .8rem;
        font-weight: 700; color: #475569; text-align: center;
        border-bottom: 2px solid #e2e8f0;
    }
    .wf-table tbody td {
        padding: 11px 14px; font-size: .875rem; color: #1e293b;
        text-align: center; border-bottom: 1px solid #f1f5f9;
    }
    .wf-table tbody tr:last-child td { border-bottom: none; }
    .wf-table tbody tr:hover td { background: #f8fafc; }
    .wf-table tfoot td {
        padding: 12px 14px; font-weight: 800; font-size: .9rem;
        background: #f8fafc; border-top: 2px solid #e2e8f0; text-align: center;
    }
    .wf-role-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 600;
    }

    /* ── Financial bar ── */
    .fin-bar { height: 8px; border-radius: 4px; background: #e2e8f0; overflow: hidden; margin-top: 6px; }
    .fin-bar-fill { height: 100%; border-radius: 4px; transition: width .6s; }

    /* ── Payment items ── */
    .pay-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;
                padding: 14px 16px; margin-bottom: 10px; }
    .pay-item:last-child { margin-bottom: 0; }

    /* ── Salary employees summary ── */
    .sal-stat-card { border-radius: 12px; padding: 16px; text-align: center; }

    /* ── Action buttons ── */
    .action-btn { display: flex; align-items: center; gap: 10px; padding: 12px 16px;
                  border-radius: 12px; border: 1px solid #e2e8f0; background: #fff;
                  font-size: .875rem; font-weight: 600; color: #1e293b; cursor: pointer;
                  transition: all .15s; text-decoration: none; width: 100%; }
    .action-btn:hover { background: #f8fafc; border-color: #cbd5e1; color: #1e293b; }
    .action-btn .action-icon { width: 36px; height: 36px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .action-btn.danger:hover { background: #fff1f2; border-color: #fca5a5; color: #b91c1c; }
</style>
@endpush

@section('page_actions')
<div class="d-flex gap-2 flex-wrap">
    @if($invoice->isSalaryInvoice())
        <a href="{{ route('salary-invoices.employees.index', $invoice->id) }}"
           class="btn btn-primary fw-semibold px-4">
            <i class="bi bi-people me-2"></i>الموظفون والدفعات
        </a>
    @endif
    <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-warning fw-semibold px-4">
        <i class="bi bi-pencil me-2"></i>تعديل
    </a>
    <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary fw-semibold px-4">
        <i class="bi bi-arrow-right me-2"></i>رجوع
    </a>
</div>
@endsection

@section('content')
@php
    /* ── Payment status helpers ── */
    $psMap = [
        'paid'      => ['class'=>'sb-paid',      'label'=>'مدفوعة',    'icon'=>'check-circle-fill'],
        'pending'   => ['class'=>'sb-pending',    'label'=>'معلقة',     'icon'=>'hourglass-split'],
        'late'      => ['class'=>'sb-late',       'label'=>'متأخرة',    'icon'=>'exclamation-circle-fill'],
        'overdue'   => ['class'=>'sb-overdue',    'label'=>'متأخرة',    'icon'=>'exclamation-circle-fill'],
        'cancelled' => ['class'=>'sb-cancelled',  'label'=>'ملغاة',     'icon'=>'x-circle-fill'],
    ];
    $ps = $psMap[$invoice->payment_status] ?? $psMap['pending'];

    $apMap = [
        'approved' => ['class'=>'sb-approved', 'label'=>'معتمدة', 'icon'=>'patch-check-fill'],
        'pending'  => ['class'=>'sb-pending',  'label'=>'بانتظار الاعتماد', 'icon'=>'hourglass-split'],
        'rejected' => ['class'=>'sb-rejected', 'label'=>'مرفوضة', 'icon'=>'x-octagon-fill'],
    ];
    $ap = $apMap[$invoice->approval_status ?? 'pending'] ?? $apMap['pending'];

    /* ── Workforce breakdown ── */
    $wfRows = [];
    if ($invoice->total_workers > 0)
        $wfRows[] = ['role'=>'عمال',    'icon'=>'person-fill',         'color'=>'#3b82f6','bg'=>'#eff6ff',
                     'count'=>(int)$invoice->total_workers,
                     'days'=>(int)($invoice->workers_days ?? $invoice->work_days ?? 0)];
    if ($invoice->total_supervisors > 0)
        $wfRows[] = ['role'=>'مشرفون',  'icon'=>'person-badge-fill',   'color'=>'#8b5cf6','bg'=>'#f5f3ff',
                     'count'=>(int)$invoice->total_supervisors,
                     'days'=>(int)($invoice->supervisors_days ?? $invoice->work_days ?? 0)];
    if ($invoice->total_managers > 0)
        $wfRows[] = ['role'=>'مدراء',   'icon'=>'person-workspace',    'color'=>'#f59e0b','bg'=>'#fffbeb',
                     'count'=>(int)$invoice->total_managers,
                     'days'=>(int)($invoice->managers_days ?? $invoice->work_days ?? 0)];
    if ($invoice->total_users > 0)
        $wfRows[] = ['role'=>'مستخدمون','icon'=>'people-fill',         'color'=>'#10b981','bg'=>'#f0fdf4',
                     'count'=>(int)$invoice->total_users,
                     'days'=>(int)($invoice->users_days ?? $invoice->work_days ?? 0)];

    $totalWfCount   = array_sum(array_column($wfRows, 'count'));
    $totalManDays   = array_sum(array_map(fn($r) => $r['count'] * $r['days'], $wfRows));

    /* ── Salary employees summary ── */
    $salEmployees   = $invoice->invoiceEmployees ?? collect();
    $salTotal       = $salEmployees->count();
    $salPaid        = $salEmployees->where('payment_status','paid')->count();
    $salPartial     = $salEmployees->where('payment_status','partially_paid')->count();
    $salUnpaid      = $salEmployees->where('payment_status','unpaid')->count();
    $salTotalAmt    = $salEmployees->sum('total_salary');
    $salPaidAmt     = $salEmployees->sum('total_paid');
    $salRemainingAmt= $salEmployees->sum('remaining_amount');

    /* ── Financial ── */
    $paidPct = $invoice->total_price > 0
        ? min(100, round(($invoice->paid_amount / $invoice->total_price) * 100))
        : 0;
@endphp

<div class="row g-4">

    {{-- ══════════════════ LEFT / MAIN COLUMN ══════════════════ --}}
    <div class="col-xl-8 col-lg-7">

        {{-- ── 1. Invoice Info ── --}}
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#eff6ff;color:#3b82f6;">
                        <i class="bi bi-file-text-fill"></i>
                    </span>
                    معلومات الفاتورة
                </h6>
                <div class="d-flex gap-2 align-items-center">
                    <span class="sb {{ $ps['class'] }}">
                        <i class="bi bi-{{ $ps['icon'] }}"></i>{{ $ps['label'] }}
                    </span>
                    <span class="sb {{ $ap['class'] }}">
                        <i class="bi bi-{{ $ap['icon'] }}"></i>{{ $ap['label'] }}
                    </span>
                </div>
            </div>
            <div class="inv-section-body">
                <div class="row g-0">
                    <div class="col-md-6">
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-hash me-1"></i>رقم الفاتورة</span>
                            <span class="dr-value" style="color:#10a37f;font-size:1rem;letter-spacing:.5px;">{{ $invoice->number }}</span>
                        </div>
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-tag me-1"></i>نوع الفاتورة</span>
                            <span class="dr-value">
                                @if($invoice->isSalaryInvoice())
                                    <span class="sb sb-approved"><i class="bi bi-people-fill"></i>فاتورة رواتب</span>
                                @else
                                    <span class="sb sb-pending"><i class="bi bi-receipt"></i>فاتورة عادية</span>
                                @endif
                            </span>
                        </div>
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-briefcase me-1"></i>الخدمة</span>
                            <span class="dr-value">{{ $invoice->service->name ?? '—' }}</span>
                        </div>
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-calendar3 me-1"></i>تاريخ الإصدار</span>
                            <span class="dr-value">{{ $invoice->generation_date->format('Y-m-d') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-right: 24px; border-right: 1px solid #f1f5f9;">
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-calendar-check me-1"></i>تاريخ الاستحقاق</span>
                            <span class="dr-value @if($invoice->is_overdue) text-danger @endif">
                                {{ $invoice->last_generation_date ? $invoice->last_generation_date->format('Y-m-d') : '—' }}
                                @if($invoice->is_overdue)
                                    <span class="sb sb-overdue ms-1" style="font-size:.7rem;">متأخرة</span>
                                @endif
                            </span>
                        </div>
                        @if($invoice->due_date)
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-calendar-x me-1"></i>آخر موعد للدفع</span>
                            <span class="dr-value">{{ $invoice->due_date->format('Y-m-d') }}</span>
                        </div>
                        @endif
                        @if($invoice->payment_date)
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-check2-circle me-1"></i>تاريخ الدفع</span>
                            <span class="dr-value text-success">{{ $invoice->payment_date->format('Y-m-d') }}</span>
                        </div>
                        @endif
                        @if($invoice->approved_at)
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-patch-check me-1"></i>تاريخ الاعتماد</span>
                            <span class="dr-value">{{ $invoice->approved_at->format('Y-m-d H:i') }}</span>
                        </div>
                        @endif
                        @if($invoice->approvedBy)
                        <div class="dr">
                            <span class="dr-label"><i class="bi bi-person-check me-1"></i>اعتمد بواسطة</span>
                            <span class="dr-value">{{ $invoice->approvedBy->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($invoice->isSalaryInvoice() && $invoice->approved_at)
                <div class="alert alert-info border-0 mt-3 mb-0 d-flex align-items-center gap-3 py-2 px-3" style="border-radius:10px;font-size:.85rem;">
                    <i class="bi bi-clock-history fs-5"></i>
                    <div><strong>فترة الاستجابة (اعتماد → أول دفعة):</strong>
                        {{ $invoice->response_period_formatted }}</div>
                </div>
                @endif

                @if($invoice->approval_notes)
                <div class="alert alert-warning border-0 mt-3 mb-0 py-2 px-3" style="border-radius:10px;font-size:.85rem;">
                    <strong><i class="bi bi-info-circle me-1"></i>ملاحظات الاعتماد:</strong> {{ $invoice->approval_notes }}
                </div>
                @endif

                @if($invoice->is_cancelled)
                <div class="alert alert-danger border-0 mt-3 mb-0 py-2 px-3" style="border-radius:10px;font-size:.85rem;">
                    <strong><i class="bi bi-x-circle me-1"></i>سبب الإلغاء:</strong> {{ $invoice->cancellation_reason ?? '—' }}
                    @if($invoice->cancelled_at)
                        <span class="ms-2 text-muted">({{ $invoice->cancelled_at->format('Y-m-d') }})</span>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- ── 2. Workforce Details ── --}}
        @if(!$invoice->isSalaryInvoice() && $totalWfCount > 0)
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#f5f3ff;color:#8b5cf6;">
                        <i class="bi bi-people-fill"></i>
                    </span>
                    تفاصيل العمالة
                </h6>
                <div class="d-flex gap-3 align-items-center">
                    <div class="text-center">
                        <div class="fw-bold" style="font-size:1.1rem;color:#8b5cf6;">{{ $totalWfCount }}</div>
                        <div style="font-size:.72rem;color:#94a3b8;">إجمالي العمالة</div>
                    </div>
                    <div class="text-center">
                        <div class="fw-bold" style="font-size:1.1rem;color:#f59e0b;">{{ $totalManDays }}</div>
                        <div style="font-size:.72rem;color:#94a3b8;">يوم عمل</div>
                    </div>
                </div>
            </div>
            <div class="inv-section-body p-0">
                <div class="table-responsive">
                    <table class="wf-table">
                        <thead>
                            <tr>
                                <th style="text-align:right;">الدور الوظيفي</th>
                                <th>العدد</th>
                                <th>أيام العمل</th>
                                <th>إجمالي أيام العمل</th>
                                <th>المبلغ اليومي</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wfRows as $row)
                            @php
                                $manDays    = $row['count'] * $row['days'];
                                $rowTotal   = $manDays * ($invoice->daily_rate ?? 0);
                            @endphp
                            <tr>
                                <td style="text-align:right;">
                                    <span class="wf-role-badge"
                                          style="background:{{ $row['bg'] }};color:{{ $row['color'] }};">
                                        <i class="bi bi-{{ $row['icon'] }}"></i>
                                        {{ $row['role'] }}
                                    </span>
                                </td>
                                <td><strong>{{ $row['count'] }}</strong></td>
                                <td>{{ $row['days'] }} يوم</td>
                                <td><strong>{{ $manDays }}</strong> يوم</td>
                                <td>{{ number_format($invoice->daily_rate ?? 0, 0) }} ر.س</td>
                                <td><strong style="color:#10a37f;">{{ number_format($rowTotal, 0) }} ر.س</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td style="text-align:right; font-size:.85rem; color:#475569;">الإجمالي</td>
                                <td style="color:#8b5cf6;">{{ $totalWfCount }}</td>
                                <td>—</td>
                                <td style="color:#f59e0b;">{{ $totalManDays }} يوم</td>
                                <td>—</td>
                                <td style="color:#10a37f;">{{ number_format($invoice->base_price, 0) }} ر.س</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endif

        {{-- ── 3. Salary Invoice Employees Summary ── --}}
        @if($invoice->isSalaryInvoice())
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#f0fdf4;color:#16a34a;">
                        <i class="bi bi-people-fill"></i>
                    </span>
                    ملخص موظفي الرواتب
                </h6>
                <a href="{{ route('salary-invoices.employees.index', $invoice->id) }}"
                   class="btn btn-sm btn-primary fw-semibold">
                    <i class="bi bi-arrow-left me-1"></i>عرض جميع الموظفين
                </a>
            </div>
            <div class="inv-section-body">
                @if($salTotal > 0)
                {{-- Stat mini cards --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-mini">
                            <div class="stat-number" style="color:#3b82f6;">{{ $salTotal }}</div>
                            <div class="stat-label">إجمالي الموظفين</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-mini">
                            <div class="stat-number" style="color:#16a34a;">{{ $salPaid }}</div>
                            <div class="stat-label">مدفوع الراتب</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-mini">
                            <div class="stat-number" style="color:#f59e0b;">{{ $salPartial }}</div>
                            <div class="stat-label">مدفوع جزئياً</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-mini">
                            <div class="stat-number" style="color:#ef4444;">{{ $salUnpaid }}</div>
                            <div class="stat-label">لم يُصرف</div>
                        </div>
                    </div>
                </div>

                {{-- Salary amounts --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                            <div style="font-size:.78rem;color:#64748b;margin-bottom:4px;">إجمالي الرواتب</div>
                            <div style="font-size:1.1rem;font-weight:800;color:#15803d;">
                                {{ number_format($salTotalAmt, 0) }} <small style="font-size:.7rem;">ر.س</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:#eff6ff;border:1px solid #bfdbfe;">
                            <div style="font-size:.78rem;color:#64748b;margin-bottom:4px;">إجمالي المدفوع</div>
                            <div style="font-size:1.1rem;font-weight:800;color:#1d4ed8;">
                                {{ number_format($salPaidAmt, 0) }} <small style="font-size:.7rem;">ر.س</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-3" style="background:{{ $salRemainingAmt > 0 ? '#fff1f2' : '#f0fdf4' }};border:1px solid {{ $salRemainingAmt > 0 ? '#fecaca' : '#bbf7d0' }};">
                            <div style="font-size:.78rem;color:#64748b;margin-bottom:4px;">المتبقي</div>
                            <div style="font-size:1.1rem;font-weight:800;color:{{ $salRemainingAmt > 0 ? '#b91c1c' : '#15803d' }};">
                                {{ number_format($salRemainingAmt, 0) }} <small style="font-size:.7rem;">ر.س</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Progress bar --}}
                @php
                    $salPct = $salTotalAmt > 0 ? min(100, round(($salPaidAmt / $salTotalAmt) * 100)) : 0;
                @endphp
                <div>
                    <div class="d-flex justify-content-between mb-1" style="font-size:.8rem;color:#64748b;">
                        <span>نسبة الصرف</span>
                        <strong style="color:#15803d;">{{ $salPct }}%</strong>
                    </div>
                    <div class="fin-bar">
                        <div class="fin-bar-fill" style="width:{{ $salPct }}%;background:linear-gradient(90deg,#16a34a,#4ade80);"></div>
                    </div>
                </div>

                {{-- salary_pay_status breakdown --}}
                @php
                    $spsFull    = $salEmployees->where('salary_pay_status','full_paid')->count();
                    $spsPartial = $salEmployees->where('salary_pay_status','partial_paid')->count();
                    $spsPended  = $salEmployees->where('salary_pay_status','pended')->count();
                    $spsNone    = $salTotal - $spsFull - $spsPartial - $spsPended;
                @endphp
                @if($spsFull + $spsPartial + $spsPended > 0)
                <div class="mt-4 p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div style="font-size:.8rem;font-weight:700;color:#475569;margin-bottom:10px;">
                        <i class="bi bi-tag me-1"></i>توزيع حالة الصرف
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @if($spsFull)
                        <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'salary_status'=>'full']) }}"
                           class="sb sb-paid text-decoration-none">
                            <i class="bi bi-patch-check-fill"></i>مدفوع بالكامل ({{ $spsFull }})
                        </a>
                        @endif
                        @if($spsPartial)
                        <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'salary_status'=>'partial']) }}"
                           class="sb sb-pending text-decoration-none">
                            <i class="bi bi-dash-circle-fill"></i>جزئي ({{ $spsPartial }})
                        </a>
                        @endif
                        @if($spsPended)
                        <a href="{{ route('salary-invoices.employees.index', ['invoice'=>$invoice->id,'salary_status'=>'pended']) }}"
                           class="sb sb-cancelled text-decoration-none">
                            <i class="bi bi-pause-circle-fill"></i>معلق ({{ $spsPended }})
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @else
                <div class="text-center py-4">
                    <i class="bi bi-people display-5 text-muted mb-2"></i>
                    <p class="text-muted mb-3">لم يتم استيراد موظفين لهذه الفاتورة بعد</p>
                    <button onclick="openSalaryImportModal({{ $invoice->id }})"
                            class="btn btn-primary">
                        <i class="bi bi-upload me-2"></i>استيراد موظفي الرواتب
                    </button>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── 4. Payments History ── --}}
        @if($invoice->payments->count() > 0)
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#f0fdf4;color:#16a34a;">
                        <i class="bi bi-cash-stack"></i>
                    </span>
                    سجل المدفوعات
                    <span class="badge bg-success ms-1" style="font-size:.72rem;">{{ $invoice->payments->count() }}</span>
                </h6>
                <span class="fw-bold" style="color:#16a34a;font-size:.9rem;">
                    {{ number_format($invoice->payments->sum('amount'), 0) }} ر.س
                </span>
            </div>
            <div class="inv-section-body">
                @foreach($invoice->payments->sortByDesc('payment_date') as $payment)
                <div class="pay-item">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-bold" style="color:#10a37f;font-size:.9rem;">{{ $payment->number }}</div>
                            <div class="text-muted" style="font-size:.8rem; margin-top:2px;">
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '—' }}
                                @if($payment->payment_method)
                                    <span class="mx-2">·</span>
                                    <i class="bi bi-credit-card me-1"></i>{{ $payment->payment_method }}
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold" style="font-size:1rem;">{{ number_format($payment->amount, 0) }} ر.س</div>
                            <span class="sb {{ $payment->status === 'completed' ? 'sb-paid' : ($payment->status === 'pending' ? 'sb-pending' : 'sb-cancelled') }}"
                                  style="font-size:.72rem; margin-top:4px; display:inline-flex;">
                                {{ $payment->status === 'completed' ? 'مكتمل' : ($payment->status === 'pending' ? 'معلق' : 'ملغي') }}
                            </span>
                        </div>
                    </div>
                    @if($payment->notes)
                    <div class="text-muted mt-2" style="font-size:.8rem;">
                        <i class="bi bi-chat-left-text me-1"></i>{{ $payment->notes }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── 5. Notes ── --}}
        @if($invoice->notes)
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#fefce8;color:#ca8a04;">
                        <i class="bi bi-sticky-note-fill"></i>
                    </span>
                    ملاحظات
                </h6>
            </div>
            <div class="inv-section-body">
                <p class="mb-0" style="line-height:1.7;color:#374151;">{{ $invoice->notes }}</p>
            </div>
        </div>
        @endif

    </div>{{-- end main col --}}

    {{-- ══════════════════ RIGHT SIDEBAR ══════════════════ --}}
    <div class="col-xl-4 col-lg-5">

        {{-- ── Financial Summary ── --}}
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#f0fdf4;color:#10a37f;">
                        <i class="bi bi-calculator-fill"></i>
                    </span>
                    الملخص المالي
                </h6>
            </div>
            <div class="inv-section-body">
                {{-- Big total --}}
                <div class="text-center mb-4 p-3 rounded-3" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                    <div style="font-size:.78rem;color:#64748b;margin-bottom:4px;">الإجمالي مع الضريبة</div>
                    <div style="font-size:1.8rem;font-weight:900;color:#15803d;letter-spacing:-.5px;">
                        {{ number_format($invoice->total_price, 2) }}
                        <small style="font-size:.9rem;font-weight:600;">ر.س</small>
                    </div>
                </div>

                {{-- Breakdown rows --}}
                <div class="dr">
                    <span class="dr-label">المبلغ الأساسي</span>
                    <span class="dr-value">{{ number_format($invoice->base_price, 2) }} ر.س</span>
                </div>
                <div class="dr">
                    <span class="dr-label">الضريبة ({{ number_format($invoice->tax_rate, 0) }}%)</span>
                    <span class="dr-value">{{ number_format($invoice->tax_amount, 2) }} ر.س</span>
                </div>
                @if($invoice->amount_difference != 0)
                <div class="dr">
                    <span class="dr-label">
                        فرق المبلغ
                        @if($invoice->difference_type === 'decrease')
                            <span class="sb sb-late" style="font-size:.68rem;padding:2px 6px;">خصم</span>
                        @else
                            <span class="sb sb-paid" style="font-size:.68rem;padding:2px 6px;">إضافة</span>
                        @endif
                    </span>
                    <span class="dr-value {{ $invoice->difference_type === 'decrease' ? 'text-danger' : 'text-success' }}">
                        {{ $invoice->difference_type === 'decrease' ? '-' : '+' }}{{ number_format(abs($invoice->amount_difference), 2) }} ر.س
                    </span>
                </div>
                @endif
                @if($invoice->total_credit_notes > 0)
                <div class="dr">
                    <span class="dr-label">إجمالي الإشعارات الدائنة</span>
                    <span class="dr-value text-warning">-{{ number_format($invoice->total_credit_notes, 2) }} ر.س</span>
                </div>
                @endif

                {{-- Paid / Remaining --}}
                <div class="mt-3 p-3 rounded-3" style="background:#f8fafc;border:1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between mb-2">
                        <span style="font-size:.82rem;color:#64748b;font-weight:600;">المدفوع</span>
                        <span class="text-success fw-bold">{{ number_format($invoice->paid_amount, 2) }} ر.س</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-size:.82rem;color:#64748b;font-weight:600;">المتبقي</span>
                        <span class="{{ $invoice->remaining_amount > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                            {{ number_format($invoice->remaining_amount, 2) }} ر.س
                        </span>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between mb-1" style="font-size:.78rem;color:#94a3b8;">
                            <span>نسبة السداد</span>
                            <strong style="color:{{ $paidPct >= 100 ? '#15803d' : ($paidPct > 0 ? '#f59e0b' : '#ef4444') }};">{{ $paidPct }}%</strong>
                        </div>
                        <div class="fin-bar">
                            <div class="fin-bar-fill"
                                 style="width:{{ $paidPct }}%;background:{{ $paidPct >= 100 ? '#16a34a' : ($paidPct > 0 ? '#f59e0b' : '#ef4444') }};"></div>
                        </div>
                    </div>
                </div>

                {{-- Delay indicators --}}
                @if($invoice->issue_delay_days > 0 || $invoice->payment_delay_days > 0)
                <div class="mt-3">
                    @if($invoice->issue_delay_days > 0)
                    <div class="d-flex align-items-center gap-2 mb-2 p-2 rounded-3" style="background:#fff7ed;font-size:.8rem;">
                        <i class="bi bi-clock text-warning"></i>
                        <span class="text-warning fw-semibold">تأخر إصدار:</span>
                        <span>{{ $invoice->issue_delay_days }} يوم</span>
                    </div>
                    @endif
                    @if($invoice->payment_delay_days > 0)
                    <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#fff1f2;font-size:.8rem;">
                        <i class="bi bi-exclamation-circle text-danger"></i>
                        <span class="text-danger fw-semibold">تأخر دفع:</span>
                        <span>{{ $invoice->payment_delay_days }} يوم</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- ── Client Info ── --}}
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#eff6ff;color:#3b82f6;">
                        <i class="bi bi-building"></i>
                    </span>
                    معلومات العميل
                </h6>
                @can('view_clients')
                <a href="#" class="text-muted" style="font-size:.8rem;">
                    <i class="bi bi-box-arrow-up-left me-1"></i>عرض الملف
                </a>
                @endcan
            </div>
            <div class="inv-section-body">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#3b82f6,#2563eb);
                                display:flex;align-items:center;justify-content:center;color:white;font-size:1.2rem;font-weight:800;flex-shrink:0;">
                        {{ mb_substr($invoice->client->name, 0, 1) }}
                    </div>
                    <div>
                        <div class="fw-bold" style="font-size:.95rem;">{{ $invoice->client->name }}</div>
                        @if($invoice->client->company_name ?? false)
                        <div class="text-muted" style="font-size:.8rem;">{{ $invoice->client->company_name }}</div>
                        @endif
                    </div>
                </div>
                @if($invoice->client->email)
                <div class="dr">
                    <span class="dr-label"><i class="bi bi-envelope me-1"></i>البريد</span>
                    <span class="dr-value" style="font-size:.82rem;">{{ $invoice->client->email }}</span>
                </div>
                @endif
                @if($invoice->client->phone)
                <div class="dr">
                    <span class="dr-label"><i class="bi bi-telephone me-1"></i>الهاتف</span>
                    <span class="dr-value" dir="ltr" style="font-size:.82rem;">{{ $invoice->client->phone }}</span>
                </div>
                @endif
                @if($invoice->client->address)
                <div class="dr">
                    <span class="dr-label"><i class="bi bi-geo-alt me-1"></i>العنوان</span>
                    <span class="dr-value" style="font-size:.82rem;">{{ $invoice->client->address }}</span>
                </div>
                @endif
                @if(isset($invoice->client->grace_period_days))
                <div class="dr">
                    <span class="dr-label"><i class="bi bi-calendar-week me-1"></i>فترة السماح</span>
                    <span class="dr-value">{{ $invoice->client->grace_period_days }} يوم</span>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Quick Actions ── --}}
        <div class="inv-section">
            <div class="inv-section-header">
                <h6>
                    <span class="icon-circle" style="background:#f1f5f9;color:#475569;">
                        <i class="bi bi-lightning-fill"></i>
                    </span>
                    الإجراءات السريعة
                </h6>
            </div>
            <div class="inv-section-body" style="padding:16px;">
                <div class="d-flex flex-column gap-2">
                    @if($invoice->isSalaryInvoice())
                    <a href="{{ route('salary-invoices.employees.index', $invoice->id) }}" class="action-btn">
                        <span class="action-icon" style="background:#eff6ff;color:#3b82f6;">
                            <i class="bi bi-people-fill"></i>
                        </span>
                        <span>عرض الموظفين والدفعات</span>
                        <i class="bi bi-chevron-left ms-auto text-muted"></i>
                    </a>
                    @else
                    <button onclick="openSalaryImportModal({{ $invoice->id }})" class="action-btn">
                        <span class="action-icon" style="background:#f5f3ff;color:#7c3aed;">
                            <i class="bi bi-upload"></i>
                        </span>
                        <span>استيراد موظفي الرواتب</span>
                        <i class="bi bi-chevron-left ms-auto text-muted"></i>
                    </button>
                    @endif

                    <button class="action-btn"
                            data-bs-toggle="modal" data-bs-target="#creditNoteModal"
                            onclick="openCreditNoteModal({{ $invoice->id }}, '{{ $invoice->number }}', {{ $invoice->total_price }}, {{ $invoice->base_price }}, {{ $invoice->tax_rate }}, {{ $invoice->employees_count ?? 0 }}, {{ $invoice->work_days_count ?? 0 }})">
                        <span class="action-icon" style="background:#fefce8;color:#ca8a04;">
                            <i class="bi bi-file-earmark-plus"></i>
                        </span>
                        <span>إضافة إشعار دائن</span>
                        <i class="bi bi-chevron-left ms-auto text-muted"></i>
                    </button>

                    <a href="{{ route('payments.create') }}?invoice_id={{ $invoice->id }}" class="action-btn">
                        <span class="action-icon" style="background:#f0fdf4;color:#16a34a;">
                            <i class="bi bi-cash-stack"></i>
                        </span>
                        <span>تسجيل دفعة جديدة</span>
                        <i class="bi bi-chevron-left ms-auto text-muted"></i>
                    </a>

                    <a href="{{ route('invoices.edit', $invoice) }}" class="action-btn">
                        <span class="action-icon" style="background:#fffbeb;color:#d97706;">
                            <i class="bi bi-pencil-fill"></i>
                        </span>
                        <span>تعديل بيانات الفاتورة</span>
                        <i class="bi bi-chevron-left ms-auto text-muted"></i>
                    </a>

                    @if(!$invoice->is_cancelled)
                    <button class="action-btn danger" onclick="confirmDelete({{ $invoice->id }})">
                        <span class="action-icon" style="background:#fff1f2;color:#ef4444;">
                            <i class="bi bi-trash-fill"></i>
                        </span>
                        <span>حذف الفاتورة</span>
                        <i class="bi bi-chevron-left ms-auto text-muted"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>

    </div>{{-- end sidebar --}}
</div>{{-- end row --}}

{{-- ── Credit Notes History ── --}}
<div class="row mt-2">
    <div class="col-12">
        @include('partials.credit-notes-history', ['invoice' => $invoice])
    </div>
</div>

{{-- Modals --}}
@include('partials.credit-note-modal')
@include('partials.salary-invoice-import-modal')
@endsection

@push('scripts')
<script>
function confirmDelete(invoiceId) {
    if (confirm('هل أنت متأكد من حذف هذه الفاتورة؟ لا يمكن التراجع عن هذا الإجراء.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/invoices/${invoiceId}`;
        const csrf = document.createElement('input');
        csrf.type = 'hidden'; csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        const method = document.createElement('input');
        method.type = 'hidden'; method.name = '_method'; method.value = 'DELETE';
        form.appendChild(csrf);
        form.appendChild(method);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
