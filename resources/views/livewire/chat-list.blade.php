<div class="invoices-chat-list">
    <!-- Header -->
    <div class="chat-list-header">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div>
                <h4 class="mb-0 text-dark">
                    <i class="bi bi-chat-left-text-fill text-success me-2"></i>
                    Invoice Discussions
                </h4>
                <p class="text-muted mb-0 small">Communicate with clients about invoices</p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- New Chat Button -->
                <button class="btn btn-success btn-sm d-flex align-items-center">
                    <i class="bi bi-plus-lg me-1"></i>
                    New Chat
                </button>

            </div>
        </div>

        <!-- Search and Filters -->
        <div class="chat-list-toolbar p-3 bg-light">
            <div class="row g-2">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" wire:model.live="search"
                               class="form-control border-start-0"
                               placeholder="Search by client or invoice number...">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex gap-2">
                        <select wire:model.live="filter" class="form-select form-select-sm">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="unread">Unread Only</option>
                            <option value="read">Read Only</option>
                        </select>

                        <select wire:model.live="perPage" class="form-select form-select-sm" style="width: 80px;">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="row mt-3">
                <div class="col">
                    <div class="d-flex flex-wrap gap-3">
                        <div class="stat-card">
                            <div class="stat-icon bg-success-light">
                                <i class="bi bi-chat-left-text text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $conversations->count() }}</h6>
                                <small class="text-muted">Total Chats</small>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="stat-icon bg-warning-light">
                                <i class="bi bi-envelope text-warning"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">
                                    {{ $conversations->where('unread_count', '>', 0)->count() }}
                                </h6>
                                <small class="text-muted">Unread</small>
                            </div>
                        </div>

{{--                        <div class="stat-card">--}}
{{--                            <div class="stat-icon bg-info-light">--}}
{{--                                <i class="bi bi-clock-history text-info"></i>--}}
{{--                            </div>--}}
{{--                            <div>--}}
{{--                                <h6 class="mb-0">--}}
{{--                                    {{ $conversations->where('is_last_message_read', false)->count() }}--}}
{{--                                </h6>--}}
{{--                                <small class="text-muted">Pending Replies</small>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversations List -->
    <div class="conversations-container">
        @if($conversations->isEmpty())
            <div class="empty-state text-center py-5">
                <i class="bi bi-chat-square-text display-4 text-muted mb-3"></i>
                <h5 class="text-muted">No conversations found</h5>
                <p class="text-muted">Start a new chat with your client</p>
                <button class="btn btn-success mt-2">
                    <i class="bi bi-plus-lg me-1"></i>
                    Start New Chat
                </button>
            </div>
        @else
            @foreach($conversations as $conversation)
                @php
                    $unreadCount = $conversation->unread_count ?? 0;
                    $isUnread = $unreadCount > 0;
                    $latestMessageTime = $conversation->latest_message_time
                        ? \Carbon\Carbon::parse($conversation->latest_message_time)->diffForHumans()
                        : 'No messages';
                    $client = $conversation->client ?? null;
                @endphp

                <div class="conversation-item {{ $isUnread ? 'unread' : '' }}"
                     wire:click="$dispatch('selectConversation', { id: {{ $conversation->id }} })"
                     style="cursor: pointer;">

                    <!-- Client Avatar -->
                    <div class="conversation-avatar">
                        @if($client?->company_logo)
                            <img src="{{ asset('storage/' . $client->company_logo) }}"
                                 alt="{{ $client->company_name }}"
                                 class="company-logo-sm">
                        @else
                            <div class="avatar-placeholder-sm bg-success">
                                {{ substr($client?->company_name ?? 'CC', 0, 2) }}
                            </div>
                        @endif

                        <!-- Online Status -->
                        <div class="online-status {{ rand(0, 1) ? 'online' : 'offline' }}"></div>
                    </div>

                    <!-- Conversation Details -->
                    <div class="conversation-details">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $client?->company_name ?? 'Unknown Client' }}</h6>
                                <p class="conversation-preview mb-0">
                                    {{ $conversation->latest_message_text ?? 'Start a conversation...' }}
                                </p>
                            </div>

                            <div class="text-end">
                                <small class="text-muted d-block">{{ $latestMessageTime }}</small>
                                @if($isUnread)
                                    <span class="badge bg-success rounded-pill px-2">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Invoice Info -->
                        <div class="invoice-info mt-2">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-receipt me-1"></i>
                                INV-{{ $conversation->id ?? '000' }}
                            </span>
                            <span class="badge bg-success-light text-success">
                                <i class="bi bi-cash-coin me-1"></i>
                                ${{ rand(100, 5000) }}
                            </span>
                            <span class="badge {{ rand(0, 1) ? 'bg-warning-light text-warning' : 'bg-success-light text-success' }}">
                                <i class="bi bi-circle-fill me-1"></i>
                                {{ rand(0, 1) ? 'Pending' : 'Paid' }}
                            </span>
                        </div>
                    </div>
                </div>

                @if(!$loop->last)
                    <hr class="my-2">
                @endif
            @endforeach

            <!-- Load More -->
            @if($hasMore)
                <div class="text-center py-3">
                    <button wire:click="loadMore"
                            class="btn btn-outline-success btn-sm"
                        {{ $loading ? 'disabled' : '' }}>
                        @if($loading)
                            <span class="spinner-border spinner-border-sm me-2"></span>
                            Loading...
                        @else
                            <i class="bi bi-arrow-down-circle me-1"></i>
                            Load More
                        @endif
                    </button>
                </div>
            @endif
        @endif
    </div>
</div>

@push('styles')
    <style>
        .invoices-chat-list {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            height: calc(100vh - 120px);
            overflow: hidden;
        }

        .chat-list-header {
            border-bottom: 1px solid #e9ecef;
            background: white;
        }

        .chat-list-toolbar {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .stat-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 15px;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            min-width: 120px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .bg-success-light {
            background-color: rgba(25, 135, 84, 0.1) !important;
        }

        .bg-warning-light {
            background-color: rgba(255, 193, 7, 0.1) !important;
        }

        .bg-info-light {
            background-color: rgba(13, 202, 240, 0.1) !important;
        }

        .conversations-container {
            height: calc(100% - 180px);
            overflow-y: auto;
            padding: 15px;
        }

        .conversation-item {
            display: flex;
            align-items: center;
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s;
            border: 1px solid transparent;
        }

        .conversation-item:hover, .conversation-item.unread {
            background: #f8f9fa;
            border-color: #e9ecef;
            transform: translateX(5px);
        }

        .conversation-item.unread {
            background: rgba(25, 135, 84, 0.05);
            border-left: 3px solid #198754;
        }

        .conversation-avatar {
            position: relative;
            margin-right: 15px;
        }

        .company-logo-sm {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #20c997;
        }

        .avatar-placeholder-sm {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            background: linear-gradient(135deg, #20c997, #198754);
        }

        .online-status {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .online-status.online {
            background-color: #28a745;
        }

        .online-status.offline {
            background-color: #6c757d;
        }

        .conversation-details {
            flex: 1;
        }

        .conversation-preview {
            font-size: 0.85rem;
            color: #6c757d;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .invoice-info {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .invoice-info .badge {
            font-size: 0.75rem;
            padding: 4px 8px;
        }

        .empty-state {
            padding: 60px 20px;
        }

        /* Custom Scrollbar */
        .conversations-container::-webkit-scrollbar {
            width: 6px;
        }

        .conversations-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .conversations-container::-webkit-scrollbar-thumb {
            background: #20c997;
            border-radius: 10px;
        }

        .conversations-container::-webkit-scrollbar-thumb:hover {
            background: #198754;
        }

        /* Loading Animation */
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .loading .conversation-item {
            animation: pulse 1.5s infinite;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-card {
                min-width: 100%;
                margin-bottom: 10px;
            }

            .invoice-info {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const conversationsContainer = document.querySelector('.conversations-container');

            // Infinite scroll
            if (conversationsContainer) {
                conversationsContainer.addEventListener('scroll', function() {
                    if (this.scrollTop + this.clientHeight >= this.scrollHeight - 100) {
                    @this.call('loadMore');
                    }
                });
            }

            // Search debounce
            let searchTimeout;
            const searchInput = document.querySelector('[wire\\:model="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                    @this.call('refresh');
                    }, 500);
                });
            }

            // Select conversation
            window.addEventListener('selectConversation', function(event) {
                // Your logic to open chat with selected conversation
                console.log('Selected conversation:', event.detail.id);
            });
        });
    </script>
@endpush
