<?php

namespace HardikSolanki\OpenAILaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromptCache extends Model
{
    protected $table = 'prompt_caches';

    protected $fillable = [
        'team_id', 'query_hash', 'model', 'response', 'tokens', 'cost',
        'ttl_expires_at', 'hit_count',
    ];

    protected $casts = [
        'response' => 'array',
        'cost' => 'decimal:6',
        'ttl_expires_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function isExpired(): bool
    {
        return $this->ttl_expires_at !== null && $this->ttl_expires_at->isPast();
    }
}
