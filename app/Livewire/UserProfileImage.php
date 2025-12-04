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
    }

    public function updatedImage()
    {
        $this->validate([
            'image' => 'image|max:1024', // 1MB Max
        ]);

        if ($this->image) {
            // Delete old image if exists and is not default
            if ($this->user->personal_image && Storage::disk('public')->exists($this->user->personal_image)) {
                Storage::disk('public')->delete($this->user->personal_image);
            }

            $path = $this->image->store('profile-photos', 'public');
            
            $this->user->personal_image = 'storage/' . $path; 
      
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
