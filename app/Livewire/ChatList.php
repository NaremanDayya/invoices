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

        // Check for existing conversation via participants
        // This is slightly more complex, but for 1-to-1 with client, we ideally check if there is a private conversation between Auth users and target.
        // For simplicity, let's keep checking legacy sender/receiver or rely on the fact that 1-on-1s are unique per pair + client.
        
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
        
        // Attach participants
        $newConversation->users()->attach([
            $user->id,
            $receiverId ?? $user->id 
        ]);

        $this->refresh();
        $this->dispatch('conversationSelected', id: $newConversation->id);
    }

    public function selectConversation($conversationId)
    {
        $this->selectedConversation = $conversationId; // Optional: mark as selected in list
        $this->dispatch('conversationSelected', id: $conversationId);
    }

    private function getConversations($page = 1)
    {
        $user = Auth::user();
        $offset = ($page - 1) * $this->perPage;

        $query = Conversation::with([
            'client:id,name',
            'client.invoices',
            // 'sender:id,name', // Less critical now
            'users' // eager load participants
        ])
            ->whereHas('users', function($q) use ($user) {
                 $q->where('users.id', $user->id);
            })
            // Filter out invoice conversations as requested
            ->where(function($q) {
                $q->whereNull('invoice_id')
                  ->orWhere('type', '!=', 'invoice');
            })
            ->when($this->search, function ($query) {
                $query->whereHas('client', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                }); 
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
            
            if ($conversation->type === 'group') {
                $conversation->receiver_name = $conversation->label ?? 'Group Chat';
            } else {
                 $conversation->receiver_name = $conversation->getReceiver()->name ?? 'Unknown';
            }
            
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
