<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use Illuminate\Console\Command;

class BudgetResetCommand extends Command
{
    protected $signature = 'openai:budget:reset {--team=}';

    protected $description = 'Reset current spend for a team budget (monthly rollover)';

    public function handle(): int
    {
        $query = BudgetLimit::query();

        if ($team = $this->option('team')) {
            $query->where('team_id', $team);
        }

        $count = $query->update(['current_spend' => 0, 'reset_at' => now(), 'notified_at' => null]);

        $this->info("Reset {$count} budget(s).");

        return self::SUCCESS;
    }
}
