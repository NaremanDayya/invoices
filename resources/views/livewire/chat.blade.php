<div class="invoice-chat-container">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="d-flex align-items-center">
                <button wire:click="$dispatch('back-to-invoices')" class="btn btn-sm btn-light me-3">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <div class="avatar-container">

                    <div class="avatar-placeholder bg-success">
                        {{ substr($client->name, 0, 2) }}
                    </div>

                </div>
                <div class="ms-3">
                    <h5 class="mb-1 text-dark">{{ $client->name }}</h5>
                    <small class="text-muted">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        @if($invoice)
                            Invoice #{{ $invoice->invoice_number }} Discussion
                        @else
                            Invoice Discussion
                        @endif
                    </small>
                </div>
            </div>


        </div>
    </div>

    <!-- Chat Body -->
    <div class="chat-body">
        <!-- Messages Area -->
        <div class="messages-container" id="messages-container">
            <livewire:chat-box :client_id="$client->id"
                               :invoice_id="$invoice->id ?? null"
                               :selectedConversation="$selectedConversation"
                               :key="$selectedConversation->id ?? 'chat-box'" />
        </div>

        <!-- Sidebar - Invoice Details -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h6><i class="bi bi-receipt me-2"></i>Invoice Details</h6>
            </div>

            <!-- Current Invoice Summary -->
            @if($invoice)
                <div class="invoice-summary">
                    <div class="summary-item">
                        <span class="label">Invoice #</span>
                        <span class="value text-success fw-bold">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Amount</span>
                        <span class="value">${{ number_format($invoice->total_amount, 0) }}</span>
                    </div>

                    <div class="summary-item">
                        <span class="label">Status</span>
                        @php
                            $statusColors = [
                                'paid' => 'bg-success-light text-success',
                                'pending' => 'bg-warning-light text-warning',
                                'overdue' => 'bg-danger-light text-danger',
                                'draft' => 'bg-secondary-light text-secondary',
                            ];
                            $statusClass = $statusColors[strtolower($invoice->status)] ?? 'bg-secondary-light text-secondary';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst($invoice->status) }}</span>
                    </div>
                </div>
            @endif

            <!-- Client Invoices List -->
            <div class="sidebar-section mt-4">
                <h6>
                    <i class="bi bi-files me-2"></i>
                    Client Invoices
                    <span class="badge bg-success ms-1">{{ $clientInvoices->count() }}</span>
                </h6>

                @if($clientInvoices->count() > 0)
                    <div class="invoices-list">
                        @foreach($clientInvoices as $clientInvoice)
                            <a href="{{ route('client.chat.invoice', ['client' => $client->id, 'invoice' => $clientInvoice->id]) }}"
                               class="invoice-item {{ $invoice && $invoice->id == $clientInvoice->id ? 'active' : '' }}"
                               wire:navigate>
                                <div class="invoice-item-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="invoice-item-details">
                                    <div class="invoice-number">{{ $clientInvoice->number }}</div>
                                    <div class="invoice-meta">
                                        <span class="amount">${{ number_format($clientInvoice->total_price, 0) }}</span>
                                        <span class="dot">•</span>
                                        <span class="date">{{ $clientInvoice->created_at->format('M d') }}</span>
                                    </div>
                                </div>
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
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-light border text-center py-3">
                        <i class="bi bi-file-text text-muted mb-2 d-block" style="font-size: 2rem;"></i>
                        <p class="mb-0 text-muted">No invoices found</p>
                    </div>
                @endif
            </div>

            <!-- Recent Activity -->
            @if($invoice)
                <div class="sidebar-section mt-4">
                    <h6><i class="bi bi-clock-history me-2"></i>Recent Activity</h6>
                    <ul class="activity-list">
                        @if($invoice->paid_at)
                            <li>
                                <i class="bi bi-check-circle-fill text-success"></i>
                                <span>Payment confirmed - {{ $invoice->paid_at->diffForHumans() }}</span>
                            </li>
                        @endif
                        @if($invoice->sent_at)
                            <li>
                                <i class="bi bi-envelope-fill text-primary"></i>
                                <span>Invoice sent - {{ $invoice->sent_at->diffForHumans() }}</span>
                            </li>
                        @endif
                        {{--                        <li>--}}
                        {{--                            <i class="bi bi-calendar-event text-info"></i>--}}
                        {{--                            <span>Created - {{ $invoice?->created_at->diffForHumans() }}</span>--}}
                        {{--                        </li>--}}
                    </ul>
                </div>

                <!-- Attachments -->

            @endif
        </div>
    </div>
</div>

@push('styles')
    <style>
        .invoice-chat-container {
            height: calc(100vh - 180px);
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0,0,0,0.12);
            display: flex;
            flex-direction: column;
            border: 1px solid #e2e8f0;
        }

        .chat-header {
            background: linear-gradient(135deg, #2d5f5d 0%, #3d7a76 100%);
            border-bottom: none;
            box-shadow: 0 4px 12px rgba(45, 95, 93, 0.2);
            position: relative;
            overflow: hidden;
        }

        .chat-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
            background-size: cover;
            opacity: 0.3;
        }

        .chat-header .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .chat-header .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            transform: translateX(3px);
        }

        .chat-header h5 {
            color: white !important;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .chat-header small {
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .chat-header .text-muted {
            color: rgba(255, 255, 255, 0.85) !important;
        }

        .avatar-container {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            overflow: hidden;
            border: 3px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            position: relative;
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
            font-size: 1.1rem;
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .chat-body {
            display: flex;
            height: calc(100% - 75px);
            background: #f8fafb;
        }

        .messages-container {
            flex: 1;
            background: #f8fafb;
            padding: 24px;
            overflow-y: auto;
        }

        .messages-container::-webkit-scrollbar {
            width: 6px;
        }

        .messages-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .messages-container::-webkit-scrollbar-thumb {
            background: #2d5f5d;
            border-radius: 10px;
        }

        .messages-container::-webkit-scrollbar-thumb:hover {
            background: #3d7a76;
        }

        .chat-sidebar {
            width: 340px;
            background: white;
            border-left: 1px solid #e9ecef;
            padding: 24px;
            overflow-y: auto;
        }

        .chat-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .chat-sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .chat-sidebar::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 10px;
        }

        .chat-sidebar::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }

        .sidebar-header {
            padding-bottom: 16px;
            border-bottom: 2px solid #e9ecef;
            margin-bottom: 20px;
        }

        .sidebar-header h6 {
            color: #2d5f5d;
            font-weight: 700;
            font-size: 1rem;
        }

        .invoice-summary {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #d1fae5;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.08);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #dee2e6;
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
        }

        .invoice-item:hover {
            background: #f0fdf4;
            color: #2d5f5d;
        }

        .invoice-item.active {
            background: linear-gradient(90deg, rgba(45, 95, 93, 0.08) 0%, rgba(45, 95, 93, 0.03) 100%);
            border-left: 4px solid #2d5f5d;
            color: #2d5f5d;
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
            margin-right: 12px;
        }

        .invoice-item-icon i {
            font-size: 1rem;
            color: #6c757d;
        }

        .invoice-item.active .invoice-item-icon {
            background: linear-gradient(135deg, #2d5f5d, #3d7a76);
        }

        .invoice-item.active .invoice-item-icon i {
            color: white;
        }

        .invoice-item-details {
            flex: 1;
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
        }

        .invoice-meta .amount {
            font-weight: 500;
        }

        .invoice-meta .dot {
            margin: 0 5px;
        }

        .invoice-status i {
            font-size: 0.6rem;
        }

        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-list li {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f1f3f4;
        }

        .activity-list li i {
            margin-right: 10px;
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
        }

        .attachment-item:hover {
            background: #e9ecef;
            border-color: #1e4a46;
            transform: translateX(5px);
        }

        .attachment-item i {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .attachment-item span {
            font-size: 0.85rem;
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Scrollbar styling */
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

        /* Responsive */
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
                order: -1;
                border-left: none;
                border-bottom: 1px solid #e9ecef;
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

            // Initial scroll
            scrollToBottom();

            // Listen for new messages
            window.addEventListener('scroll-bottom', scrollToBottom);

            // Auto-scroll on new messages
            Livewire.on('newMessage', scrollToBottom);

            // Back to invoices
            window.addEventListener('back-to-invoices', function() {
                window.location.href = '{{ route("invoices.index") }}';
            });
        });
    </script>
@endpush
