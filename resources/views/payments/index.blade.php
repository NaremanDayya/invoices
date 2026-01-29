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
    <a href="{{ route('payments.create') }}" class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i>
        <span>إضافة دفعة</span>
    </a>
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
                        <td>{{ is_string($payment->payment_date) ? $payment->payment_date : $payment->payment_date->format('Y-m-d') }}</td>
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
                                <a href="#" class="btn-action" title="عرض"><i class="bi bi-eye"></i></a>
                                <a href="#" class="btn-action" title="تعديل"><i class="bi bi-pencil"></i></a>
                                <button class="btn-action text-danger" title="حذف"><i class="bi bi-trash"></i></button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


@endsection

@push('scripts')
    @include('components.export-scripts')
    <script>
        // Initialize data
        let PaymentsData = @json($payments);

        document.addEventListener('DOMContentLoaded', function() {
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
        });

        // Export function
        function exportTable(type) {
            if (type === 'xlsx') {
                exportPaymentsToExcel();
            } else if (type === 'pdf') {
                exportPaymentsToPDF();
            }

            // Close dropdown
            document.querySelectorAll('.dropdown').forEach(drop => {
                drop.classList.remove('active');
            });
        }

        // Export to Excel
        function exportPaymentsToExcel() {
            const table = document.getElementById('paymentsTable');
            const headers = [];
            const data = [];

            // Get headers
            const headerCells = table.querySelectorAll('thead th');
            headerCells.forEach(header => {
                if (!header.classList.contains('no-print')) {
                    headers.push(header.textContent.trim());
                }
            });

            // Get data
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const rowData = [];
                const cells = row.querySelectorAll('td');

                cells.forEach((cell, index) => {
                    if (!headerCells[index].classList.contains('no-print')) {
                        let cellContent = cell.textContent.trim();

                        // Handle badge HTML content
                        if (cell.querySelector('.badge')) {
                            const badgeText = cell.querySelector('.badge').textContent.trim();
                            cellContent = badgeText;
                        }

                        rowData.push(cellContent);
                    }
                });

                if (rowData.length > 0) {
                    data.push(rowData);
                }
            });

            // Prepare worksheet data
            const wsData = [headers, ...data];
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

            // Create workbook and save
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, "المدفوعات");

            XLSX.writeFile(workbook, `مدفوعات_${new Date().toISOString().slice(0, 10)}.xlsx`);
        }

        // Export to PDF
        function exportPaymentsToPDF() {
            // Clone the table
            const originalTable = document.getElementById('paymentsTable');
            const table = originalTable.cloneNode(true);
            const pdfHeader = document.querySelector('.pdf-header').cloneNode(true);
            const pdfFooter = document.querySelector('.pdf-footer').cloneNode(true);

            // Show header and footer
            pdfHeader.style.display = 'block';
            pdfFooter.style.display = 'block';

            // Create container for PDF
            const pdfContainer = document.createElement('div');
            pdfContainer.style.padding = '20px';
            pdfContainer.appendChild(pdfHeader);
            pdfContainer.appendChild(table);
            pdfContainer.appendChild(pdfFooter);

            // Hide non-printable columns
            const headers = table.querySelectorAll('thead th');
            headers.forEach((header, index) => {
                if (header.classList.contains('no-print')) {
                    header.style.display = 'none';
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells[index]) {
                            row.cells[index].style.display = 'none';
                        }
                    });
                }
            });

            // PDF options
            const options = {
                margin: 10,
                filename: `تقرير_المدفوعات_${new Date().toISOString().slice(0,10)}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: document.documentElement.offsetWidth
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'landscape',
                    compress: true
                }
            };

            // Generate and save PDF
            html2pdf().set(options).from(pdfContainer).save();
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setupExportDropdown('exportDropdown', 'payments-table-container', 'payments-table', 'تقرير_المدفوعات');
        });
    </script>
@endpush
