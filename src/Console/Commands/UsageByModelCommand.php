<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use Illuminate\Console\Command;

class UsageByModelCommand extends Command
{
    protected $signature = 'openai:usage:by-model {--team=}';

    protected $description = 'Show usage broken down by model for a team';

    public function handle(UsageTrackingService $usage): int
    {
        $breakdown = $usage->getModelBreakdown((int) $this->option('team'));

        $this->table(
            ['Model', 'Requests', 'Tokens', 'Cost'],
            collect($breakdown)->map(fn ($row, $model) => [
                $model, $row['requests'], $row['tokens'], number_format($row['cost'], 6),
            ])->values()
        );

        return self::SUCCESS;
    }
}
