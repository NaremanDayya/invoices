<!-- Client Selection Modal -->
<div class="modal fade" id="clientSelectionModal" tabindex="-1" aria-labelledby="clientSelectionModalLabel" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0" style="background: linear-gradient(135deg, #2d5f5d 0%, #3d7a76 100%); border-radius: 15px 15px 0 0;">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                <h5 class="modal-title fw-bold text-white" id="clientSelectionModalLabel">
                    <i class="bi bi-people-fill me-2"></i>
                    بدء محادثة جديدة
                </h5>
            </div>
            <div class="modal-body p-4">
                <!-- Search Input -->
                <div class="input-group mb-4">
                    <input type="text"
                           class="form-control border-end-0"
                           placeholder="ابحث عن عميل بالاسم أو البريد الإلكتروني..."
                           wire:model.live.debounce.300ms="clientSearch"
                           style="border-radius: 10px 0 0 10px;">
                    <span class="input-group-text bg-white border-start-0" style="border-radius: 0 10px 10px 0;">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                </div>

                <!-- Results List -->
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($this->suggestedClients as $client)
                        <button type="button"
                                class="list-group-item list-group-item-action d-flex align-items-center p-3 border-bottom hover-item"
                                wire:click="startChat({{ $client->id }})"
                                data-bs-dismiss="modal"
                                style="transition: all 0.2s; cursor: pointer;">
                            <div class="ms-auto text-success">
                                <i class="bi bi-chat-dots-fill fs-5"></i>
                            </div>
                            <div class="text-end flex-grow-1">
                                <h6 class="mb-1 fw-bold text-dark">{{ $client->name }}</h6>
                                <small class="text-muted d-block">
                                    <i class="bi bi-envelope ms-1"></i> {{ $client->email }}
                                </small>
                            </div>
                            <div class="ms-3">
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold"
                                     style="width: 50px; height: 50px; font-size: 1.2rem; background: linear-gradient(135deg, #2d5f5d, #3d7a76);">
                                    {{ mb_substr($client->name, 0, 2) }}
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="text-center py-5">
                            @if(strlen($clientSearch ?? '') > 0)
                                <i class="bi bi-search display-6 text-muted mb-3 d-block"></i>
                                <p class="text-muted">لم يتم العثور على عملاء مطابقين لـ "{{ $clientSearch }}"</p>
                            @else
                                <i class="bi bi-people display-6 text-muted mb-3 d-block"></i>
                                <p class="text-muted">ابدأ بالكتابة للبحث عن العملاء</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-item:hover {
        background-color: #f0fdf4 !important;
        transform: translateX(-5px);
    }
    
    #clientSelectionModal .list-group-item {
        border-radius: 10px !important;
        margin-bottom: 8px;
    }
    
    #clientSelectionModal .modal-content {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }
</style>
