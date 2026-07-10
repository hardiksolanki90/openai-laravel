<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Models\RateLimitToken;
use Illuminate\Console\Command;

class RateLimitResetCommand extends Command
{
    protected $signature = 'openai:rate-limit:reset {--team=}';

    protected $description = 'Reset persisted rate limit buckets';

    public function handle(): int
    {
        $query = RateLimitToken::query();

        if ($team = $this->option('team')) {
            $query->where('team_id', $team);
        }

        $count = $query->delete();

        $this->info("Reset {$count} rate limit bucket(s).");

        return self::SUCCESS;
    }
}
