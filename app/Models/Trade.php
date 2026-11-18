<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'strategy_id',
        'strategy_name',
        'instrument',
        'direction',
        'entry_datetime',
        'exit_datetime',
        'quantity',
        'entry_price',
        'exit_price',
        'stop_loss',
        'target',
        'exit_reason',
        'gross_pnl',
        'charges',
        'net_pnl',
        'screenshot',
        'lessons',
        'remarks',
        'journal_datetime',
        'trade_status',
    ];

    protected function casts(): array
    {
        return [
            'entry_datetime' => 'datetime',
            'exit_datetime' => 'datetime',
            'journal_datetime' => 'datetime',
            'quantity' => 'decimal:4',
            'entry_price' => 'decimal:2',
            'exit_price' => 'decimal:2',
            'stop_loss' => 'decimal:2',
            'target' => 'decimal:2',
            'gross_pnl' => 'decimal:2',
            'charges' => 'decimal:2',
            'net_pnl' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function strategy(): BelongsTo
    {
        return $this->belongsTo(Strategy::class);
    }

    public function isWin(): bool
    {
        return (float) $this->net_pnl > 0;
    }

    public function isLoss(): bool
    {
        return (float) $this->net_pnl < 0;
    }

    public function isBreakEven(): bool
    {
        return (float) $this->net_pnl == 0;
    }

    public function getScreenshotUrlAttribute(): ?string
    {
        if (! $this->screenshot) {
            return null;
        }

        return asset('storage/'.$this->screenshot);
    }

    public function getDisplayStrategyAttribute(): string
    {
        return $this->strategy?->name ?? $this->strategy_name ?? 'Default Strategy';
    }

    public function getRoiAttribute(): float
    {
        $capital = (float) $this->entry_price * (float) $this->quantity;
        if ($capital <= 0) {
            return 0.0;
        }

        return round(((float) $this->net_pnl / $capital) * 100, 2);
    }
}
