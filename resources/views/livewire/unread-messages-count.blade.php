<a href="{{ route('chat.index') }}" class="icon-btn text-decoration-none">
    <i class="fas fa-comment-alt"></i> <!-- Changed icon to represent chat better -->
    @if($count > 0)
        <span class="notification-badge" style="background-color: #ef4444;">
            {{ $count > 99 ? '99+' : $count }}
        </span>
    @endif
</a>
