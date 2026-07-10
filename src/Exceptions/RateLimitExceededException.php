<?php

namespace HardikSolanki\OpenAILaravel\Exceptions;

class RateLimitExceededException extends OpenAIException
{
    public function __construct(
        protected int $remainingSeconds = 0,
        protected int $retryAfter = 0,
    ) {
        parent::__construct("Rate limit exceeded. Retry after {$retryAfter} seconds.");
    }

    public function resetInSeconds(): int
    {
        return $this->remainingSeconds;
    }

    public function retryAfter(): int
    {
        return $this->retryAfter;
    }
}
