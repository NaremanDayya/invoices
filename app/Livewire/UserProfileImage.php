<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserProfileImage extends Component
{
    use WithFileUploads;

    public $image;
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
        // Remove dd from mount() as it only runs on initial load
        // dd($this->user , $this->user->personal_image);
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:1024',
        ]);

        if ($this->image) {
            // Debug: Show temporary upload path
            dd('Temporary image path:', $this->image->getRealPath(),
                'Original name:', $this->image->getClientOriginalName(),
                'File size:', $this->image->getSize(),
                'MIME type:', $this->image->getMimeType());

            if ($this->user->personal_image && Storage::disk('public')->exists($this->user->personal_image)) {
                Storage::disk('public')->delete($this->user->personal_image);
            }

            $path = $this->image->store('profile-photos', 'public');

            // Debug: Show storage path
            dd('Storage path:', $path,
                'Full path with storage prefix:', 'storage/' . $path,
                'Current user:', $this->user->id,
                'Current personal_image:', $this->user->personal_image);

            $this->user->personal_image = 'storage/' . $path;
            $this->user->save();

            $this->dispatch('profile-updated');
            session()->flash('success', 'تم تحديث الصورة الشخصية بنجاح');
        }
    }

    public function render()
    {
        return view('livewire.user-profile-image');
    }
}
