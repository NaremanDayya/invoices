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
            
            $this->user->personal_image = 'storage/' . $path; // Adjust path as needed based on your accessor/storage config
            // Or just $path if you have an accessor. Let's assume direct path for now or 'storage/'.$path if using symlink.
            // Actually, usually it's better to store just the path and use an accessor, but let's stick to what might be standard here.
            // If the existing code uses asset('storage/' . ...), then we should store the relative path.
            
            // Let's check how other images are stored. 
            // In Chat.blade.php: asset('storage/' . $client->company_logo)
            // So we should store just the path relative to public/storage (which is what store() returns).
            // But wait, if I save 'profile-photos/xyz.jpg', then asset('storage/profile-photos/xyz.jpg') works.
            // So I should save 'profile-photos/xyz.jpg' to the DB?
            // Let's look at the master layout: Auth::user()->personal_image
            // It uses asset(Auth::user()->personal_image).
            // This implies the DB contains the FULL path or a path relative to public?
            // If it's asset('assets/img/default-avatar.png'), that's relative to public.
            // So if I use storage, I should probably save 'storage/profile-photos/xyz.jpg' in the DB.
            
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
