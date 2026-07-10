<?php

namespace HardikSolanki\OpenAILaravel\Events;

use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use HardikSolanki\OpenAILaravel\Models\Team;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetLimitReached
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Team $team,
        public BudgetLimit $budgetLimit,
    ) {
    }
}
