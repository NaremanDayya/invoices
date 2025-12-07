<!-- Client Selection Modal -->
<div class="modal fade" id="clientSelectionModal" tabindex="-1" aria-labelledby="clientSelectionModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold" id="clientSelectionModalLabel">
                    <i class="bi bi-people-fill text-success me-2"></i>
                    Start New Conversation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Search Input -->
                <div class="input-group mb-4">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0"
                           placeholder="Search clients by name, company, or email..."
                           wire:model.live.debounce.300ms="clientSearch">
                </div>

                <!-- Results List -->
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($this->suggestedClients as $client)
                        <button type="button"
                                class="list-group-item list-group-item-action d-flex align-items-center p-3 border-bottom"
                                wire:click="startChat({{ $client->id }})"
                                data-bs-dismiss="modal">
                            <div class="me-3">
                                @if($client->company_logo)
                                    <img src="{{ asset('storage/' . $client->company_logo) }}"
                                         class="rounded-circle border"
                                         width="50" height="50"
                                         style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold"
                                         style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        {{ substr($client->company_name ?? $client->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-1 fw-bold text-dark">{{ $client->company_name ?? $client->name }}</h6>
                                <small class="text-muted d-block">
                                    <i class="bi bi-envelope me-1"></i> {{ $client->email }}
                                </small>
                            </div>
                            <div class="ms-auto text-success">
                                <i class="bi bi-chat-dots-fill fs-5"></i>
                            </div>
                        </button>
                    @empty
                        <div class="text-center py-5">
                            @if(strlen($clientSearch) > 0)
                                <i class="bi bi-search display-6 text-muted mb-3"></i>
                                <p class="text-muted">No clients found matching "{{ $clientSearch }}"</p>
                            @else
                                <i class="bi bi-people display-6 text-muted mb-3"></i>
                                <p class="text-muted">Type to search for clients</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
