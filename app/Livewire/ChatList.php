<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
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

    protected $listeners = [
        'conversationUpdated' => 'handleConversationUpdate',
        'newMessageReceived' => 'handleNewMessage',
        'refreshChatList' => 'refresh',
    ];

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

        // Check for existing conversation
        $existing = Conversation::where('client_id', $clientId)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->first();

        if ($existing) {
            $this->dispatch('selectConversation', ['id' => $existing->id]);
            return;
        }

        // Create new conversation
        $client = Client::find($clientId);
        if (!$client) return;

        // Determine receiver (default to sales rep, or fallback to Admin/Self if strictly necessary, but preferably sales rep)
        // If sales_rep_id is null or same as sender, we might need a fallback.
        // For now, let's use sales_rep_id.
        $receiverId = $client->sales_rep_id;

        // If no receiver found (e.g. client has no rep), maybe use the first admin found?
        // Or keep it null if DB allows. Assuming DB allows or logic handles it.
        // Let's use a fail-safe: if no rep, and I am not ID 1, use ID 1 (Super Admin).
        if (!$receiverId) {
            // Fallback logic could be complex. Let's assume sales_rep_id is valid or nullable.
            // If receiver_id is NOT nullable in DB, this will fail.
            // Let's check conversation model fillable.
        }

        $newConversation = Conversation::create([
            'id' => (string)Str::uuid(),
            'sender_id' => $user->id,
            'receiver_id' => $receiverId ?? $user->id, // Fallback to avoid SQL error if not nullable
            'client_id' => $clientId,
        ]);

        $this->refresh();
        $this->dispatch('selectConversation', ['id' => $newConversation->id]);
    }

    private function getConversations($page = 1)
    {
        $user = Auth::user();
        $offset = ($page - 1) * $this->perPage;

        $query = Conversation::with([
            'client:id,name',
            'client.invoices',
            'sender:id,name', // Added: Ensure sender is loaded for getReceiver()
            'receiver:id,name', // Added: Ensure receiver is loaded for getReceiver()
        ])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('receiver_id', $user->id);
            })
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                }); // Missing closing parenthesis and semicolon
            })
            ->addSelect([
                'latest_message_created_at' => Message::selectRaw('MAX(created_at)')
                    ->whereColumn('conversation_id', 'conversations.id')
            ]);

        // Add unread messages count for filtering/sorting
        $query->withCount(['messages as unread_count' => function ($sub) use ($user) {
            $sub->whereNull('read_at')
                ->where('sender_id', '!=', $user->id);
        }]);

        // Apply filter conditions
        $query->when($this->filter === 'unread', function ($q) {
            $q->having('unread_count', '>', 0);
        });

        $query->when($this->filter === 'read', function ($q) {
            $q->having('unread_count', '=', 0);
        });

        // Apply ordering using latest message timestamp
        if ($this->filter === 'oldest') {
            // Oldest conversations first by latest message time
            $query->orderBy('unread_count', 'desc')
                ->orderBy('latest_message_created_at', 'asc')
                ->orderBy('created_at', 'asc');
        } else {
            // Default/newest + other filters: unread first then latest message time desc
            $query->orderBy('unread_count', 'desc')
                ->orderBy('latest_message_created_at', 'desc')
                ->orderBy('created_at', 'desc');
        }

        $conversations = $query
            ->skip($offset)
            // Fetch one extra record to determine if there are more without another query
            ->take($this->perPage + 1)
            ->get();

        // Determine hasMore for this batch and trim to perPage
        $this->lastBatchHasMore = $conversations->count() > $this->perPage;
        if ($this->lastBatchHasMore) {
            $conversations = $conversations->slice(0, $this->perPage)->values();
        }

        return $this->enhanceConversationsWithMessages($conversations);
    }

    private function enhanceConversationsWithMessages($conversations)
    {
        if ($conversations->isEmpty()) {
            return $conversations;
        }

        $conversationIds = $conversations->pluck('id');

        // Get latest messages
        $latestMessages = Message::whereIn('conversation_id', $conversationIds)
            ->whereIn('id', function ($query) use ($conversationIds) {
                $query->select(\DB::raw('MAX(id)'))
                    ->from('messages')
                    ->whereIn('conversation_id', $conversationIds)
                    ->groupBy('conversation_id');
            })
            ->select('id', 'conversation_id', 'message', 'created_at', 'sender_id')
            ->get()
            ->keyBy('conversation_id');

        // Map the collection to add data
        $conversations->each(function ($conversation) use ($latestMessages) {
            $latestMessage = $latestMessages->get($conversation->id);

            // Calculate attributes
            $conversation->latest_message_text = $latestMessage ? $latestMessage->message : '';
            $conversation->latest_message_time = $latestMessage ? $latestMessage->created_at : null;
            $conversation->latest_message_sender_id = $latestMessage ? $latestMessage->sender_id : null;
            $conversation->receiver_name = $conversation->getReceiver()->name ?? 'Unknown';
            $conversation->is_last_message_read = $conversation->isLastMessageReadByUser();

            if (!isset($conversation->unread_count)) {
                $conversation->unread_count = $conversation->unreadMessagesCount();
            }
        });

        return $conversations;
    }
    public function updatedSearch()
    {
        $this->resetPage();
        $this->allConversations = $this->getConversations(1);
    }

    public function render()
    {
        if (!$this->allConversations) {
            $this->allConversations = $this->getConversations($this->page);
        }

        return view('livewire.chat-list', [
            'conversations' => $this->allConversations ?? collect(),
        ]);
    }
}
