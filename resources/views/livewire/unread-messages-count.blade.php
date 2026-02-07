<a href="{{ route('chat.index') }}" class="action-icon position-relative text-decoration-none">
    <i class="bi bi-chat-dots-fill"></i>
    @if($count > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25rem 0.4rem;">
            {{ $count > 99 ? '99+' : $count }}
            <span class="visually-hidden">رسائل غير مقروءة</span>
        </span>
    @endif
</a>

