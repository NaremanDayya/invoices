<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'personal_image',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function assignRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        if (!$this->roles->contains($role)) {
            $this->roles()->attach($role);
        }
    }

    public function removeRole(Role|string $role): void
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        $this->roles()->detach($role);
    }

    public function hasPermission(string $permissionName): bool
    {
        foreach ($this->roles as $role) {
            if ($role->isAdmin()) {
                return true;
            }
            if ($role->hasPermission($permissionName)) {
                return true;
            }
        }
        return false;
    }

    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function getRole(): ?Role
    {
        return $this->roles()->first();
    }

    public function getRoleName(): ?string
    {
        $role = $this->getRole();
        return $role ? $role->display_name : null;
    }

    public function receivesBroadcastNotificationsOn(): array
    {
        $channels = [
            'chat.' . $this->id,
        ];

        return $channels;
    }
}
