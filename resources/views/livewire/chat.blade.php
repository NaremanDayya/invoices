<div class="invoice-chat-container">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="d-flex align-items-center">
                <button wire:click="$dispatch('back-to-invoices')" class="btn btn-sm btn-light me-3">
                    <i class="bi bi-arrow-left"></i>
                </button>
                <div class="avatar-container">
                    @if($client->company_logo)
                        <img src="{{ asset('storage/' . $client->company_logo) }}"
                             alt="{{ $client->company_name }}"
                             class="company-logo">
                    @else
                        <div class="avatar-placeholder bg-success">
                            {{ substr($client->company_name, 0, 2) }}
                        </div>
                    @endif
                </div>
                <div class="ms-3">
                    <h5 class="mb-1 text-dark">{{ $client->company_name }}</h5>
                    <small class="text-muted">
                        <i class="bi bi-file-earmark-text me-1"></i>
                        Invoice Discussion
                    </small>
                </div>
            </div>

            <div class="chat-actions">
                <button class="btn btn-sm btn-outline-success me-2">
                    <i class="bi bi-paperclip"></i>
                </button>
                <button class="btn btn-sm btn-outline-success me-2">
                    <i class="bi bi-telephone"></i>
                </button>
                <div class="dropdown">
                    <button class="btn btn-sm btn-success dropdown-toggle" type="button"
                            data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-download me-2"></i>Export Chat</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-printer me-2"></i>Print</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-trash me-2"></i>Clear Chat</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Body -->
    <div class="chat-body">
{{--        <div class="relative w-full md:w-[320px] xl:w-[400px] overflow-y-auto shrink-0 h-full border">--}}
{{--            <livewire:chat-list :selectedConversation="$selectedConversation" :conversation="$conversation">--}}
{{--        </div>--}}
        <!-- Messages Area -->
        <div class="messages-container" id="messages-container">
            <livewire:chat-box :client_id="$client->id" :selectedConversation="$selectedConversation" />
        </div>

        <!-- Sidebar - Invoice Details -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h6><i class="bi bi-receipt me-2"></i>Invoice Details</h6>
            </div>

            <div class="invoice-summary">
                <div class="summary-item">
                    <span class="label">Invoice #</span>
                    <span class="value text-success fw-bold">INV-{{ $client->id }}001</span>
                </div>
                <div class="summary-item">
                    <span class="label">Amount</span>
                    <span class="value">$4,250.00</span>
                </div>
                <div class="summary-item">
                    <span class="label">Due Date</span>
                    <span class="value">Nov 30, 2024</span>
                </div>
                <div class="summary-item">
                    <span class="label">Status</span>
                    <span class="badge bg-success-light text-success">Paid</span>
                </div>
            </div>

            <div class="sidebar-section mt-4">
                <h6><i class="bi bi-clock-history me-2"></i>Recent Activity</h6>
                <ul class="activity-list">
                    <li>
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>Payment confirmed - 2 hours ago</span>
                    </li>
                    <li>
                        <i class="bi bi-envelope-fill text-primary"></i>
                        <span>Invoice sent - Yesterday</span>
                    </li>
                    <li>
                        <i class="bi bi-chat-left-text-fill text-info"></i>
                        <span>Quotation discussion - 3 days ago</span>
                    </li>
                </ul>
            </div>

            <div class="sidebar-section mt-4">
                <h6><i class="bi bi-paperclip me-2"></i>Attachments</h6>
                <div class="attachments">
                    <a href="#" class="attachment-item">
                        <i class="bi bi-file-pdf text-danger"></i>
                        <span>invoice_001.pdf</span>
                    </a>
                    <a href="#" class="attachment-item">
                        <i class="bi bi-file-excel text-success"></i>
                        <span>items_list.xlsx</span>
                    </a>
                    <a href="#" class="attachment-item">
                        <i class="bi bi-file-image text-info"></i>
                        <span>receipt.jpg</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        .invoice-chat-container {
            height: calc(100vh - 180px); /* Adjusted to account for header and padding */
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
            border-left: 1px solid #e9ecef;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar-header {
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
            margin-bottom: 20px;
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

        .sidebar-section h6 {
            color: #495057;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
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
            border-color: #20c997;
            transform: translateX(5px);
        }

        .attachment-item i {
            font-size: 1.2rem;
            margin-right: 10px;
        }

        .attachment-item span {
            font-size: 0.85rem;
            flex: 1;
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
