<div class="invoices-chat-list" dir="rtl">
    <!-- الرأس -->
    <div class="chat-list-header pb-2 border-bottom shadow-sm">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div>
                <h4 class="mb-0 text-dark">
                    <i class="bi bi-chat-left-text-fill text-primary ms-2"></i>
                    المحادثات
                </h4>
                <p class="text-muted mb-0 small">المحادثات الخاصة ومناقشات الفواتير</p>
            </div>
        </div>

        <!-- البحث والتصفية -->
        <div class="chat-list-toolbar px-3 pb-2">
            <div class="row g-2">
                <div class="col-12 mb-2">
                    <div class="input-group">
                        <input type="text" wire:model.live="search"
                               class="form-control border-secondary-subtle"
                               placeholder="بحث باسم العميل...">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                    </div>
                </div>

                <!-- فلتر نوع المحادثة -->
                <div class="col-12">
                    <div class="chat-type-tabs d-flex gap-1">
                        <button wire:click="$set('chatType', 'all')"
                                class="chat-type-btn flex-fill {{ $chatType === 'all' ? 'active' : '' }}">
                            <i class="bi bi-grid-fill ms-1"></i> الكل
                        </button>
                        <button wire:click="$set('chatType', 'private')"
                                class="chat-type-btn flex-fill {{ $chatType === 'private' ? 'active' : '' }}">
                            <i class="bi bi-person-fill ms-1"></i> خاص
                        </button>
                        <button wire:click="$set('chatType', 'invoice')"
                                class="chat-type-btn flex-fill {{ $chatType === 'invoice' ? 'active' : '' }}">
                            <i class="bi bi-receipt ms-1"></i> فواتير
                        </button>
                    </div>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <select wire:model.live="filter" class="form-select form-select-sm" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white;">
                            <option value="newest" style="color: #1e4a46;">الأحدث</option>
                            <option value="unread" style="color: #1e4a46;">غير المقروء</option>
                        </select>

                        <button class="btn btn-sm flex-grow-1 d-flex align-items-center justify-content-center"
                                style="background: #fbbd08; color: #1e4a46; border: none; font-weight: 600;"
                                data-bs-toggle="modal"
                                data-bs-target="#clientSelectionModal">
                            <i class="bi bi-plus-lg ms-1"></i>
                            محادثة جديدة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة المحادثات -->
    <div class="conversations-container" id="conversationsList">
        @if($conversations->isEmpty())
            <div class="empty-state text-center py-5">
                <i class="bi bi-chat-square-text display-4 text-muted mb-3"></i>
                <h5 class="text-muted">لا توجد محادثات</h5>
                <p class="text-muted">ابدأ محادثة جديدة مع عميلك</p>
                <button class="btn mt-2"
                        style="background: #1e4a46; color: white; border: none;"
                        data-bs-toggle="modal"
                        data-bs-target="#clientSelectionModal">
                    <i class="bi bi-plus-lg ms-1"></i>
                    بدء محادثة جديدة
                </button>
            </div>
        @else
            @foreach($conversations as $conversation)
                @php
                    $unreadCount    = (int) ($conversation->unread_count ?? 0);
                    $privateUnread  = (int) ($conversation->private_unread_count ?? 0);
                    $invoiceUnread  = (int) ($conversation->invoice_unread_count ?? 0);
                    $isUnread       = $unreadCount > 0;
                    $isInvoiceType  = $conversation->type === 'invoice';
                    $latestMessageTime = $conversation->latest_message_time
                        ? \Carbon\Carbon::parse($conversation->latest_message_time)->locale('ar')->diffForHumans()
                        : 'لا توجد رسائل';
                    $client = $conversation->client ?? null;

                    // Client-level metrics (used for private chats)
                    $activeInvoices = $client?->invoices?->where('is_cancelled', false) ?? collect();
                    $invoiceCount   = $activeInvoices->count();
                    $totalAmount    = $activeInvoices->sum('total_price');
                    $paidAmount     = $activeInvoices->where('payment_status', 'paid')->sum('paid_amount');
                    $pendingAmount  = $totalAmount - $paidAmount;
                    $hasPending     = $pendingAmount > 0;
                @endphp

                <a href="{{ route('client.chat', ['client' => $client?->id ?? 'unknown', 'conversation' => $conversation->id]) }}"
                   class="conversation-item {{ $isUnread ? 'unread' : '' }} {{ $isInvoiceType ? 'invoice-type' : 'private-type' }} text-decoration-none"
                   style="cursor: pointer; display: block; text-decoration: none; color: inherit;"
                   data-name="{{ $client?->name ?? '' }}"
                   data-type="{{ $conversation->type }}">


                    <!-- صورة رمزية للعميل -->
                    <div class="conversation-avatar">
                        <div class="avatar-placeholder-sm shadow-sm {{ $isInvoiceType ? 'avatar-invoice' : 'avatar-private' }}">
                            @if($isInvoiceType)
                                <i class="bi bi-receipt"></i>
                            @else
                                {{ mb_substr($client?->name ?? 'عم', 0, 2) }}
                            @endif
                        </div>
                        <div class="chat-type-indicator {{ $isInvoiceType ? 'type-invoice' : 'type-private' }}"
                             title="{{ $isInvoiceType ? 'محادثة فاتورة' : 'محادثة خاصة' }}"></div>
                    </div>

                    <div class="conversation-details">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="text-end">
                                <small class="text-muted d-block" style="font-size: 0.7rem;">{{ $latestMessageTime }}</small>
                                @if($isUnread)
                                    <div class="d-flex gap-1 justify-content-end mt-1">
                                        @if($isInvoiceType)
                                            {{-- Invoice chat: single badge for this invoice's unread --}}
                                            <span class="badge unread-badge-invoice rounded-pill px-2"
                                                  title="رسائل غير مقروءة في هذه الفاتورة">
                                                <i class="bi bi-receipt me-1"></i>{{ $unreadCount }}
                                            </span>
                                        @else
                                            {{-- Private chat: separate badges for private vs invoice sub-chats --}}
                                            @if($privateUnread > 0)
                                                <span class="badge unread-badge-private rounded-pill px-2"
                                                      title="رسائل خاصة غير مقروءة">
                                                    <i class="bi bi-chat-fill me-1"></i>{{ $privateUnread }}
                                                </span>
                                            @endif
                                            @if($invoiceUnread > 0)
                                                <span class="badge unread-badge-invoice rounded-pill px-2"
                                                      title="رسائل فواتير غير مقروءة">
                                                    <i class="bi bi-receipt me-1"></i>{{ $invoiceUnread }}
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div>
                                <div class="d-flex align-items-center gap-1 flex-wrap">
                                    <h6 class="mb-0 fw-bold text-dark">{{ $client?->name ?? 'عميل غير معروف' }}</h6>
                                    @if($isInvoiceType && $conversation->invoice)
                                        <span class="badge bg-secondary-subtle text-secondary" style="font-size:0.65rem;">
                                            #{{ $conversation->invoice->number ?? '' }}
                                        </span>
                                    @endif
                                </div>
                                <p class="conversation-preview mb-0 text-muted small mt-1">
                                    {{ $conversation->latest_message_text ?? 'ابدأ المحادثة...' }}
                                </p>
                            </div>
                        </div>

                        <!-- معلومات الفاتورة / العميل -->
                        <div class="invoice-info mt-2" style="flex-direction: row-reverse;">
                            @if($isInvoiceType)
                                {{-- Invoice chat: show this invoice's amount and payment status --}}
                                @if($conversation->invoice)
                                    <span class="badge bg-light text-dark border-0 shadow-sm py-1 px-2">
                                        <i class="bi bi-cash-coin ms-1 text-primary"></i>
                                        <span class="fw-bold">{{ number_format($conversation->invoice->total_price ?? 0, 0) }}</span> ر.س
                                    </span>
                                    @if(($conversation->invoice->payment_status ?? '') === 'paid')
                                        <span class="badge bg-success-subtle text-success-emphasis border-0 shadow-sm py-1 px-2">
                                            <i class="bi bi-check-circle-fill ms-1"></i> مدفوع
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning-emphasis border-0 shadow-sm py-1 px-2">
                                            <i class="bi bi-clock-history ms-1"></i> معلق
                                        </span>
                                    @endif
                                @endif
                            @else
                                {{-- Private chat: show client summary --}}
                                <span class="badge bg-light text-dark border-0 shadow-sm py-1 px-2">
                                    <i class="bi bi-cash-coin ms-1 text-primary"></i>
                                    <span class="fw-bold">{{ number_format($totalAmount, 0) }}</span> ر.س
                                </span>
                                <span class="badge bg-light text-muted border-0 shadow-sm py-1 px-2">
                                    <i class="bi bi-receipt ms-1"></i>
                                    {{ $invoiceCount }}
                                </span>
                                @if($hasPending)
                                    <span class="badge bg-warning-subtle text-warning-emphasis border-0 shadow-sm py-1 px-2">
                                        <i class="bi bi-clock-history ms-1"></i>
                                        {{ number_format($pendingAmount, 0) }} معلق
                                    </span>
                                @else
                                    <span class="badge bg-success-subtle text-success-emphasis border-0 shadow-sm py-1 px-2">
                                        <i class="bi bi-check-circle-fill ms-1"></i>
                                        خالص
                                    </span>
                                @endif
                            @endif
                        </div>
                    </div>
                </a>

                @if(!$loop->last)
                    <hr class="my-2">
                @endif
            @endforeach

            <!-- تحميل المزيد -->
            @if($hasMore)
                <div class="text-center py-3">
                    <button wire:click="loadMore"
                            class="btn btn-sm"
                            style="border: 1px solid #1e4a46; color: #1e4a46;"
                        {{ $loading ? 'disabled' : '' }}>
                        @if($loading)
                            <span class="spinner-border spinner-border-sm ms-2"></span>
                            جاري التحميل...
                        @else
                            <i class="bi bi-arrow-down-circle ms-1"></i>
                            تحميل المزيد
                        @endif
                    </button>
                </div>
            @endif
        @endif
    </div>
    @include('partials.client-selection-modal')

    @push('styles')
        <style>
            .invoices-chat-list {
                background: white;
                border-radius: 20px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.05);
                height: 100%;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                border: 1px solid #e2e8f0;
            }

            .chat-list-header {
                background: linear-gradient(135deg, #2d5f5d 0%, #3d7a76 100%);
                flex-shrink: 0;
                color: white;
                position: relative;
            }

            .chat-list-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
                background-size: cover;
                opacity: 0.3;
                pointer-events: none;
            }

            .chat-list-toolbar {
                position: relative;
                z-index: 2;
            }

            .chat-list-header h4 {
                color: white;
            }

            .chat-list-header .text-muted {
                color: rgba(255, 255, 255, 0.8) !important;
            }

            .chat-list-header .text-primary {
                color: #10b981 !important;
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
                flex-direction: row-reverse;
                text-align: right;
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
                flex-grow: 1;
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
                text-align: right;
            }

            .conversation-item:hover {
                background: #f0fdf4;
                border-color: #d1fae5;
                transform: translateX(-5px);
            }

            .conversation-item.unread {
                background: rgba(45, 95, 93, 0.05);
                border-right: 3px solid #2d5f5d;
                border-left: none;
            }

            .conversation-item.unread:hover {
                background: rgba(45, 95, 93, 0.08);
            }

            .conversation-item.invoice-type.unread {
                border-right-color: #7c3aed;
                background: rgba(124, 58, 237, 0.04);
            }

            .conversation-item.invoice-type.unread:hover {
                background: rgba(124, 58, 237, 0.07);
            }

            .avatar-invoice {
                background: linear-gradient(135deg, #7c3aed, #9d5cf5) !important;
                font-size: 1.1rem;
            }

            .avatar-private {
                background: linear-gradient(135deg, #2d5f5d, #3d7a76) !important;
            }

            .chat-type-indicator {
                position: absolute;
                bottom: 2px;
                left: 2px;
                right: auto;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                border: 2px solid white;
            }

            .chat-type-indicator.type-invoice {
                background-color: #7c3aed;
            }

            .chat-type-indicator.type-private {
                background-color: #2d5f5d;
            }

            .unread-badge-private {
                background: #dc3545;
                color: white;
                font-size: 0.7rem;
            }

            .unread-badge-invoice {
                background: #7c3aed;
                color: white;
                font-size: 0.7rem;
            }

            .chat-type-tabs {
                background: rgba(0,0,0,0.15);
                border-radius: 8px;
                padding: 3px;
            }

            .chat-type-btn {
                background: transparent;
                border: none;
                color: rgba(255,255,255,0.75);
                border-radius: 6px;
                padding: 5px 8px;
                font-size: 0.78rem;
                font-weight: 500;
                transition: all 0.2s;
                cursor: pointer;
                white-space: nowrap;
            }

            .chat-type-btn:hover {
                background: rgba(255,255,255,0.15);
                color: white;
            }

            .chat-type-btn.active {
                background: white;
                color: #2d5f5d;
                font-weight: 700;
                box-shadow: 0 1px 4px rgba(0,0,0,0.15);
            }

            .conversation-avatar {
                position: relative;
                margin-left: 15px;
                margin-right: 0;
            }

            .company-logo-sm {
                width: 50px;
                height: 50px;
                border-radius: 10px;
                object-fit: cover;
                border: 2px solid #10b981;
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
            }

            .conversation-details {
                flex: 1;
                text-align: right;
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

            /* شريط التمرير المخصص */
            .conversations-container::-webkit-scrollbar {
                width: 6px;
            }

            .conversations-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .conversations-container::-webkit-scrollbar-thumb {
                background: #2d5f5d;
                border-radius: 10px;
            }

            .conversations-container::-webkit-scrollbar-thumb:hover {
                background: #3d7a76;
            }

            /* رسالة لا توجد محادثات */
            #noConvoMsg.hidden {
                display: none;
            }

            .conversation-item.hidden {
                display: none;
            }

            /* رسوم متحركة للتحميل */
            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }

            .loading .conversation-item {
                animation: pulse 1.5s infinite;
            }

            /* تجاوب */
            @media (max-width: 768px) {
                .invoice-info {
                    flex-direction: column;
                    align-items: flex-end;
                }

                .conversation-item {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .conversation-avatar {
                    margin-left: 0;
                    margin-bottom: 10px;
                    align-self: flex-end;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const conversationsContainer = document.querySelector('.conversations-container');

                // التمرير اللانهائي
                if (conversationsContainer) {
                    conversationsContainer.addEventListener('scroll', function() {
                        if (this.scrollTop + this.clientHeight >= this.scrollHeight - 100) {
                        @this.call('loadMore');
                        }
                    });
                }

                // البحث بفاصل زمني
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

                // اختيار محادثة
                window.addEventListener('selectConversation', function(event) {
                    console.log('المحادثة المختارة:', event.detail.id);
                });

                // Ensure modal works with Livewire
                Livewire.hook('morph.updated', ({ el, component }) => {
                    // Reinitialize Bootstrap modals after Livewire updates
                    const modalEl = document.getElementById('clientSelectionModal');
                    if (modalEl && typeof bootstrap !== 'undefined') {
                        // Dispose old instance if exists
                        const oldModal = bootstrap.Modal.getInstance(modalEl);
                        if (oldModal) {
                            oldModal.dispose();
                        }
                        // Create new instance
                        new bootstrap.Modal(modalEl);
                    }
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput      = document.querySelector('[wire\\:model="search"]');
                const listContainer    = document.getElementById('conversationsList');
                const emptyState       = document.getElementById('noConvoMsg');

                if (!searchInput || !listContainer) return;

                const items = Array.from(listContainer.querySelectorAll('a[data-name]'));

                function filterList() {
                    const q = searchInput.value.trim().toLowerCase();
                    let visible = 0;

                    items.forEach(item => {
                        const name = item.dataset.name.toLowerCase();
                        const show = !q || name.includes(q);
                        item.classList.toggle('hidden', !show);
                        if (show) visible++;
                    });

                    if (emptyState) emptyState.classList.toggle('hidden', visible !== 0);
                }

                filterList();
                searchInput.addEventListener('input', filterList);
            });
        </script>
    @endpush
</div>
