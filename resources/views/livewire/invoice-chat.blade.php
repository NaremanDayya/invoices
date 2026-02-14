<div class="invoice-chat-container" dir="rtl">
    <!-- رأس الدردشة -->
    <div class="chat-header">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <h5 class="mb-1 text-dark">{{ $client->name }}</h5>
                    <small class="text-muted">
                        <i class="bi bi-file-earmark-text ms-1"></i>
                        @if($invoice)
                            مناقشة الفاتورة رقم #{{ $invoice->number }}
                        @else
                            مناقشة الفاتورة
                        @endif
                    </small>
                </div>
                <div class="avatar-container ms-3">
                    <div class="avatar-placeholder bg-success">
                        {{ mb_substr($client->name, 0, 2) }}
                    </div>
                </div>
                <button wire:click="$dispatch('back-to-invoices')" class="btn btn-sm btn-light ms-3">
                    <i class="bi bi-arrow-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- جسم الدردشة -->
    <div class="chat-body">
        <!-- الشريط الجانبي - تفاصيل الفاتورة -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h6><i class="bi bi-receipt ms-2"></i>تفاصيل الفاتورة</h6>
            </div>

            <!-- ملخص الفاتورة الحالية -->
            @if($invoice)
                <div class="invoice-summary">
                    <div class="summary-item">
                        <span class="label">رقم الفاتورة</span>
                        <span class="value text-success fw-bold">{{ $invoice->number }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">المبلغ</span>
                        <span class="value">{{ number_format($invoice->total_price, 0) }} ر.س</span>
                    </div>

                    <div class="summary-item">
                        <span class="label">الحالة</span>
                        @php
                            $statusColors = [
                                'paid' => 'bg-success-light text-success',
                                'pending' => 'bg-warning-light text-warning',
                                'overdue' => 'bg-danger-light text-danger',
                                'draft' => 'bg-secondary-light text-secondary',
                            ];
                            $statusClass = $statusColors[strtolower($invoice->status)] ?? 'bg-secondary-light text-secondary';

                            $statusLabels = [
                                'paid' => 'مدفوعة',
                                'pending' => 'قيد الانتظار',
                                'overdue' => 'متأخرة',
                                'draft' => 'مسودة',
                            ];
                            $statusText = $statusLabels[strtolower($invoice->status)] ?? ucfirst($invoice->status);
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                    </div>
                </div>
            @endif

            <!-- قائمة فواتير العميل -->
            <div class="sidebar-section mt-4">
                <h6>
                    <i class="bi bi-files ms-2"></i>
                    فواتير العميل
                    <span class="badge bg-success me-1">{{ $clientInvoices->count() }}</span>
                </h6>

                @if($clientInvoices->count() > 0)
                    <div class="invoices-list">
                        @foreach($clientInvoices as $clientInvoice)
                            <a href="{{ route('client.chat.invoice', ['client' => $client->id, 'invoice' => $clientInvoice->id]) }}"
                               class="invoice-item {{ $invoice && $invoice->id == $clientInvoice->id ? 'active' : '' }}"
                               wire:navigate>
                                <div class="invoice-status">
                                    @php
                                        $invoiceStatusColors = [
                                            'paid' => 'text-success',
                                            'pending' => 'text-warning',
                                            'overdue' => 'text-danger',
                                            'draft' => 'text-secondary',
                                        ];
                                        $invoiceStatusClass = $invoiceStatusColors[strtolower($clientInvoice->status)] ?? 'text-secondary';
                                    @endphp
                                    <i class="bi bi-circle-fill {{ $invoiceStatusClass }}"></i>
                                </div>
                                <div class="invoice-item-details">
                                    <div class="invoice-number">{{ $clientInvoice->number }}</div>
                                    <div class="invoice-meta">
                                        <span class="date">{{ $clientInvoice->created_at->translatedFormat('d M') }}</span>
                                        <span class="dot">•</span>
                                        <span class="amount">{{ number_format($clientInvoice->total_price, 0) }} ر.س</span>
                                    </div>
                                </div>
                                <div class="invoice-item-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light border text-center py-3">
                        <i class="bi bi-file-text text-muted mb-2 d-block" style="font-size: 2rem;"></i>
                        <p class="mb-0 text-muted">لا توجد فواتير</p>
                    </div>
                @endif
            </div>

            <!-- النشاط الأخير -->
            @if($invoice)
                <div class="sidebar-section mt-4">
                    <h6><i class="bi bi-clock-history ms-2"></i>النشاط الأخير</h6>
                    <ul class="activity-list">
                        @if($invoice->paid_at)
                            <li>
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>تم تأكيد الدفع - منذ {{ $invoice->paid_at->locale('ar')->diffForHumans() }}</span>
                            </li>
                        @endif
                        @if($invoice->sent_at)
                            <li>
                                <i class="bi bi-envelope-fill text-primary"></i>
                                <span>تم إرسال الفاتورة - منذ {{ $invoice->sent_at->locale('ar')->diffForHumans() }}</span>
                            </li>
                        @endif
                        @if($invoice->created_at)
                            <li>
                                <i class="bi bi-calendar-event text-info"></i>
                                <span>تم الإنشاء - منذ {{ $invoice->created_at->locale('ar')->diffForHumans() }}</span>
                            </li>
                        @endif
                    </ul>
                </div>

                <!-- المرفقات -->
                @if($invoice->attachments && $invoice->attachments->count() > 0)
                    <div class="sidebar-section mt-4">
                        <h6><i class="bi bi-paperclip ms-2"></i>المرفقات</h6>
                        <div class="attachments">
                            @foreach($invoice->attachments->take(3) as $attachment)
                                <a href="{{ Storage::url($attachment->path) }}"
                                   target="_blank"
                                   class="attachment-item">
                                    <span>{{ $attachment->name }}</span>
                                    <i class="bi bi-file-arrow-down"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>

        <!-- منطقة الرسائل -->
        <div class="messages-container" id="messages-container">
            <livewire:chat-box :client_id="$client->id"
                               :invoice_id="$invoice->id ?? null"
                               :selectedConversation="$selectedConversation"
                               :key="$selectedConversation->id ?? 'chat-box'" />
        </div>
    </div>
</div>

@push('styles')
    <style>
        .invoice-chat-container {
            height: calc(100vh - 180px);
            background: #f8f9fa;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
        }

        .avatar-container {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            overflow: hidden;
            border: 2px solid #20c997;
        }

        .company-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            background: linear-gradient(135deg, #20c997, #198754);
        }

        .chat-body {
            display: flex;
            height: calc(100% - 75px);
        }

        .messages-container {
            flex: 1;
            background: #f8f9fa;
            padding: 20px;
            overflow-y: auto;
        }

        .chat-sidebar {
            width: 320px;
            background: white;
            border-right: 1px solid #e9ecef;
            padding: 20px;
            overflow-y: auto;
            border-left: none;
        }

        .sidebar-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
            text-align: right;
        }

        .sidebar-header h6 {
            color: #198754;
            font-weight: 600;
        }

        .invoice-summary {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #dee2e6;
            text-align: right;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .summary-item .label {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .summary-item .value {
            font-weight: 500;
            text-align: left;
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }
        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }
        .bg-danger-light {
            background-color: rgba(220, 53, 69, 0.1) !important;
        }
        .bg-secondary-light {
            background-color: rgba(108, 117, 125, 0.1) !important;
        }

        .sidebar-section h6 {
            color: #495057;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            text-align: right;
            justify-content: flex-end;
        }

        .invoices-list {
            max-height: 300px;
            overflow-y: auto;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .invoice-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            text-decoration: none;
            color: #495057;
            border-bottom: 1px solid #f1f3f4;
            transition: all 0.2s;
            background: white;
            text-align: right;
            flex-direction: row-reverse;
        }

        .invoice-item:hover {
            background: #f8f9fa;
            color: #198754;
        }

        .invoice-item.active {
            background: rgba(25, 135, 84, 0.05);
            border-right: 3px solid #198754;
            border-left: none;
            color: #198754;
        }

        .invoice-item:last-child {
            border-bottom: none;
        }

        .invoice-item-icon {
            width: 36px;
            height: 36px;
            background: #e9ecef;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 12px;
            margin-right: 0;
        }

        .invoice-item-icon i {
            font-size: 1rem;
            color: #6c757d;
        }

        .invoice-item.active .invoice-item-icon {
            background: #198754;
        }

        .invoice-item.active .invoice-item-icon i {
            color: white;
        }

        .invoice-item-details {
            flex: 1;
            text-align: right;
        }

        .invoice-number {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        .invoice-meta {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: #6c757d;
            justify-content: flex-end;
            flex-direction: row-reverse;
        }

        .invoice-meta .amount {
            font-weight: 500;
        }

        .invoice-meta .dot {
            margin: 0 5px;
        }

        .invoice-status i {
            font-size: 0.6rem;
            margin-left: 10px;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: right;
        }

        .activity-list li {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
            justify-content: flex-end;
        }

        .activity-list li i {
            margin-left: 10px;
            margin-right: 0;
            font-size: 0.8rem;
        }

        .activity-list li span {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .attachments {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .attachment-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            text-decoration: none;
            color: #495057;
            transition: all 0.3s;
            border: 1px solid transparent;
            text-align: right;
            flex-direction: row-reverse;
        }

        .attachment-item:hover {
            background: #e9ecef;
            border-color: #20c997;
            transform: translateX(-5px);
        }

        .attachment-item i {
            font-size: 1.2rem;
            margin-left: 10px;
            margin-right: 0;
        }

        .attachment-item span {
            font-size: 0.85rem;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: right;
        }

        /* تخصيص شريط التمرير */
        .invoices-list::-webkit-scrollbar {
            width: 4px;
        }

        .invoices-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .invoices-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 2px;
        }

        /* التجاوب */
        @media (max-width: 992px) {
            .chat-sidebar {
                width: 280px;
            }
        }

        @media (max-width: 768px) {
            .chat-body {
                flex-direction: column;
            }

            .chat-sidebar {
                width: 100%;
                order: 1;
                border-right: none;
                border-bottom: 1px solid #e9ecef;
                margin-top: 20px;
            }

            .invoices-list {
                max-height: 200px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesContainer = document.getElementById('messages-container');

            function scrollToBottom() {
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            }

            // التمرير الأولي
            scrollToBottom();

            // الاستماع للرسائل الجديدة
            window.addEventListener('scroll-bottom', scrollToBottom);

            // التمرير التلقائي للرسائل الجديدة
            Livewire.on('newMessage', scrollToBottom);

            // العودة للفواتير
            window.addEventListener('back-to-invoices', function() {
                window.location.href = '{{ route("invoices.index") }}';
            });
        });
    </script>
@endpush
