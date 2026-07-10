<?php

namespace HardikSolanki\OpenAILaravel\Support;

class UsageStatistics
{
    public function __construct(
        public readonly int $totalRequests,
        public readonly int $totalTokens,
        public readonly float $totalCost,
        public readonly array $byModel = [],
        public readonly array $byUser = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'total_requests' => $this->totalRequests,
            'total_tokens' => $this->totalTokens,
            'total_cost' => $this->totalCost,
            'by_model' => $this->byModel,
            'by_user' => $this->byUser,
        ];
    }
}
