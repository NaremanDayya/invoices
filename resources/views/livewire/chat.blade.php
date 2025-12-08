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
                        <i class="bi bi-chat-left-text me-1"></i>
                        @if(isset($selectedConversation) && $selectedConversation->invoice_id)
                            Invoice #{{ $selectedConversation->invoice->invoice_number ?? 'N/A' }}
                        @else
                            General Discussion
                        @endif
                    </small>
                </div>
            </div>

            <div class="chat-actions">
                <!-- Top right arrow button to toggle chat list -->
                <button wire:click="toggleChatList" class="btn btn-sm btn-outline-success me-2">
                    <i class="bi bi-chat-right-text"></i>
                </button>

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
        <!-- Chat List Sidebar (Hidden by default, toggled by button) -->
        @if($showChatList)
            <div class="chat-list-sidebar">
                <div class="sidebar-tabs">
                    <div class="nav nav-tabs" role="tablist">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#chats-tab">
                            <i class="bi bi-chat-left-text me-1"></i> Chats
                        </button>
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#invoices-tab">
                            <i class="bi bi-receipt me-1"></i> Invoices
                        </button>
                    </div>

                    <div class="tab-content">
                        <!-- Chats Tab -->
                        <div class="tab-pane fade show active" id="chats-tab">
                            <livewire:chat-list
                                :client="$client"
                                :selectedConversation="$selectedConversation ?? null"
                            />
                        </div>

                        <!-- Invoices Tab -->
                        <div class="tab-pane fade" id="invoices-tab">
                            <div class="invoices-list">
                                <div class="list-group">
                                    @foreach($invoices as $invoice)
                                        <a href="#"
                                           wire:click.prevent="openInvoiceChat({{ $invoice->id }})"
                                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold">Invoice #{{ $invoice->invoice_number }}</div>
                                                <small class="text-muted">{{ $invoice->created_at->format('M d, Y') }}</small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-success">${{ number_format($invoice->total_amount, 2) }}</div>
                                                <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Messages Area -->
        <div class="messages-container" id="messages-container">
            @if(isset($selectedConversation))
                <livewire:chat-box
                    :client_id="$client->id"
                    :selectedConversation="$selectedConversation"
                />
            @else
                <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                    <i class="bi bi-chat-left-text display-4 mb-3"></i>
                    <h4>Select a Conversation</h4>
                    <p>Choose a chat from the list or start a new one</p>
                </div>
            @endif
        </div>

        <!-- Sidebar - Invoice Details -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h6>
                    <i class="bi bi-receipt me-2"></i>
                    @if(isset($selectedConversation) && $selectedConversation->invoice)
                        Invoice #{{ $selectedConversation->invoice->invoice_number }}
                    @else
                        Client Details
                    @endif
                </h6>
            </div>

            @if(isset($selectedConversation) && $selectedConversation->invoice)
                    <?php $invoice = $selectedConversation->invoice; ?>
                <div class="invoice-summary">
                    <div class="summary-item">
                        <span class="label">Invoice #</span>
                        <span class="value text-success fw-bold">{{ $invoice->invoice_number }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Amount</span>
                        <span class="value">${{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Due Date</span>
                        <span class="value">{{ $invoice->due_date->format('M d, Y') }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Status</span>
                        <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}-light text-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($invoice->status) }}
                    </span>
                    </div>
                </div>
            @else
                <div class="client-summary">
                    <div class="summary-item">
                        <span class="label">Company</span>
                        <span class="value">{{ $client->company_name }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Contact</span>
                        <span class="value">{{ $client->contact_name ?? 'N/A' }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Email</span>
                        <span class="value">{{ $client->email }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="label">Phone</span>
                        <span class="value">{{ $client->phone ?? 'N/A' }}</span>
                    </div>
                </div>
            @endif

            <div class="sidebar-section mt-4">
                <h6><i class="bi bi-clock-history me-2"></i>Recent Activity</h6>
                <ul class="activity-list">
                    @if(isset($selectedConversation))
                        @foreach($selectedConversation->messages()->latest()->limit(3)->get() as $activity)
                            <li>
                                <i class="bi bi-chat-left-text-fill text-info"></i>
                                <span>{{ $activity->sender->name ?? 'User' }}: {{ Str::limit($activity->message, 30) }} - {{ $activity->created_at->diffForHumans() }}</span>
                            </li>
                        @endforeach
                    @else
                        <li class="text-muted text-center py-3">
                            No recent activity
                        </li>
                    @endif
                </ul>
            </div>

            @if(isset($selectedConversation) && $selectedConversation->invoice)
                <div class="sidebar-section mt-4">
                    <h6><i class="bi bi-paperclip me-2"></i>Attachments</h6>
                    <div class="attachments">
                        @if($selectedConversation->invoice->attachments()->count() > 0)
                            @foreach($selectedConversation->invoice->attachments()->limit(3)->get() as $attachment)
                                <a href="{{ Storage::url($attachment->path) }}" target="_blank" class="attachment-item">
                                    <i class="bi bi-file-{{ $attachment->type === 'pdf' ? 'pdf text-danger' : ($attachment->type === 'excel' ? 'excel text-success' : 'image text-info') }}"></i>
                                    <span>{{ $attachment->name }}</span>
                                </a>
                            @endforeach
                        @else
                            <div class="text-muted text-center py-2">
                                No attachments
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Add these new styles to your existing CSS */

        .chat-list-sidebar {
            width: 320px;
            background: white;
            border-right: 1px solid #e9ecef;
            overflow-y: auto;
            padding: 15px;
        }

        .sidebar-tabs {
            height: 100%;
        }

        .sidebar-tabs .nav-tabs {
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 15px;
        }

        .sidebar-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: 10px 15px;
            flex: 1;
            text-align: center;
        }

        .sidebar-tabs .nav-link.active {
            color: #198754;
            border-bottom: 2px solid #198754;
            background: transparent;
        }

        .invoices-list {
            height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .invoices-list .list-group-item {
            border: none;
            border-bottom: 1px solid #f1f3f4;
            padding: 12px 0;
            margin: 0;
        }

        .invoices-list .list-group-item:hover {
            background-color: #f8f9fa;
        }

        /* Adjust chat body for sidebar */
        .chat-body {
            display: flex;
            height: calc(100% - 75px);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .chat-list-sidebar {
                width: 280px;
                position: absolute;
                z-index: 1000;
                height: 100%;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }

            .chat-sidebar {
                width: 280px;
            }
        }

        @media (max-width: 768px) {
            .chat-body {
                flex-direction: column;
            }

            .chat-list-sidebar {
                width: 100%;
                position: absolute;
                z-index: 1000;
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

            // Listen for conversation selection
            Livewire.on('conversation-selected', (event) => {
                // Hide chat list after selection (for mobile)
            @this.set('showChatList', false);
            });
        });
    </script>
@endpush
