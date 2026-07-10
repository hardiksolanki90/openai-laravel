<?php

namespace HardikSolanki\OpenAILaravel\Models;

use HardikSolanki\OpenAILaravel\Traits\HasCostTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Conversation extends Model
{
    use HasCostTracking;

    protected $fillable = [
        'team_id', 'user_id', 'api_key_id', 'title', 'description', 'model',
        'system_prompt', 'messages', 'metadata', 'total_tokens',
        'total_cost', 'context_window_used', 'is_archived',
    ];

    protected $casts = [
        'messages' => 'array',
        'metadata' => 'array',
        'total_cost' => 'decimal:6',
        'is_archived' => 'boolean',
    ];

    protected $attributes = [
        'messages' => '[]',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class), 'user_id');
    }

    public function apiKey(): BelongsTo
    {
        return $this->belongsTo(APIKey::class, 'api_key_id');
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class);
    }

    public function addMessage(string $role, string $content, ?array $metadata = null): void
    {
        $messages = $this->messages ?? [];

        $messages[] = array_merge([
            'role' => $role,
            'content' => $content,
            'timestamp' => now()->toIso8601String(),
        ], $metadata ?? []);

        $this->messages = $messages;
        $this->save();
    }
}
