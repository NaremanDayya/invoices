<div class="invoice-chat-messages">
    <!-- Messages List -->
    <div class="messages-list" id="messages-list">
        @php
            $currentDate = null;
        @endphp

        @foreach($loadedMessages as $message)
            @php
                $isSender = $message->sender_id === Auth::id();
                $messageTime = $message->created_at->format('g:i A');
                $messageDate = $message->created_at->format('Y-m-d');
                $isEdited = $message->edited_at ?? false;

                // Show date separator if date changed
                if ($currentDate !== $messageDate):
                    $currentDate = $messageDate;
            @endphp
            <div class="date-separator">
                <span>{{ $message->created_at->format('F j, Y') }}</span>
            </div>
            @php endif; @endphp

            <div class="message-wrapper {{ $isSender ? 'sent' : 'received' }}" wire:key="message-{{ $message->id }}">
                <!-- Message Bubble -->
                <div class="message-bubble {{ $isSender ? 'sent-bubble' : 'received-bubble' }}">
                    <!-- Message Content -->
                    @if($message->message === 'like')
                        <div class="like-message">
                            <i class="bi bi-heart-fill text-danger"></i>
                        </div>
                    @else
                        <div class="message-content">
                            {{ $message->message }}

                            @if($isEdited)
                                <small class="edited-text">(edited)</small>
                            @endif
                        </div>
                    @endif

                    <!-- Message Footer -->
                    <div class="message-footer">
                        <span class="message-time">{{ $messageTime }}</span>

                        @if($isSender)
                            <span class="message-status">
                                @if($message->read_at)
                                    <i class="bi bi-check2-all text-success"></i>
                                @else
                                    <i class="bi bi-check2"></i>
                                @endif
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Message Actions (for sender's messages) -->
                @if($isSender && $message->message !== 'like')
                    <div class="message-actions">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" type="button"
                                    data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#"
                                       wire:click.prevent="$dispatch('edit-message', { id: {{ $message->id }} })">
                                        <i class="bi bi-pencil me-2"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#"
                                       onclick="confirm('Are you sure?') && Livewire.dispatch('deleteMessage', { messageId: {{ $message->id }} })">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- Load More Indicator -->
        @if($hasMoreMessages)
            <div class="load-more-indicator" id="load-more-trigger">
                <button wire:click="loadMore" class="btn btn-sm btn-outline-success"
                    {{ $loading ? 'disabled' : '' }}>
                    @if($loading)
                        <span class="spinner-border spinner-border-sm me-2"></span>
                        Loading...
                    @else
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Load Older Messages
                    @endif
                </button>
            </div>
        @endif
    </div>

    <!-- Typing Indicator -->
    <div class="typing-indicator" style="display: none;">
        <div class="typing-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <span class="typing-text">Client is typing...</span>
    </div>

    <!-- Message Input -->
    <div class="message-input-container">
        <div class="input-group">
            <!-- Main Input -->
            <input type="text"
                   wire:model="message"
                   wire:keydown.enter.prevent="sendMessage"
                   class="form-control border-success"
                   placeholder="Type your message about the invoice..."
                   aria-label="Message"
                   id="message-input">

            <!-- Send Button -->
            <button wire:click="sendMessage"
                    wire:keydown.enter.prevent
                    class="btn btn-success"
                    type="button"
                {{ empty(trim($message)) ? 'disabled' : '' }}>
                <i class="bi bi-send"></i>
            </button>

            <!-- Like Button -->
            <button wire:click="sendLike" class="btn btn-outline-success" type="button">
                <i class="bi bi-heart"></i>
            </button>
        </div>

        <!-- Quick Suggestions -->
        <div class="quick-suggestions mt-2">
            <small class="text-muted me-2">Quick reply:</small>
            @php
                $suggestions = [
                    'Payment received, thank you!',
                    'Can you send the invoice again?',
                    'Please update the due date',
                    'When will the payment be processed?'
                ];
            @endphp

            @foreach($suggestions as $suggestion)
                <button wire:click="$set('message', '{{ $suggestion }}')"
                        class="btn btn-sm btn-light border">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    </div>
</div>

@push('styles')
    <style>
        .invoice-chat-messages {
            height: 100%;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .date-separator {
            text-align: center;
            margin: 15px 0;
            position: relative;
        }

        .date-separator span {
            background: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.8rem;
            color: #6c757d;
            border: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .messages-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .message-wrapper {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-end;
        }

        .message-wrapper.sent {
            flex-direction: row-reverse;
        }

        .message-wrapper.received {
            flex-direction: row;
        }

        .message-bubble {
            max-width: 70%;
            padding: 10px 14px;
            border-radius: 18px;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }

        .sent-bubble {
            background: linear-gradient(135deg, #198754, #20c997);
            color: white;
            border-bottom-right-radius: 4px;
            margin-left: 10px;
        }

        .received-bubble {
            background: white;
            color: #212529;
            border-bottom-left-radius: 4px;
            border: 1px solid #e9ecef;
            margin-right: 10px;
        }

        .like-message {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }

        .sent-bubble .like-message {
            background: rgba(255, 255, 255, 0.2);
        }

        .received-bubble .like-message {
            background: rgba(0, 0, 0, 0.05);
        }

        .like-message i {
            font-size: 1.2rem;
        }

        .message-content {
            word-break: break-word;
            line-height: 1.5;
        }

        .edited-text {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-left: 5px;
        }

        .message-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-top: 4px;
            gap: 5px;
        }

        .message-time {
            font-size: 0.7rem;
            opacity: 0.8;
        }

        .sent-bubble .message-time {
            color: rgba(255,255,255,0.8);
        }

        .received-bubble .message-time {
            color: #6c757d;
        }

        .message-status {
            font-size: 0.8rem;
        }

        .message-actions {
            opacity: 0;
            transition: opacity 0.3s;
        }

        .message-wrapper:hover .message-actions {
            opacity: 1;
        }

        .load-more-indicator {
            text-align: center;
            padding: 10px 0;
            margin: 10px 0;
        }

        .load-more-indicator button {
            border-radius: 20px;
            padding: 5px 15px;
            font-size: 0.85rem;
        }

        .typing-indicator {
            background: white;
            padding: 10px 15px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            border: 1px solid #e9ecef;
            max-width: fit-content;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .typing-dots {
            display: flex;
            gap: 3px;
        }

        .typing-dots span {
            width: 8px;
            height: 8px;
            background: #20c997;
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dots span:nth-child(2) { animation-delay: 0.2s; }
        .typing-dots span:nth-child(3) { animation-delay: 0.4s; }

        .typing-text {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .message-input-container {
            background: white;
            padding: 15px;
            border-top: 1px solid #e9ecef;
            border-radius: 0 0 12px 12px;
            flex-shrink: 0;
        }

        .input-group .form-control:focus {
            border-color: #20c997;
            box-shadow: 0 0 0 0.25rem rgba(32, 201, 151, 0.25);
        }

        .quick-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .quick-suggestions button {
            font-size: 0.8rem;
            white-space: nowrap;
            padding: 4px 10px;
            transition: all 0.2s;
        }

        .quick-suggestions button:hover {
            background-color: #f8f9fa;
            transform: translateY(-1px);
        }

        /* Animations */
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }

        .message-bubble {
            animation: messageAppear 0.3s ease-out;
        }

        @keyframes messageAppear {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Scrollbar styling */
        .messages-list::-webkit-scrollbar {
            width: 6px;
        }

        .messages-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        .messages-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .messages-list::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .message-bubble {
                max-width: 85%;
            }

            .quick-suggestions {
                flex-direction: column;
                align-items: stretch;
            }

            .quick-suggestions button {
                text-align: left;
                white-space: normal;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const messagesList = document.getElementById('messages-list');
            const messageInput = document.getElementById('message-input');
            let isLoading = false;
            let scrollPositionBeforeLoad = 0;

            // Auto-scroll to bottom on initial load and new messages
            function scrollToBottom() {
                if (messagesList) {
                    messagesList.scrollTop = messagesList.scrollHeight;
                }
            }

            // Initial scroll to bottom
            scrollToBottom();

            // Listen for Livewire events
            Livewire.on('scroll-bottom', scrollToBottom);

            // Focus input on load
            if (messageInput) {
                messageInput.focus();
            }

            // Auto-scroll after Livewire updates (when new messages are added)
            Livewire.hook('morph.updated', ({ el, component }) => {
                if (el.id === 'messages-list') {
                    scrollToBottom();
                }
            });

            // Handle sending message with Enter key
            if (messageInput) {
                messageInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        if (messageInput.value.trim()) {
                            Livewire.dispatch('sendMessage');
                        }
                    }
                });
            }

            // Infinite scroll for loading more messages
            if (messagesList) {
                messagesList.addEventListener('scroll', function() {
                    // Load more messages when scrolling to top
                    if (messagesList.scrollTop < 100 && !isLoading) {
                        const loadMoreTrigger = document.getElementById('load-more-trigger');
                        if (loadMoreTrigger) {
                            scrollPositionBeforeLoad = messagesList.scrollHeight;
                            Livewire.dispatch('loadMore');
                            isLoading = true;
                        }
                    }
                });
            }

            // Reset loading state after messages are loaded
            Livewire.on('messages-loaded', () => {
                isLoading = false;
                // Maintain scroll position after loading more messages
                if (messagesList && scrollPositionBeforeLoad > 0) {
                    const newHeight = messagesList.scrollHeight;
                    messagesList.scrollTop = newHeight - scrollPositionBeforeLoad;
                    scrollPositionBeforeLoad = 0;
                }
            });

            // Typing simulation
            let typingTimeout;
            if (messageInput) {
                messageInput.addEventListener('input', function() {
                    clearTimeout(typingTimeout);

                    typingTimeout = setTimeout(() => {
                        const typingIndicator = document.querySelector('.typing-indicator');
                        if (typingIndicator && messageInput.value.length > 2) {
                            typingIndicator.style.display = 'flex';

                            setTimeout(() => {
                                typingIndicator.style.display = 'none';
                            }, 3000);
                        }
                    }, 1000);
                });
            }

            // Clear message input after sending
            Livewire.on('messageSent', () => {
                if (messageInput) {
                    messageInput.value = '';
                    messageInput.focus();
                }
                // Ensure scroll to bottom after message sent
                setTimeout(scrollToBottom, 100);
            });
        });
    </script>
@endpush
