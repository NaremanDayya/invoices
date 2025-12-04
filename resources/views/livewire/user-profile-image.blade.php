<div class="position-relative d-inline-block">
    <div class="user-avatar-container" style="position: relative; cursor: pointer;"
         x-data="{ uploading: false, progress: 0 }"
         x-on:livewire-upload-start="uploading = true"
         x-on:livewire-upload-finish="uploading = false"
         x-on:livewire-upload-error="uploading = false"
         x-on:livewire-upload-progress="progress = $event.detail.progress">

        <!-- Image -->
        <img src="{{ $user->personal_image ? asset($user->personal_image) : asset('assets/img/default-avatar.png') }}"
             alt="User Avatar"
             class="user-avatar"
             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255, 255, 255, 0.3);">

        <!-- Overlay with Pencil Icon -->
        <label for="profile-image-upload"
               class="avatar-overlay"
               style="position: absolute; bottom: -5px; right: -5px; background: var(--primary); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;">
            <i class="fas fa-pencil-alt text-white" style="font-size: 10px;"></i>
        </label>

        <!-- Hidden Input -->
        <input type="file" id="profile-image-upload" wire:model="image" class="d-none" accept="image/*">

        <!-- Loading Indicator -->
        <div x-show="uploading" style="position: absolute; inset: 0; background: rgba(0,0,0,0.5); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
            <div class="spinner-border text-white spinner-border-sm" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>
</div>
