<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'subject',
        'content',
        'target_audience',
        'recipients_count',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'recipients_count' => 'integer',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
