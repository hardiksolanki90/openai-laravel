<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Events\BudgetLimitReached;
use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use HardikSolanki\OpenAILaravel\Models\Team;
use Illuminate\Console\Command;

class BudgetCheckCommand extends Command
{
    protected $signature = 'openai:budget:check';

    protected $description = 'Check all active team budgets and dispatch events for exceeded/warning thresholds';

    public function handle(): int
    {
        BudgetLimit::where('is_active', true)->with('team')->chunk(100, function ($budgets) {
            foreach ($budgets as $budget) {
                if ($budget->isExceeded() || $budget->isWarning()) {
                    $team = $budget->team ?? Team::find($budget->team_id);
                    event(new BudgetLimitReached($team, $budget));
                    $this->warn("Team [{$budget->team_id}] budget alert: {$budget->percentageUsed()}% used.");
                }
            }
        });

        return self::SUCCESS;
    }
}
