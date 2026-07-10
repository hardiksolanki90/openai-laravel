<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Models\Conversation;
use HardikSolanki\OpenAILaravel\Services\ContextOptimizerService;
use Illuminate\Console\Command;

class ContextOptimizeCommand extends Command
{
    protected $signature = 'openai:context:optimize {--team=}';

    protected $description = 'Recalculate and optimize context window usage for team conversations';

    public function handle(ContextOptimizerService $optimizer): int
    {
        Conversation::where('team_id', $this->option('team'))
            ->where('is_archived', false)
            ->chunkById(50, function ($conversations) use ($optimizer) {
                foreach ($conversations as $conversation) {
                    $optimized = $optimizer->optimizeMessages(
                        $conversation->messages,
                        $conversation->model,
                        (float) config('openai.context_optimization.reserved_percentage', 0.15)
                    );

                    $conversation->update([
                        'messages' => $optimized,
                        'context_window_used' => $optimizer->estimateTokens(implode("\n", array_column($optimized, 'content'))),
                    ]);
                }
            });

        $this->info('Context optimization complete.');

        return self::SUCCESS;
    }
}
