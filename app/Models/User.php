<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable , HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'personal_image',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function isAdmin()
    {
        return $this->role === 'admin';

    }
    public function receivesBroadcastNotificationsOn(): array
    {
        $channels = [
            'agreement.request.approved.' . $this->id,
            'agreement.request.rejected.' . $this->id,
            'client.request.approved.' . $this->id,
            'client.request.rejected.' . $this->id,
            'target.achieved.' . $this->id,
            'late.customer.' . $this->id,
            'agreement.notice.' . $this->id,

        ];

        if ($this->isAdmin()) {
            $channels[] = [
                'client.request.sent.' . $this->id,
                'agreement.request.sent.' . $this->id,
                'new-client.' . $this->id,
                'new-agreement.' . $this->id,
                'agreement-renewed.' . $this->id,
                'pended-request.notice.'. $this->id,
                'salesrep-login-ip.'. $this->id,
                'birthday'. $this->id,
            ];
        }

        return $channels;
    }

}
