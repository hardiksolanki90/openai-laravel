<?php

namespace HardikSolanki\OpenAILaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetLimit extends Model
{
    protected $fillable = [
        'team_id', 'monthly_limit', 'current_spend', 'warning_threshold',
        'is_active', 'block_on_limit', 'month_starts_at', 'notified_at', 'reset_at',
    ];

    protected $casts = [
        'monthly_limit' => 'decimal:6',
        'current_spend' => 'decimal:6',
        'warning_threshold' => 'decimal:6',
        'is_active' => 'boolean',
        'block_on_limit' => 'boolean',
        'notified_at' => 'datetime',
        'reset_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function isExceeded(): bool
    {
        return $this->is_active && (float) $this->current_spend >= (float) $this->monthly_limit;
    }

    public function isWarning(): bool
    {
        return $this->is_active && (float) $this->current_spend >= (float) $this->warning_threshold;
    }

    public function remainingBudget(): float
    {
        return max(0, (float) $this->monthly_limit - (float) $this->current_spend);
    }

    public function percentageUsed(): float
    {
        if ((float) $this->monthly_limit <= 0) {
            return 0.0;
        }

        return round(((float) $this->current_spend / (float) $this->monthly_limit) * 100, 2);
    }
}
