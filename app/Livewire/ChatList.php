<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class ChatList extends Component
{
    public $client;
    public $selectedConversation;
    public $search = '';
    public $clientSearch = ''; // Search for new chat modal
    public $perPage = 20;
    public $hasMore = true;
    public $loading = false;
    public $page = 1;
    public $allConversations; // This will store Conversation objects
    private $lastBatchHasMore = false; // track availability of more data based on server fetch
    // Filter options: unread, read, oldest, newest
    public $filter = 'newest';
    // Chat type filter: all, private, invoice
    public $chatType = 'all';

    protected $listeners = [
        'conversationUpdated' => 'handleConversationUpdate',
        'newMessageReceived' => 'handleNewMessage',
        'refreshChatList' => 'refresh',
    ];

    public function getListeners()
    {
        $authId = Auth::id();
        return [
            'conversationUpdated'  => 'handleConversationUpdate',
            'newMessageReceived'   => 'handleNewMessage',
            'refreshChatList'      => 'refresh',
            "echo-private:chat.{$authId},Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'refresh',
        ];
    }

    public function mount($client = null)
    {
        $this->client = $client;
        $this->loadInitialConversations();
    }

    public function loadInitialConversations()
    {
        $this->allConversations = $this->getConversations(1);
        // hasMore is determined by server-side over-fetching
        $this->hasMore = $this->lastBatchHasMore;
    }

    public function loadMore()
    {
        if ($this->loading || !$this->hasMore) {
            return;
        }

        $this->loading = true;
        $this->page++;

        $additionalConversations = $this->getConversations($this->page);

        // hasMore is determined by server-side over-fetching
        $this->hasMore = $this->lastBatchHasMore;

        // Merge new conversations with existing ones (dedupe by id, keep as Collection)
        $this->allConversations = $this->allConversations
            ->concat($additionalConversations)
            ->unique('id')
            ->values();
        $this->loading = false;
    }

    public function handleConversationUpdate($conversationId)
    {
        // Refresh the entire list to get updated data
        $this->refresh();
    }

    public function handleNewMessage($data)
    {
        // Refresh to show new messages and updated order
        $this->refresh();
    }

    public function refresh()
    {
        $this->page = 1;
        $this->hasMore = true;
        $this->loading = false;
        $this->loadInitialConversations();
    }

    public function updatedFilter()
    {
        // Reset pagination and reload when filter changes
        $this->refresh();
    }

    public function updatedChatType()
    {
        $this->refresh();
    }

    public function getSuggestedClientsProperty()
    {
        return Client::query()
            ->when($this->clientSearch, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%' . $this->clientSearch . '%')
                        ->orWhere('email', 'like', '%' . $this->clientSearch . '%');
                });
            })
            ->latest()
            ->take(20)
            ->get();
    }

    public function startChat($clientId)
    {
        $user = Auth::user();

        $existing = Conversation::where('client_id', $clientId)
            ->where('type', 'private')
            ->whereHas('users', function($q) use ($user) {
                $q->where('users.id', $user->id);
            })
            ->first();

        if ($existing) {
            $this->dispatch('selectConversation', ['id' => $existing->id]);
            return;
        }

        // Create new conversation
        $client = Client::find($clientId);
        if (!$client) return;

        $receiverId = $client->sales_rep_id;
        /* Fail-safe if needed */

        $newConversation = Conversation::create([
            'id' => (string)Str::uuid(),
            'sender_id' => $user->id,
            'receiver_id' => $receiverId ?? $user->id,
            'client_id' => $clientId,
            'type' => 'private'
        ]);

        // Add ALL system users to the conversation as requested
        $allUserIds = \App\Models\User::pluck('id')->toArray();
        // Ensure no duplicates and valid IDs
        $participants = array_unique($allUserIds);

        $newConversation->users()->sync($participants);

        return redirect()->route('client.chat', [
            'client' => $clientId,
            'conversation' => $newConversation->id
        ]);
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversation = $conversationId;
        $this->dispatch('conversationSelected', id: $conversationId);
    }

    private function getConversations($page = 1)
    {
        $user = Auth::user();
        $offset = ($page - 1) * $this->perPage;

        $query = Conversation::with([
            'client:id,name',
            'users',
            'invoice:id,number,total_price,payment_status'
        ])
            ->whereHas('users', function($q) use ($user) {
                 $q->where('users.id', $user->id);
            })
            ->when($this->chatType !== 'all', function ($query) {
                $query->where('type', $this->chatType);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            });

        // Add latest message timestamp
        $query->addSelect([
            'conversations.*',
            'latest_message_created_at' => \DB::table('messages')
                ->selectRaw('MAX(created_at)')
                ->whereColumn('conversation_id', 'conversations.id')
        ]);

        // Add unread count per conversation: messages sent by others, not yet read by this user
        $query->addSelect([
            'unread_count' => \DB::table('messages')
                ->join('chat_receivers', 'messages.id', '=', 'chat_receivers.message_id')
                ->whereColumn('messages.conversation_id', 'conversations.id')
                ->where('chat_receivers.receiver_id', $user->id)
                ->where('chat_receivers.is_read', false)
                ->where('messages.sender_id', '!=', $user->id)
                ->selectRaw('COUNT(*)')
        ]);

        // Apply filter
        if ($this->filter === 'unread') {
            $query->whereExists(function($q) use ($user) {
                $q->select(\DB::raw(1))
                    ->from('messages')
                    ->join('chat_receivers', 'messages.id', '=', 'chat_receivers.message_id')
                    ->whereColumn('messages.conversation_id', 'conversations.id')
                    ->where('chat_receivers.receiver_id', $user->id)
                    ->where('chat_receivers.is_read', false)
                    ->where('messages.sender_id', '!=', $user->id);
            });
        }

        // Apply ordering: unread first, then by latest message
        if ($this->filter === 'oldest') {
            $query->orderByRaw('unread_count DESC')
                ->orderBy('latest_message_created_at', 'asc')
                ->orderBy('created_at', 'asc');
        } else {
            $query->orderByRaw('unread_count DESC')
                ->orderBy('latest_message_created_at', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $conversations = $query
            ->skip($offset)
            ->take($this->perPage + 1)
            ->get();

        // Determine hasMore for this batch and trim to perPage
        $this->lastBatchHasMore = $conversations->count() > $this->perPage;
        if ($this->lastBatchHasMore) {
            $conversations = $conversations->slice(0, $this->perPage)->values();
        }

        return $this->enhanceConversationsWithMessages($conversations, $user);
    }

    private function enhanceConversationsWithMessages($conversations, $user = null)
    {
        if ($conversations->isEmpty()) {
            return $conversations;
        }

        if (!$user) {
            $user = Auth::user();
        }

        $conversationIds = $conversations->pluck('id');

        // ── 1. Latest message per conversation (single bulk query) ──────────────
        $latestMessages = Message::whereIn('conversation_id', $conversationIds)
            ->whereIn('id', function ($q) use ($conversationIds) {
                $q->select(DB::raw('MAX(id)'))
                    ->from('messages')
                    ->whereIn('conversation_id', $conversationIds)
                    ->groupBy('conversation_id');
            })
            ->select('id', 'conversation_id', 'message', 'created_at', 'sender_id')
            ->get()
            ->keyBy('conversation_id');

        // ── 2. Unread counts per conversation (single bulk query) ───────────────
        // Excludes messages sent by the auth user — only counts messages from others.
        $unreadPerConversation = \DB::table('messages as m')
            ->join('chat_receivers as cr', function ($join) use ($user) {
                $join->on('cr.message_id', '=', 'm.id')
                     ->where('cr.receiver_id', '=', $user->id)
                     ->where('cr.is_read', '=', false);
            })
            ->whereIn('m.conversation_id', $conversationIds)
            ->where('m.sender_id', '!=', $user->id)
            ->selectRaw('m.conversation_id, COUNT(*) as unread_count')
            ->groupBy('m.conversation_id')
            ->pluck('unread_count', 'conversation_id');

        // ── 3. Invoice sub-chat unread counts per client (for private chats) ────
        // One extra query only when private conversations are present.
        $clientIds = $conversations
            ->where('type', 'private')
            ->pluck('client_id')
            ->filter()
            ->unique()
            ->values();

        $invoiceUnreadByClient = collect();
        if ($clientIds->isNotEmpty()) {
            $invoiceUnreadByClient = \DB::table('conversations as c')
                ->join('messages as m', 'm.conversation_id', '=', 'c.id')
                ->join('chat_receivers as cr', function ($join) use ($user) {
                    $join->on('cr.message_id', '=', 'm.id')
                         ->where('cr.receiver_id', '=', $user->id)
                         ->where('cr.is_read', '=', false);
                })
                ->whereIn('c.client_id', $clientIds)
                ->where('c.type', 'invoice')
                ->whereNotNull('c.invoice_id')
                ->where('m.sender_id', '!=', $user->id)
                ->selectRaw('c.client_id, COUNT(*) as invoice_unread_count')
                ->groupBy('c.client_id')
                ->pluck('invoice_unread_count', 'client_id');
        }

        // ── 4. Decorate each conversation ───────────────────────────────────────
        $conversations->each(function ($conversation) use ($latestMessages, $unreadPerConversation, $invoiceUnreadByClient) {
            $latestMessage = $latestMessages->get($conversation->id);

            $conversation->latest_message_text      = $latestMessage?->message ?? '';
            $conversation->latest_message_time      = $latestMessage?->created_at;
            $conversation->latest_message_sender_id = $latestMessage?->sender_id;

            if ($conversation->type === 'invoice') {
                // Invoice chat: unread = messages in this conversation not read by user
                $own = (int) ($unreadPerConversation->get($conversation->id) ?? 0);
                $conversation->unread_count         = $own;
                $conversation->private_unread_count = 0;
                $conversation->invoice_unread_count = $own;
            } else {
                // Private chat: own private unread + unread across all invoice sub-chats for this client
                $own     = (int) ($unreadPerConversation->get($conversation->id) ?? 0);
                $invoice = (int) ($invoiceUnreadByClient->get($conversation->client_id) ?? 0);
                $conversation->unread_count         = $own + $invoice;
                $conversation->private_unread_count = $own;
                $conversation->invoice_unread_count = $invoice;
            }
        });

        return $conversations;
    }
    public function updatedSearch()
    {
        $this->page = 1;
        $this->hasMore = true;
        $this->allConversations = $this->getConversations(1);
    }

    public function render()
    {
        if (!$this->allConversations) {
            $this->allConversations = $this->getConversations($this->page);
        }
        dd($this->allConversations);

        return view('livewire.chat-list', [
            'conversations' => $this->allConversations ?? collect(),
        ]);
    }
}
