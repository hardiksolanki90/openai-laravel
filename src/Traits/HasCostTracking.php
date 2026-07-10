<?php

namespace HardikSolanki\OpenAILaravel\Traits;

trait HasCostTracking
{
    public function incrementCost(float $cost, int $tokens = 0): void
    {
        $this->increment('total_cost', $cost);

        if ($tokens > 0) {
            $this->increment('total_tokens', $tokens);
        }
    }
}
