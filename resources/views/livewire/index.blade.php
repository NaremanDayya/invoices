<div class="invoices-chat-dashboard">
    <!-- Welcome Header -->
    <div class="welcome-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold text-dark mb-2">
                    <i class="bi bi-chat-left-dots-fill text-success me-3"></i>
                    Invoice Communications
                </h1>
                <p class="text-muted mb-0">
                    Manage all invoice-related conversations with your clients in one place.
                    Track payments, resolve queries, and maintain clear communication.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="stats-badge bg-success text-white p-3 rounded-3 d-inline-block">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-chat-square-text-fill fs-1 me-3"></i>
                        <div>
                            <h3 class="mb-0">{{ \App\Models\Conversation::count() }}</h3>
                            <small>Active Conversations</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chat Layout -->
    <div class="chat-layout">
        <!-- Sidebar - Chat List -->
        <div class="chat-sidebar">
            <livewire:chat-list />
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            @if($selectedConversation)
                <livewire:chat :conversation="$selectedConversation" :client="$selectedConversation->client" :key="$selectedConversation->id" />
            @else
                <!-- Default State (When no chat selected) -->
                <div class="chat-default-state">
                    <div class="text-center py-5">
                        <div class="default-state-icon mb-4">
                            <i class="bi bi-chat-square-text display-1 text-success"></i>
                        </div>
                        <h3 class="mb-3 text-dark">Select a Conversation</h3>
                        <p class="text-muted mb-4">
                            Choose a client from the sidebar to start discussing invoices,
                            payments, and billing queries.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>

@push('styles')
    <style>
        .invoices-chat-dashboard {
            min-height: calc(100vh - 100px);
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .welcome-header {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 5px solid #198754;
        }

        .stats-badge {
            box-shadow: 0 6px 15px rgba(25, 135, 84, 0.3);
            transition: transform 0.3s;
        }

        .stats-badge:hover {
            transform: translateY(-5px);
        }

        .chat-layout {
            display: flex;
            height: 70vh;
            gap: 20px;
            margin-top: 20px;
        }

        .chat-sidebar {
            width: 380px;
            flex-shrink: 0;
        }

        .chat-main {
            flex: 1;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .chat-default-state {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }

        .default-state-icon {
            width: 120px;
            height: 120px;
            background: rgba(25, 135, 84, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
        }

        .feature-card {
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #e9ecef;
            text-align: center;
            transition: all 0.3s;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #20c997;
        }

        .quick-actions-footer {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .quick-actions-footer .btn {
            padding: 12px;
            border-radius: 10px;
            transition: all 0.3s;
        }

        .quick-actions-footer .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Animation for new messages */
        @keyframes highlight {
            0% { background-color: rgba(25, 135, 84, 0.1); }
            100% { background-color: transparent; }
        }

        .new-message {
            animation: highlight 2s ease-out;
        }

        /* Responsive */
        @media (max-width: 992px) {
            .chat-layout {
                flex-direction: column;
                height: auto;
            }

            .chat-sidebar {
                width: 100%;
                margin-bottom: 20px;
            }

            .welcome-header .row {
                flex-direction: column;
                text-align: center;
            }

            .stats-badge {
                margin-top: 20px;
            }
        }

        @media (max-width: 768px) {
            .invoices-chat-dashboard {
                padding: 10px;
            }

            .feature-card {
                margin-bottom: 15px;
            }

            .quick-actions-footer .row > div {
                margin-bottom: 10px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for chat selection
            window.addEventListener('selectConversation', function(event) {
                const chatArea = document.querySelector('.chat-active-area');
                const defaultState = document.querySelector('.chat-default-state');

                if (chatArea && defaultState) {
                    defaultState.style.display = 'none';
                    chatArea.style.display = 'block';

                    // Add highlight effect
                    chatArea.classList.add('new-message');
                    setTimeout(() => {
                        chatArea.classList.remove('new-message');
                    }, 2000);
                }
            });

            // Handle back to list
            window.addEventListener('back-to-invoices', function() {
                const chatArea = document.querySelector('.chat-active-area');
                const defaultState = document.querySelector('.chat-default-state');

                if (chatArea && defaultState) {
                    chatArea.style.display = 'none';
                    defaultState.style.display = 'block';
                }
            });
        });
    </script>
@endpush
