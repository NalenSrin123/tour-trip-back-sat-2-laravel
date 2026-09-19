<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';


    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
        'full_name',
        'email',
        'password',
        'password_hash',
        'role',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'password_hash',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (User $user) {
            if ($user->isDirty('name') && !$user->isDirty('full_name')) {
                $user->full_name = $user->name;
            } elseif ($user->isDirty('full_name') && !$user->isDirty('name')) {
                $user->name = $user->full_name;
            }

            if ($user->isDirty('password') && !$user->isDirty('password_hash')) {
                $user->password_hash = $user->password;
            } elseif ($user->isDirty('password_hash') && !$user->isDirty('password')) {
                $user->password = $user->password_hash;
            }
        });
    }

    public function getAuthPassword()
    {
        return $this->password ?? $this->password_hash;
    }

    public function getUserIdAttribute()
    {
        return $this->attributes['id'] ?? null;
    }
}
