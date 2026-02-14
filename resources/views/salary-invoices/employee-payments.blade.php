@extends('layouts.master')

@section('title', 'سجل دفعات الموظف - ' . $employee->employee_name)
@section('page_title', 'سجل دفعات الموظف')
@section('page_subtitle', $employee->employee_name)

@section('page_actions')
    <div class="d-flex gap-2">
        <a href="{{ route('salary-invoices.employees.index', $invoice) }}" class="btn btn-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-arrow-right me-2"></i>رجوع لقائمة الموظفين
        </a>
        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-outline-secondary rounded-xl px-4 py-2 fw-bold">
            <i class="bi bi-file-text me-2"></i>عرض الفاتورة
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-gradient-primary text-white p-4">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-person-fill me-2"></i>
                    معلومات الموظف
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">اسم الموظف</small>
                            <strong class="fs-5">{{ $employee->employee_name }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">المشروع</small>
                            <strong>{{ $employee->project ?? '-' }}</strong>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">رقم الحساب (IBAN)</small>
                            <strong dir="ltr">{{ $employee->iban ?? '-' }}</strong>
                        </div>
                    </div>
                    @if($employee->account_holder_name)
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">اسم صاحب الحساب</small>
                            <strong>{{ $employee->account_holder_name }}</strong>
                        </div>
                    </div>
                    @endif
                    @if($employee->bank_name)
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">اسم البنك</small>
                            <strong>{{ $employee->bank_name }}</strong>
                        </div>
                    </div>
                    @endif
                    <div class="col-md-4">
                        <div class="mb-3">
                            <small class="text-muted d-block">نوع الراتب</small>
                            @if($employee->payment_method === 'wps' || $employee->salary_type === 'wps')
                                <span class="badge bg-purple-600 text-white">نظام حماية الأجور (WPS)</span>
                            @else
                                <span class="badge bg-blue-600 text-white">راتب شهري عادي</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light p-4">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-cash-stack me-2"></i>
                            سجل الدفعات
                        </h5>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-success" onclick="exportToExcel()">
                                <i class="bi bi-file-earmark-excel me-1"></i>تصدير Excel
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="exportToPDF()">
                                <i class="bi bi-file-earmark-pdf me-1"></i>تصدير PDF
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchPayments" placeholder="البحث في الدفعات...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterPaymentType">
                            <option value="">جميع الأنواع</option>
                            <option value="full">دفع كامل</option>
                            <option value="partial">دفع جزئي</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filterPaymentMode">
                            <option value="">جميع الطرق</option>
                            <option value="monthly">شهري</option>
                            <option value="wps">WPS</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($employee->payments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">تاريخ الدفع</th>
                                    <th class="px-4 py-3">نوع الدفع</th>
                                    <th class="px-4 py-3">مبلغ الدفع</th>
                                    <th class="px-4 py-3">طريقة الدفع</th>
                                    <th class="px-4 py-3">تم بواسطة</th>
                                    <th class="px-4 py-3">ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employee->payments as $payment)
                                <tr>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                                        {{ $payment->payment_date->format('Y-m-d') }}
                                        <br>
                                        <small class="text-muted">{{ $payment->payment_date->format('h:i A') }}</small>
                                    </td>
                                    <td class="px-4 py-3" data-payment-type="{{ $payment->payment_type }}">
                                        @if($payment->payment_type === 'full')
                                            <span class="badge bg-success">دفع كامل</span>
                                        @else
                                            <span class="badge bg-warning text-dark">دفع جزئي</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <strong class="text-success fs-5">{{ number_format($payment->payment_amount, 0) }} ر.س</strong>
                                    </td>
                                    <td class="px-4 py-3" data-payment-mode="{{ $payment->payment_mode }}">
                                        @if($payment->payment_mode === 'wps')
                                            <span class="badge bg-purple-600 text-white">
                                                <i class="bi bi-credit-card me-1"></i>WPS
                                            </span>
                                        @else
                                            <span class="badge bg-blue-600 text-white">
                                                <i class="bi bi-cash me-1"></i>شهري
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <i class="bi bi-person-fill me-1 text-muted"></i>
                                        {{ $payment->createdBy->name ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if($payment->notes)
                                            <small class="text-muted">{{ $payment->notes }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-end"><strong>إجمالي المدفوع:</strong></td>
                                    <td class="px-4 py-3">
                                        <strong class="text-success fs-5">{{ number_format($employee->payments->sum('payment_amount'), 0) }} ر.س</strong>
                                    </td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-cash-stack display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد دفعات مسجلة</h5>
                        <p class="text-muted">لم يتم تسجيل أي دفعات لهذا الموظف بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light p-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-calculator me-2"></i>
                    الملخص المالي
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">إجمالي الراتب</small>
                    <strong class="fs-4 text-primary">{{ number_format($employee->total_salary ?? $employee->net_salary, 0) }} ر.س</strong>
                </div>
                <div class="mb-3 pb-3 border-bottom">
                    <small class="text-muted d-block mb-1">المبلغ المدفوع</small>
                    <strong class="fs-4 text-success">{{ number_format($employee->total_paid ?? 0, 0) }} ر.س</strong>
                    <div class="progress mt-2" style="height: 8px;">
                        @php
                            $totalSalary = $employee->total_salary ?? $employee->net_salary;
                            $percentage = $totalSalary > 0 ? ($employee->total_paid / $totalSalary) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ min($percentage, 100) }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($percentage, 1) }}% مدفوع</small>
                </div>
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">المبلغ المتبقي</small>
                    <strong class="fs-4 {{ $employee->remaining_amount > 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($employee->remaining_amount ?? ($employee->total_salary - $employee->total_paid), 0) }} ر.س
                    </strong>
                </div>
                <div class="alert alert-{{ $employee->payment_status === 'paid' ? 'success' : ($employee->payment_status === 'partially_paid' ? 'warning' : 'danger') }} border-0 mb-0">
                    <strong>حالة الدفع:</strong>
                    @if($employee->payment_status === 'paid')
                        <span class="badge bg-success">مدفوع بالكامل</span>
                    @elseif($employee->payment_status === 'partially_paid')
                        <span class="badge bg-warning text-dark">مدفوع جزئياً</span>
                    @else
                        <span class="badge bg-danger">غير مدفوع</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light p-3">
                <h6 class="mb-0 fw-bold">
                    <i class="bi bi-file-text me-2"></i>
                    معلومات الفاتورة
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="mb-2">
                    <small class="text-muted d-block">رقم الفاتورة</small>
                    <strong>{{ $invoice->number }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">العميل</small>
                    <strong>{{ $invoice->client->name }}</strong>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">تاريخ الإصدار</small>
                    <strong>{{ $invoice->generation_date->format('Y-m-d') }}</strong>
                </div>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-primary">
                        <i class="bi bi-eye me-2"></i>عرض الفاتورة
                    </a>
                    <a href="{{ route('salary-invoices.employees.index', $invoice) }}" class="btn btn-outline-primary">
                        <i class="bi bi-people me-2"></i>قائمة الموظفين
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .bg-purple-600 {
        background-color: #7c3aed !important;
    }
    .bg-blue-600 {
        background-color: #2563eb !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchPayments');
    const filterType = document.getElementById('filterPaymentType');
    const filterMode = document.getElementById('filterPaymentMode');
    const tableRows = document.querySelectorAll('tbody tr');
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const typeFilter = filterType.value;
        const modeFilter = filterMode.value;
        
        let visibleCount = 0;
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const paymentType = row.querySelector('[data-payment-type]')?.dataset.paymentType || '';
            const paymentMode = row.querySelector('[data-payment-mode]')?.dataset.paymentMode || '';
            
            const matchesSearch = text.includes(searchTerm);
            const matchesType = !typeFilter || paymentType === typeFilter;
            const matchesMode = !modeFilter || paymentMode === modeFilter;
            
            if (matchesSearch && matchesType && matchesMode) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update total if needed
        updateVisibleTotal();
    }
    
    function updateVisibleTotal() {
        const visibleRows = Array.from(tableRows).filter(row => row.style.display !== 'none');
        const total = visibleRows.reduce((sum, row) => {
            const amountText = row.cells[2]?.textContent.replace(/[^\d.]/g, '') || '0';
            return sum + parseFloat(amountText);
        }, 0);
        
        const totalCell = document.querySelector('tfoot td:nth-child(2) strong');
        if (totalCell && visibleRows.length < tableRows.length) {
            totalCell.textContent = `${number_format(total, 0)} ر.س (${visibleRows.length} من ${tableRows.length})`;
        }
    }
    
    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (filterType) filterType.addEventListener('change', filterTable);
    if (filterMode) filterMode.addEventListener('change', filterTable);
});

function exportToExcel() {
    const table = document.querySelector('.table-responsive table');
    const wb = XLSX.utils.table_to_book(table, {sheet: "سجل الدفعات"});
    XLSX.writeFile(wb, 'سجل_دفعات_{{ $employee->employee_name }}_{{ date("Y-m-d") }}.xlsx');
}

function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('p', 'mm', 'a4');
    
    // Add Arabic font support (you may need to add custom font)
    doc.setFont('helvetica');
    doc.setFontSize(16);
    doc.text('Payment History Report', 105, 15, { align: 'center' });
    
    doc.setFontSize(12);
    doc.text('Employee: {{ $employee->employee_name }}', 14, 25);
    doc.text('Invoice: {{ $invoice->number }}', 14, 32);
    doc.text('Date: {{ date("Y-m-d") }}', 14, 39);
    
    const tableData = [];
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cells = row.querySelectorAll('td');
            tableData.push([
                cells[0]?.textContent.trim().split('\n')[0] || '',
                cells[1]?.textContent.trim() || '',
                cells[2]?.textContent.trim() || '',
                cells[3]?.textContent.trim() || '',
                cells[4]?.textContent.trim() || '',
                cells[5]?.textContent.trim() || ''
            ]);
        }
    });
    
    doc.autoTable({
        head: [['Date', 'Type', 'Amount', 'Mode', 'Processed By', 'Notes']],
        body: tableData,
        startY: 45,
        styles: { font: 'helvetica', fontSize: 9 },
        headStyles: { fillColor: [52, 152, 219] }
    });
    
    doc.save('payment_history_{{ $employee->employee_name }}_{{ date("Y-m-d") }}.pdf');
}

function number_format(number, decimals) {
    return number.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>
@endpush
