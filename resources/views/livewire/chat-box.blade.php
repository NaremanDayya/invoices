<div class="invoice-chat-messages" dir="rtl">
    <!-- قائمة الرسائل -->
    <div class="messages-list" id="messages-list">
        @php
            $currentDate = null;
        @endphp

        @foreach($loadedMessages as $message)
            @php
                $isSender = $message->sender_id === Auth::id();
                $messageTime = $message->created_at->translatedFormat('g:i A');
                $messageDate = $message->created_at->format('Y-m-d');
                $isEdited = $message->edited_at ?? false;

                // عرض فاصل التاريخ إذا تغير
                if ($currentDate !== $messageDate):
                    $currentDate = $messageDate;
            @endphp
            <div class="date-separator">
                <span>{{ $message->created_at->translatedFormat('j F, Y') }}</span>
            </div>
            @php endif; @endphp

            <div class="message-wrapper {{ $isSender ? 'sent' : 'received' }}" wire:key="message-{{ $message->id }}">
                <!-- فقاعة الرسالة -->
                <div class="message-bubble {{ $isSender ? 'sent-bubble' : 'received-bubble' }}">
                    <!-- محتوى الرسالة -->
                    @if($message->message === 'like')
                        <div class="like-message">
                            <i class="bi bi-heart-fill text-danger"></i>
                        </div>
                    @elseif($message->image_path)
                        <!-- رسالة صورة -->
                        <div class="image-message">
                            <img src="{{ Storage::url($message->image_path) }}"
                                 alt="صورة مشاركة"
                                 class="chat-image"
                                 onclick="window.open('{{ Storage::url($message->image_path) }}', '_blank')">
                            @if($message->message && $message->message !== '[Image]')
                                <div class="image-caption">{{ $message->message }}</div>
                            @endif
                        </div>
                    @else
                        <div class="message-content">
                            @if($message->message_type === 'system')
                                {!! nl2br(e($message->message)) !!}
                            @else
                                {{ $message->message }}
                            @endif

                            @if($isEdited)
                                <small class="edited-text">(تم التعديل)</small>
                            @endif
                        </div>
                    @endif

                    <!-- تذييل الرسالة -->
                    <div class="message-footer">
                        @if($isSender)
                            <span class="message-status">
                                @if($message->read_at)
                                    <i class="bi bi-check2-all text-success"></i>
                                @else
                                    <i class="bi bi-check2"></i>
                                @endif
                            </span>
                        @endif
                        <span class="message-time">{{ $messageTime }}</span>
                    </div>
                </div>

                <!-- إجراءات الرسالة (لرسائل المرسل) -->
                @if($isSender && $message->message !== 'like')
                    <div class="message-actions">
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted p-0" type="button"
                                    data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" dir="rtl">
                                <li>
                                    <a class="dropdown-item" href="#"
                                       wire:click.prevent="$dispatch('edit-message', { id: {{ $message->id }} })">
                                        <i class="bi bi-pencil ms-2"></i>تعديل
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#"
                                       onclick="confirm('هل أنت متأكد؟') && Livewire.dispatch('deleteMessage', { messageId: {{ $message->id }} })">
                                        <i class="bi bi-trash ms-2"></i>حذف
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach

        <!-- مؤشر تحميل المزيد -->
{{--        @if($hasMoreMessages)--}}
{{--            <div class="load-more-indicator" id="load-more-trigger">--}}
{{--                <button wire:click="loadMore" class="btn btn-sm btn-outline-success"--}}
{{--                    {{ $loading ? 'disabled' : '' }}>--}}
{{--                    @if($loading)--}}
{{--                        <span class="spinner-border spinner-border-sm ms-2"></span>--}}
{{--                        جاري التحميل...--}}
{{--                    @else--}}
{{--                        <i class="bi bi-arrow-clockwise ms-1"></i>--}}
{{--                        تحميل رسائل أقدم--}}
{{--                    @endif--}}
{{--                </button>--}}
{{--            </div>--}}
{{--        @endif--}}
    </div>

    <!-- مؤشر الكتابة -->
    <div class="typing-indicator" style="display: none;">
        <div class="typing-dots">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <span class="typing-text">يكتب الآن...</span>
    </div>

    <!-- إدخال الرسالة -->
    <div class="message-input-container">
        <div class="input-group">
            <!-- زر الإعجاب -->
            <button wire:click="sendLike" class="btn btn-outline-success" type="button">
                <i class="bi bi-heart"></i>
            </button>

            <!-- زر الإرسال -->
            <button id="send-message-btn"
                    class="btn btn-success"
                    type="button">
                <i class="bi bi-send"></i>
            </button>

            <!-- حقل الإدخال الرئيسي -->
            <input type="text"
                   wire:model.defer="message"
                   class="form-control border-success"
                   placeholder="اكتب رسالتك حول الفاتورة..."
                   aria-label="الرسالة"
                   id="message-input"
            >

            <!-- زر المرفقات -->
            <button class="btn btn-outline-success" type="button" id="attach-file-btn">
                <i class="bi bi-paperclip"></i>
            </button>
        </div>

        <!-- الاقتراحات السريعة -->
        <div class="quick-suggestions mt-2">
            <small class="text-muted ms-2">رد سريع:</small>
            @php
                $suggestions = [
                    'تم استلام الدفع، شكراً لك!',
                    'هل يمكنك إرسال الفاتورة مرة أخرى؟',
                    'الرجاء تحديث تاريخ الاستحقاق',
                    'متى سيتم معالجة الدفع؟'
                ];
            @endphp

            @foreach($suggestions as $suggestion)
                <button wire:click="sendQuickReply('{{ $suggestion }}')"
                        class="btn btn-sm btn-light border">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>

        <!-- قائمة اقتراحات الإشارات -->
        <div id="mention-suggestions" class="mention-suggestions" style="display:none;"></div>
    </div>

    <!-- نافذة معاينة الصورة (نمط واتساب) -->
    <div class="image-preview-modal" id="image-preview-modal" style="display: none;">
        <div class="preview-overlay"></div>
        <div class="preview-container">
            <div class="preview-header">
                <h6 class="mb-0">معاينة الصورة</h6>
                <button class="preview-close-btn" id="close-preview">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <div class="preview-body">
                <img id="preview-image" src="" alt="معاينة">
            </div>

            <div class="preview-footer">
                <button class="btn btn-success send-image-btn" id="send-image-btn">
                    <i class="bi bi-send-fill ms-2"></i>إرسال
                </button>
                <div class="caption-input-group">
                    <input type="text"
                           class="form-control caption-input"
                           id="image-caption"
                           placeholder="أضف وصفاً...">
                </div>
            </div>
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
            flex-direction: row;
        }

        .message-wrapper.received {
            flex-direction: row-reverse;
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
            border-bottom-left-radius: 4px;
            margin-right: 10px;
        }

        .received-bubble {
            background: white;
            color: #212529;
            border-bottom-right-radius: 4px;
            border: 1px solid #e9ecef;
            margin-left: 10px;
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
            text-align: right;
        }

        .image-message {
            max-width: 300px;
        }

        .chat-image {
            width: 100%;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
        }

        .chat-image:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .image-caption {
            margin-top: 8px;
            font-size: 0.9rem;
            word-break: break-word;
            text-align: right;
        }

        .sent-bubble .image-caption {
            color: rgba(255, 255, 255, 0.95);
        }

        .received-bubble .image-caption {
            color: #212529;
        }

        .edited-text {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-right: 5px;
        }

        .message-footer {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-top: 4px;
            gap: 5px;
        }

        .sent-bubble .message-footer {
            justify-content: flex-start;
        }

        .received-bubble .message-footer {
            justify-content: flex-start;
            flex-direction: row-reverse;
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

        .dropdown-menu {
            text-align: right;
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
            flex-direction: row-reverse;
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
            border-radius: 12px 12px 0 0;
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
            flex-direction: row-reverse;
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

        /* تنسيق شريط التمرير */
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

        /* تجاوب */
        @media (max-width: 768px) {
            .message-bubble {
                max-width: 85%;
            }

            .quick-suggestions {
                flex-direction: column;
                align-items: stretch;
            }

            .quick-suggestions button {
                text-align: right;
                white-space: normal;
            }
        }

        /* أنماط نافذة معاينة الصورة (نمط واتساب) */
        .image-preview-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(5px);
        }

        .preview-container {
            position: relative;
            z-index: 10000;
            background: #1e1e1e;
            border-radius: 12px;
            max-width: 90vw;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            animation: modalSlideUp 0.3s ease-out;
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .preview-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            background: #2a2a2a;
            border-radius: 12px 12px 0 0;
            border-bottom: 1px solid #3a3a3a;
            flex-direction: row-reverse;
        }

        .preview-header h6 {
            color: #ffffff;
            margin: 0;
            font-weight: 500;
        }

        .preview-close-btn {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .preview-close-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .preview-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: #1e1e1e;
            min-height: 300px;
            max-height: 60vh;
            overflow: hidden;
        }

        .preview-body img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .preview-footer {
            padding: 20px;
            background: #2a2a2a;
            border-radius: 0 0 12px 12px;
            border-top: 1px solid #3a3a3a;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-direction: row-reverse;
        }

        .caption-input-group {
            flex: 1;
        }

        .caption-input {
            background: #3a3a3a;
            border: 1px solid #4a4a4a;
            color: #ffffff;
            border-radius: 25px;
            padding: 10px 20px;
            font-size: 0.95rem;
            text-align: right;
        }

        .caption-input:focus {
            background: #3a3a3a;
            border-color: #20c997;
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(32, 201, 151, 0.25);
        }

        .caption-input::placeholder {
            color: #888;
            text-align: right;
        }

        .send-image-btn {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            white-space: nowrap;
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            transition: all 0.3s;
        }

        .send-image-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(32, 201, 151, 0.4);
        }

        .send-image-btn:active {
            transform: translateY(0);
        }

        /* تنسيق زر المرفقات */
        #attach-file-btn {
            border-top-left-radius: 25px;
            border-bottom-left-radius: 25px;
        }

        #message-input {
            border-radius: 0;
            text-align: right;
        }

        .message-input-container .btn-success {
            border-radius: 0;
        }

        .message-input-container .btn-outline-success:first-child {
            border-top-right-radius: 25px;
            border-bottom-right-radius: 25px;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .mention-suggestions {
            position: absolute;
            bottom: 100%;
            right: 15px;
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            width: 250px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 -4px 12px rgba(0,0,0,0.1);
            text-align: right;
        }

        .mention-item {
            padding: 8px 12px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background 0.2s;
            flex-direction: row-reverse;
        }

        .mention-item:last-child {
            border-bottom: none;
        }

        .mention-item:hover {
            background-color: #f1f8f5;
            color: #198754;
        }

        .mention-avatar {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: #6c757d;
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

            // التمرير التلقائي للأسفل عند التحميل الأولي والرسائل الجديدة
            function scrollToBottom() {
                if (messagesList) {
                    messagesList.scrollTop = messagesList.scrollHeight;
                }
            }

            // التمرير الأولي للأسفل
            scrollToBottom();

            // الاستماع لأحداث Livewire
            Livewire.on('scroll-bottom', scrollToBottom);

            // التركيز على حقل الإدخال عند التحميل
            if (messageInput) {
                messageInput.focus();
            }

            // معالج زر الإرسال
            const sendBtn = document.getElementById('send-message-btn');
            if (sendBtn) {
                sendBtn.addEventListener('click', function() {
                    const messageText = messageInput.value.trim();
                    if (messageText) {
                        @this.set('message', messageText);
                        @this.call('sendMessage');
                    }
                });
            }

            // التمرير التلقائي بعد تحديثات Livewire (عند إضافة رسائل جديدة)
            Livewire.hook('morph.updated', ({ el, component }) => {
                if (el.id === 'messages-list') {
                    scrollToBottom();
                }
            });

            // التعامل مع إرسال الرسالة بمفتاح Enter
            if (messageInput) {
                messageInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        const messageText = messageInput.value.trim();
                        if (messageText) {
                            // Update Livewire model before sending
                            @this.set('message', messageText);
                            @this.call('sendMessage');
                        }
                    }
                });
            }

            // التمرير اللانهائي لتحميل المزيد من الرسائل
            if (messagesList) {
                messagesList.addEventListener('scroll', function() {
                    // تحميل المزيد من الرسائل عند التمرير للأعلى
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

            // إعادة تعيين حالة التحميل بعد تحميل الرسائل
            Livewire.on('messages-loaded', () => {
                isLoading = false;
                // الحفاظ على موضع التمرير بعد تحميل المزيد من الرسائل
                if (messagesList && scrollPositionBeforeLoad > 0) {
                    const newHeight = messagesList.scrollHeight;
                    messagesList.scrollTop = newHeight - scrollPositionBeforeLoad;
                }
            });

            // محاكاة الكتابة
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

            // مسح حقل الإدخال بعد الإرسال
            Livewire.on('message-sent', () => {
                if (messageInput) {
                    messageInput.value = '';
                    messageInput.focus();
                }
                // Auto-scroll to bottom after sending
                setTimeout(() => {
                    scrollToBottom();
                }, 100);
            });

            // ========== وظيفية لصق الصور والمرفقات ==========

            let currentImageFile = null;
            const previewModal = document.getElementById('image-preview-modal');
            const previewImage = document.getElementById('preview-image');
            const imageCaption = document.getElementById('image-caption');
            const closePreviewBtn = document.getElementById('close-preview');
            const sendImageBtn = document.getElementById('send-image-btn');
            const attachFileBtn = document.getElementById('attach-file-btn');
            const fileInput = document.getElementById('file-input');

            // التعامل مع حدث اللصق للصور
            document.addEventListener('paste', function(e) {
                const items = e.clipboardData?.items;
                if (!items) return;

                for (let i = 0; i < items.length; i++) {
                    if (items[i].type.indexOf('image') !== -1) {
                        e.preventDefault();
                        const blob = items[i].getAsFile();
                        showImagePreview(blob);
                    }
                }
            });

            // ========== منطق الإشارات ==========
            const mentionList = document.getElementById('mention-suggestions');
            // الحصول على المشاركين بأمان
            const participants = @json($participants ?? []);

            if (messageInput && mentionList) {
                messageInput.addEventListener('input', function(e) {
                    const val = messageInput.value;
                    const cursor = messageInput.selectionStart;

                    // البحث عن رمز @ قبل المؤشر
                    const lastAt = val.lastIndexOf('@', cursor - 1);

                    if (lastAt !== -1) {
                        const query = val.substring(lastAt + 1, cursor);
                        // البحث فقط إذا لم يحتوي الاستعلام على مسافات
                        if (!/\s/.test(query) || (query.split(' ').length < 3)) {
                            const matches = participants.filter(p => {
                                const name = p.name || '';
                                return name.toLowerCase().includes(query.toLowerCase());
                            });

                            if (matches.length > 0) {
                                showMentions(matches, lastAt, cursor);
                                return;
                            }
                        }
                    }
                    hideMentions();
                });

                // الإخفاء عند النقر خارج
                document.addEventListener('click', function(e) {
                    if (!mentionList.contains(e.target) && e.target !== messageInput) {
                        hideMentions();
                    }
                });

                function hideMentions() {
                    mentionList.style.display = 'none';
                }

                function showMentions(users, start, end) {
                    mentionList.innerHTML = '';
                    users.forEach(u => {
                        const div = document.createElement('div');
                        div.className = 'mention-item';

                        // صورة رمزية مؤقتة
                        const avatar = document.createElement('div');
                        avatar.className = 'mention-avatar';
                        avatar.innerText = u.name.charAt(0).toUpperCase();

                        const nameSpan = document.createElement('span');
                        nameSpan.innerText = u.name;

                        div.appendChild(avatar);
                        div.appendChild(nameSpan);

                        div.onclick = (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            const val = messageInput.value;
                            const before = val.substring(0, start);
                            const after = val.substring(end);

                            // إدراج الاسم مع @
                            const newValue = before + '@' + u.name + ' ' + after;

                            messageInput.value = newValue;

                            // تشغيل تحديث Livewire
                            messageInput.dispatchEvent(new Event('input', { bubbles: true }));

                            hideMentions();
                            messageInput.focus();

                            // نقل المؤشر لنهاية الاسم المُدخل
                            // const newCursorPos = start + u.name.length + 2; // +2 لـ @ والمسافة
                            // messageInput.setSelectionRange(newCursorPos, newCursorPos);
                        };
                        mentionList.appendChild(div);
                    });
                    mentionList.style.display = 'block';
                }
            }

            // التعامل مع تغيير ملف الإدخال
            if (attachFileBtn && fileInput) {
                attachFileBtn.addEventListener('click', function() {
                    fileInput.click();
                });

                fileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file && file.type.startsWith('image/')) {
                        showImagePreview(file);
                    }
                    // إعادة تعيين ملف الإدخال
                    fileInput.value = '';
                });
            }

            // عرض معاينة الصورة
            function showImagePreview(file) {
                currentImageFile = file;
                const reader = new FileReader();

                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewModal.style.display = 'flex';

                    // التركيز على حقل الوصف
                    setTimeout(() => {
                        if (imageCaption) {
                            imageCaption.focus();
                        }
                    }, 300);
                };

                reader.readAsDataURL(file);
            }

            // إغلاق نافذة المعاينة
            if (closePreviewBtn) {
                closePreviewBtn.addEventListener('click', function() {
                    closeImagePreview();
                });
            }

            // الإغلاق عند النقر على الخلفية
            if (previewModal) {
                previewModal.addEventListener('click', function(e) {
                    if (e.target === previewModal || e.target.classList.contains('preview-overlay')) {
                        closeImagePreview();
                    }
                });
            }

            // الإغلاق بمفتاح Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && previewModal.style.display === 'flex') {
                    closeImagePreview();
                }
            });

            function closeImagePreview() {
                previewModal.style.display = 'none';
                previewImage.src = '';
                imageCaption.value = '';
                currentImageFile = null;
            }

            // إرسال الصورة مع الوصف
            if (sendImageBtn) {
                sendImageBtn.addEventListener('click', function() {
                    if (!currentImageFile) return;

                    const caption = imageCaption.value.trim();

                    // عرض حالة التحميل
                    sendImageBtn.disabled = true;
                    sendImageBtn.innerHTML = '<span class="spinner-border spinner-border-sm ms-2"></span>جاري الرفع...';

                    // تحديث الرسالة مع الوصف
                    if(caption) {
                    @this.set('message', caption);
                    }

                    // رفع الملف باستخدام Livewire
                @this.upload('attachment', currentImageFile, (uploadedFilename) => {
                    // النجاح: إرسال الرسالة
                    sendImageBtn.innerHTML = '<span class="spinner-border spinner-border-sm ms-2"></span>جاري الإرسال...';
                @this.sendMessage().then(() => {
                    closeImagePreview();
                    // إعادة تعيين الزر
                    sendImageBtn.disabled = false;
                    sendImageBtn.innerHTML = '<i class="bi bi-send-fill ms-2"></i>إرسال';
                });
                }, (error) => {
                    // خطأ
                    alert('خطأ في رفع الصورة');
                    sendImageBtn.disabled = false;
                    sendImageBtn.innerHTML = '<i class="bi bi-send-fill ms-2"></i>إرسال';
                });
                });

                // إرسال بمفتاح Enter في حقل الوصف
                if (imageCaption) {
                    imageCaption.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' && !e.shiftKey) {
                            e.preventDefault();
                            sendImageBtn.click();
                        }
                    });
                }
            }
        });
    </script>
@endpush
