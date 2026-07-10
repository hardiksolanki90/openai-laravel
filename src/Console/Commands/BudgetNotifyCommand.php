<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Events\BudgetLimitReached;
use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use HardikSolanki\OpenAILaravel\Models\Team;
use Illuminate\Console\Command;

class BudgetNotifyCommand extends Command
{
    protected $signature = 'openai:budget:notify';

    protected $description = 'Dispatch BudgetLimitReached for teams crossing their warning threshold and not yet notified';

    public function handle(): int
    {
        $budgets = BudgetLimit::where('is_active', true)
            ->whereNull('notified_at')
            ->get()
            ->filter(fn (BudgetLimit $b) => $b->isWarning());

        foreach ($budgets as $budget) {
            event(new BudgetLimitReached($budget->team ?? Team::findOrFail($budget->team_id), $budget));
            $budget->update(['notified_at' => now()]);
            $this->info("Notified team [{$budget->team_id}].");
        }

        return self::SUCCESS;
    }
}
