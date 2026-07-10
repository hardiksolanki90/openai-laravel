<?php

namespace HardikSolanki\OpenAILaravel\Models;

use HardikSolanki\OpenAILaravel\Utilities\PromptInterpolator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PromptTemplate extends Model
{
    protected $fillable = [
        'team_id', 'name', 'slug', 'description', 'content',
        'variables', 'model', 'is_public', 'created_by',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_public' => 'boolean',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class), 'created_by');
    }

    public function interpolate(array $data): string
    {
        return app(PromptInterpolator::class)->interpolate($this->content, $data);
    }

    public static function findBySlug(string $slug, ?int $teamId = null): ?self
    {
        return static::when($teamId, fn ($q) => $q->where('team_id', $teamId))
            ->where('slug', $slug)
            ->first();
    }
}
