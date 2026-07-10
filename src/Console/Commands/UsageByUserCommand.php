<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use Illuminate\Console\Command;

class UsageByUserCommand extends Command
{
    protected $signature = 'openai:usage:by-user {--team=}';

    protected $description = 'Show usage broken down by user for a team';

    public function handle(UsageTrackingService $usage): int
    {
        $stats = $usage->getTeamUsage((int) $this->option('team'));

        $this->table(
            ['User ID', 'Requests', 'Tokens', 'Cost'],
            collect($stats->byUser)->map(fn ($row, $userId) => [
                $userId, $row['requests'], $row['tokens'], number_format($row['cost'], 6),
            ])->values()
        );

        return self::SUCCESS;
    }
}
