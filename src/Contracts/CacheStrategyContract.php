<?php

namespace HardikSolanki\OpenAILaravel\Contracts;

use HardikSolanki\OpenAILaravel\Models\PromptCache;

interface CacheStrategyContract
{
    public function get(int $teamId, string $model, array $messages): ?PromptCache;

    public function set(int $teamId, string $model, array $messages, mixed $response, int $tokens, float $cost, ?int $ttlSeconds = 3600): PromptCache;

    public function invalidate(int $teamId, string $model): void;
}
