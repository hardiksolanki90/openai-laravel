<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use Carbon\Carbon;
use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use Illuminate\Console\Command;

class UsageReportCommand extends Command
{
    protected $signature = 'openai:usage:report {--team=} {--month=} {--format=table}';

    protected $description = 'Generate a usage report for a team';

    public function handle(UsageTrackingService $usage): int
    {
        $month = $this->option('month') ? Carbon::createFromFormat('m', $this->option('month')) : now();

        $stats = $usage->getTeamUsage(
            (int) $this->option('team'),
            $month->copy()->startOfMonth(),
            $month->copy()->endOfMonth(),
        );

        if ($this->option('format') === 'json') {
            $this->line(json_encode($stats->toArray(), JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

        $this->table(
            ['Total Requests', 'Total Tokens', 'Total Cost'],
            [[$stats->totalRequests, $stats->totalTokens, number_format($stats->totalCost, 6)]]
        );

        return self::SUCCESS;
    }
}
