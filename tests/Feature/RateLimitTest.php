<?php

namespace HardikSolanki\OpenAILaravel\Tests\Feature;

use HardikSolanki\OpenAILaravel\Services\RateLimitService;
use HardikSolanki\OpenAILaravel\Tests\TestCase;

class RateLimitTest extends TestCase
{
    public function test_allows_requests_until_bucket_is_exhausted(): void
    {
        $service = new RateLimitService(maxTokens: 2, windowSize: 3600);

        $this->assertTrue($service->isAllowed(1, 1));
        $this->assertTrue($service->consumeTokens(1, 1));
        $this->assertTrue($service->consumeTokens(1, 1));
        $this->assertFalse($service->consumeTokens(1, 1));
        $this->assertFalse($service->isAllowed(1, 1));
    }

    public function test_reset_limit_restores_the_bucket(): void
    {
        $service = new RateLimitService(maxTokens: 1, windowSize: 3600);

        $service->consumeTokens(1, 1);
        $this->assertFalse($service->isAllowed(1, 1));

        $service->resetLimit(1, 1);

        $this->assertTrue($service->isAllowed(1, 1));
        $this->assertSame(1, $service->getRemainingTokens(1, 1));
    }
}
