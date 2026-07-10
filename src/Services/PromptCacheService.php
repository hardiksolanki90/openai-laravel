<?php

namespace HardikSolanki\OpenAILaravel\Services;

use HardikSolanki\OpenAILaravel\Contracts\CacheStrategyContract;
use HardikSolanki\OpenAILaravel\Models\PromptCache;
use HardikSolanki\OpenAILaravel\Utilities\QueryHasher;

class PromptCacheService implements CacheStrategyContract
{
    public function __construct(protected QueryHasher $hasher)
    {
    }

    public function get(int $teamId, string $model, array $messages): ?PromptCache
    {
        $hash = $this->hasher->hash($model, $messages);

        $cached = PromptCache::where('team_id', $teamId)
            ->where('query_hash', $hash)
            ->where(function ($q) {
                $q->whereNull('ttl_expires_at')->orWhere('ttl_expires_at', '>', now());
            })
            ->first();

        $cached?->increment('hit_count');

        return $cached;
    }

    public function set(
        int $teamId,
        string $model,
        array $messages,
        mixed $response,
        int $tokens,
        float $cost,
        ?int $ttlSeconds = 3600
    ): PromptCache {
        $hash = $this->hasher->hash($model, $messages);

        return PromptCache::updateOrCreate(
            ['team_id' => $teamId, 'query_hash' => $hash],
            [
                'model' => $model,
                'response' => $response,
                'tokens' => $tokens,
                'cost' => $cost,
                'ttl_expires_at' => $ttlSeconds ? now()->addSeconds($ttlSeconds) : null,
            ]
        );
    }

    public function invalidate(int $teamId, string $model): void
    {
        PromptCache::where('team_id', $teamId)->where('model', $model)->delete();
    }

    public function clearExpired(): int
    {
        return PromptCache::whereNotNull('ttl_expires_at')->where('ttl_expires_at', '<=', now())->delete();
    }

    public function getHitRate(int $teamId): float
    {
        $caches = PromptCache::where('team_id', $teamId)->get(['hit_count']);

        if ($caches->isEmpty()) {
            return 0.0;
        }

        $totalHits = $caches->sum('hit_count');
        $totalEntries = $caches->count();

        return round($totalHits / $totalEntries, 2);
    }
}
