<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'email_verified_at',
        'password',
        'phone',
        'trading_experience',
        'agreed_to_terms',
        'role',
        'status',
        'confirmation_token',
        'approved_at',
        'approved_by',
        'rejection_reason',
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
            'agreed_to_terms' => 'boolean',
            'trading_experience' => 'integer',
            'approved_at' => 'datetime',
        ];
    }

    protected static function booted()
    {
        static::saving(function ($user) {
            if (empty($user->name) && (! empty($user->first_name) || ! empty($user->last_name))) {
                $user->name = trim(($user->first_name ?? '').' '.($user->last_name ?? ''));
            }
        });
    }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? '')) ?: ($this->name ?? '');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isUnconfirmed(): bool
    {
        return $this->status === 'unconfirmed' || is_null($this->email_verified_at);
    }

    public function isEmailVerified(): bool
    {
        return ! is_null($this->email_verified_at);
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function trades(): HasMany
    {
        return $this->hasMany(Trade::class);
    }

    public function strategies(): HasMany
    {
        return $this->hasMany(Strategy::class);
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(UserMessage::class, 'admin_id');
    }

    public function receivedMessages(): HasMany
    {
        return $this->hasMany(UserMessage::class, 'user_id');
    }

    public function newsletters(): HasMany
    {
        return $this->hasMany(Newsletter::class, 'admin_id');
    }
}
