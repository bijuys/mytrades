<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

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
        'avatar',
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

    public function getInitialsAttribute(): string
    {
        $first = ! empty($this->first_name) ? mb_substr(trim($this->first_name), 0, 1) : '';
        $last = ! empty($this->last_name) ? mb_substr(trim($this->last_name), 0, 1) : '';

        if ($first === '' && $last === '' && ! empty($this->name)) {
            $parts = preg_split('/\s+/', trim($this->name));
            $first = mb_substr($parts[0] ?? '', 0, 1);
            $last = isset($parts[1]) ? mb_substr($parts[1], 0, 1) : '';
        }

        $initials = mb_strtoupper($first.$last);

        return $initials ?: 'U';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        if (str_starts_with($this->avatar, 'http://') || str_starts_with($this->avatar, 'https://')) {
            return $this->avatar;
        }

        $disk = config('filesystems.default');
        if ($disk === 's3') {
            try {
                return Storage::disk('s3')->temporaryUrl($this->avatar, now()->addHours(24));
            } catch (\Throwable) {
                return Storage::disk('s3')->url($this->avatar);
            }
        }

        return Storage::disk('public')->url($this->avatar);
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
