<?php

namespace HardikSolanki\OpenAILaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RateLimitExceeded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public int $teamId,
        public ?int $apiKeyId,
        public int $retryAfter,
    ) {
    }
}
