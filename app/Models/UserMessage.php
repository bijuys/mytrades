<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'subject',
        'message',
        'sent_email',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_email' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
