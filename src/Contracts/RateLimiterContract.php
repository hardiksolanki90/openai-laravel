<?php

namespace HardikSolanki\OpenAILaravel\Contracts;

interface RateLimiterContract
{
    public function isAllowed(int $teamId, int $apiKeyId): bool;

    public function consumeTokens(int $teamId, int $apiKeyId, int $count = 1): bool;

    public function getRemainingTokens(int $teamId, int $apiKeyId): int;

    public function resetLimit(int $teamId, int $apiKeyId): void;
}
