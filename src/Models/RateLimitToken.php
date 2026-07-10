<?php

namespace HardikSolanki\OpenAILaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateLimitToken extends Model
{
    protected $fillable = [
        'team_id', 'api_key_id', 'tokens_remaining', 'refill_at', 'window_size', 'max_tokens',
    ];

    protected $casts = [
        'refill_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(APIKey::class, 'api_key_id');
    }
}
