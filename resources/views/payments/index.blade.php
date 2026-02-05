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
                <h3>{{ $stats['completed'] ?? 120 }}</h3>
                <p>مكتملة</p>
            </div>
            <div class="stat-icon-box" style="background: #e6fffa; color: #319795;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $stats['pending'] ?? 15 }}</h3>
                <p>قيد الانتظار</p>
            </div>
            <div class="stat-icon-box" style="background: #fffaf0; color: #dd6b20;">
                <i class="bi bi-clock-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3 class="text-danger">{{ $stats['cancelled'] ?? 2 }}</h3>
                <p>ملغاة</p>
            </div>
            <div class="stat-icon-box" style="background: #fff5f5; color: #e53e3e;">
                <i class="bi bi-x-circle-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ number_format($stats['total_amount'] ?? 450000) }}</h3>
                <p>إجمالي المبالغ</p>
            </div>
            <div class="stat-icon-box" style="background: #ebf8ff; color: #3182ce;">
                <i class="bi bi-cash-stack"></i>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-100 p-3 mb-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3 flex-grow-1">
            <div class="search-box ms-0" style="width: 300px; background: #fcfcfc; border: 1px solid #f0f0f0;">
                <i class="bi bi-search text-muted"></i>
                <input type="text" placeholder="بحث برقم الدفع أو العميل..." style="font-size: 0.85rem;">
            </div>
            <select class="form-select border-0 bg-light rounded-xl" style="width: 150px; font-size: 0.85rem;">
                <option selected>جميع الحالات</option>
                <option>مكتملة</option>
                <option>معلقة</option>
            </select>
        </div>
        <div class="d-flex gap-2">
            @include('components.export-dropdown')
        </div>
    </div>

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
                    @php
                        $items = $payments->isEmpty() ? collect([]) : $payments;
                        if($items->isEmpty()){
                            for($i=1; $i<=5; $i++){
                                $items->push((object)[
                                    'id' => $i,
                                    'number' => 'PAY-00'.($i),
                                    'invoice' => (object)[
                                        'number' => 'INV-00'.($i),
                                        'client' => (object)['name' => 'شركة النور للمقاولات']
                                    ],
                                    'payment_date' => now()->subDays($i),
                                    'formatted_amount' => number_format(15000 * $i).' ر.س',
                                    'payment_method' => 'تحويل بنكي',
                                    'status' => 'completed',
                                    'reference_number' => 'REF-987654321'.$i,
                                ]);
                            }
                        }
                    @endphp
                    @foreach($items as $payment)
                    <tr>
                        <td><span class="pay-number">{{ $payment->number }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $payment->invoice->client->name ?? '—' }}</div>
                        </td>
                        <td><span class="text-muted">{{ $payment->invoice->number ?? '—' }}</span></td>
                        <td>
                            <div>{{ is_string($payment->payment_date) ? $payment->payment_date : $payment->payment_date->format('Y-m-d') }}</div>
                            @if(isset($payment->late_days) && $payment->late_days > 0)
                                <small class="badge bg-danger mt-1">
                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>متأخر {{ $payment->late_days }} يوم
                                </small>
                            @endif
                        </td>
                        <td><span class="payment-amount">{{ $payment->formatted_amount }}</span></td>
                        <td>
                            <span class="badge bg-light text-dark border rounded-pill px-3">
                                <i class="bi bi-wallet2 me-1"></i>
                                {{ $payment->payment_method ?? 'تحويل' }}
                            </span>
                        </td>
                        <td>
                            @php
                                $statusMap = [
                                    'completed' => ['class' => 'completed', 'label' => 'مكتملة', 'icon' => 'check-circle-fill'],
                                    'pending' => ['class' => 'pending', 'label' => 'معلقة', 'icon' => 'hourglass-split'],
                                    'failed' => ['class' => 'failed', 'label' => 'فشلت', 'icon' => 'x-circle-fill'],
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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

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
    @include('components.export-scripts')
    <script>
        // Initialize data
        let PaymentsData = @json($payments);

        // Delete confirmation function
        function confirmDeletePayment(paymentId) {
            const form = document.getElementById('deletePaymentForm');
            form.action = `/payments/${paymentId}`;
            const modal = new bootstrap.Modal(document.getElementById('deletePaymentModal'));
            modal.show();
        }

        // Export to PDF Function
        function exportPaymentsToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4'); // Landscape orientation
            
            // Title (using English for proper rendering)
            doc.setFontSize(18);
            doc.text('Payments Report - تقرير الدفعات', doc.internal.pageSize.getWidth() / 2, 15, { align: 'center' });
            
            doc.setFontSize(10);
            const today = new Date();
            const dateStr = today.getFullYear() + '-' + String(today.getMonth() + 1).padStart(2, '0') + '-' + String(today.getDate()).padStart(2, '0');
            doc.text('Report Date: ' + dateStr, doc.internal.pageSize.getWidth() / 2, 22, { align: 'center' });
            
            // Get table data
            const payments = @json($payments->items());
            
            const tableData = payments.map(payment => {
                const methodMap = {
                    'cash': 'Cash',
                    'bank_transfer': 'Bank Transfer',
                    'check': 'Check',
                    'credit_card': 'Credit Card',
                    'other': 'Other'
                };
                
                const statusMap = {
                    'completed': 'Completed',
                    'pending': 'Pending',
                    'cancelled': 'Cancelled'
                };
                
                return [
                    payment.number || '',
                    payment.invoice?.number || '',
                    payment.invoice?.client?.name || '',
                    payment.payment_date || '',
                    parseFloat(payment.amount || 0).toFixed(2) + ' SAR',
                    methodMap[payment.payment_method] || payment.payment_method,
                    statusMap[payment.status] || payment.status
                ];
            });
            
            // Add table
            doc.autoTable({
                head: [['Payment #', 'Invoice #', 'Client', 'Payment Date', 'Amount', 'Method', 'Status']],
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
            doc.save('payments_' + new Date().toISOString().split('T')[0] + '.pdf');
            
            if (window.toastr) toastr.success('Payments exported to PDF successfully');
        }
        
        // Export to Excel Function
        function exportPaymentsToExcel() {
            const payments = @json($payments->items());
            
            const methodMap = {
                'cash': 'نقدي',
                'bank_transfer': 'تحويل بنكي',
                'check': 'شيك',
                'credit_card': 'بطاقة ائتمان',
                'other': 'أخرى'
            };
            
            const statusMap = {
                'completed': 'مكتمل',
                'pending': 'قيد الانتظار',
                'cancelled': 'ملغى'
            };
            
            const excelData = payments.map(payment => ({
                'رقم الدفعة': payment.number || '',
                'رقم الفاتورة': payment.invoice?.number || '',
                'العميل': payment.invoice?.client?.name || '',
                'تاريخ الدفع': payment.payment_date || '',
                'المبلغ': parseFloat(payment.amount || 0).toFixed(2),
                'طريقة الدفع': methodMap[payment.payment_method] || payment.payment_method,
                'الحالة': statusMap[payment.status] || payment.status,
                'رقم المرجع': payment.reference_number || '',
                'اسم البنك': payment.bank_name || '',
                'ملاحظات': payment.notes || ''
            }));
            
            const ws = XLSX.utils.json_to_sheet(excelData);
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'الدفعات');
            
            // Set column widths
            ws['!cols'] = [
                { wch: 15 }, { wch: 15 }, { wch: 25 }, { wch: 15 }, { wch: 15 },
                { wch: 15 }, { wch: 15 }, { wch: 20 }, { wch: 20 }, { wch: 30 }
            ];
            
            XLSX.writeFile(wb, 'payments_' + new Date().toISOString().split('T')[0] + '.xlsx');
            
            if (window.toastr) toastr.success('Payments exported to Excel successfully');
        }
    </script>
@endpush
