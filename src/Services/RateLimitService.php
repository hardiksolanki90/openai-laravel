<?php

namespace HardikSolanki\OpenAILaravel\Services;

use HardikSolanki\OpenAILaravel\Contracts\RateLimiterContract;
use HardikSolanki\OpenAILaravel\Models\RateLimitToken;

class RateLimitService implements RateLimiterContract
{
    /** @var array<string, array{tokens: int, refill_at: int}> in-memory bucket state, keyed "teamId:apiKeyId" */
    protected array $limits = [];

    public function __construct(
        protected int $maxTokens = 100,
        protected int $windowSize = 3600,
    ) {
    }

    public function isAllowed(int $teamId, int $apiKeyId): bool
    {
        return $this->getRemainingTokens($teamId, $apiKeyId) > 0;
    }

    public function consumeTokens(int $teamId, int $apiKeyId, int $count = 1): bool
    {
        $bucket = $this->bucket($teamId, $apiKeyId);

        if ($bucket['tokens'] < $count) {
            return false;
        }

        $bucket['tokens'] -= $count;
        $this->limits[$this->key($teamId, $apiKeyId)] = $bucket;

        return true;
    }

    public function getRemainingTokens(int $teamId, int $apiKeyId): int
    {
        return $this->bucket($teamId, $apiKeyId)['tokens'];
    }

    public function resetLimit(int $teamId, int $apiKeyId): void
    {
        $this->limits[$this->key($teamId, $apiKeyId)] = [
            'tokens' => $this->maxTokens,
            'refill_at' => now()->addSeconds($this->windowSize)->timestamp,
        ];
    }

    public function persistToDatabase(): void
    {
        foreach ($this->limits as $key => $bucket) {
            [$teamId, $apiKeyId] = array_map(fn ($v) => $v === '' ? null : (int) $v, explode(':', $key));

            RateLimitToken::updateOrCreate(
                ['team_id' => $teamId, 'api_key_id' => $apiKeyId],
                [
                    'tokens_remaining' => $bucket['tokens'],
                    'refill_at' => now()->createFromTimestamp($bucket['refill_at']),
                    'window_size' => $this->windowSize,
                    'max_tokens' => $this->maxTokens,
                ]
            );
        }
    }

    public function restoreFromDatabase(): void
    {
        RateLimitToken::query()->each(function (RateLimitToken $token) {
            $this->limits[$this->key($token->team_id, $token->api_key_id)] = [
                'tokens' => $token->tokens_remaining,
                'refill_at' => $token->refill_at->timestamp,
            ];
        });
    }

    protected function bucket(int $teamId, int $apiKeyId): array
    {
        $key = $this->key($teamId, $apiKeyId);

        if (! isset($this->limits[$key])) {
            $this->limits[$key] = [
                'tokens' => $this->maxTokens,
                'refill_at' => now()->addSeconds($this->windowSize)->timestamp,
            ];
        }

        if (now()->timestamp >= $this->limits[$key]['refill_at']) {
            $this->limits[$key] = [
                'tokens' => $this->maxTokens,
                'refill_at' => now()->addSeconds($this->windowSize)->timestamp,
            ];
        }

        return $this->limits[$key];
    }

    protected function key(int $teamId, ?int $apiKeyId): string
    {
        return "{$teamId}:{$apiKeyId}";
    }
}
