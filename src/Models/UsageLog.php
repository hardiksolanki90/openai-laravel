<?php

namespace HardikSolanki\OpenAILaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UsageLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'team_id', 'conversation_id', 'user_id', 'api_key_id', 'model',
        'prompt_tokens', 'completion_tokens', 'total_tokens', 'cost',
        'status', 'error_message', 'metadata',
    ];

    protected $casts = [
        'cost' => 'decimal:6',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $log) {
            $log->created_at ??= now();
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class), 'user_id');
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(APIKey::class, 'api_key_id');
    }
}
