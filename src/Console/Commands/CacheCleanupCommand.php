<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Services\PromptCacheService;
use Illuminate\Console\Command;

class CacheCleanupCommand extends Command
{
    protected $signature = 'openai:cache:cleanup';

    protected $description = 'Delete expired prompt cache entries';

    public function handle(PromptCacheService $cache): int
    {
        $deleted = $cache->clearExpired();

        $this->info("Removed {$deleted} expired cache entries.");

        return self::SUCCESS;
    }
}
