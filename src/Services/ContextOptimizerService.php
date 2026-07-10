<?php

namespace HardikSolanki\OpenAILaravel\Services;

use HardikSolanki\OpenAILaravel\Contracts\ContextOptimizerContract;
use HardikSolanki\OpenAILaravel\Exceptions\ContextWindowExceededException;
use HardikSolanki\OpenAILaravel\Utilities\ContextTruncator;
use HardikSolanki\OpenAILaravel\Utilities\TokenCounter;

class ContextOptimizerService implements ContextOptimizerContract
{
    public function __construct(
        protected TokenCounter $tokenCounter,
        protected ContextTruncator $truncator,
    ) {
    }

    public function getContextWindow(string $model): int
    {
        return (int) config("openai.models.{$model}.context_window", 4096);
    }

    public function optimizeMessages(array $messages, string $model, float $reservedPercentage = 0.1): array
    {
        $available = $this->availableTokens($model, $reservedPercentage);

        if ($this->tokenCounter->countMessages($messages) <= $available) {
            return $messages;
        }

        return $this->truncateOldestMessages($messages, $model, $available);
    }

    public function estimateTokens(string $text): int
    {
        return $this->tokenCounter->count($text);
    }

    public function canFitInContext(array $messages, string $model, string $newPrompt): bool
    {
        $reserved = (float) config('openai.context_optimization.reserved_percentage', 0.1);
        $available = $this->availableTokens($model, $reserved);

        $required = $this->tokenCounter->countMessages($messages) + $this->tokenCounter->count($newPrompt);

        return $required <= $available;
    }

    public function truncateOldestMessages(array $messages, string $model, int $targetTokens): array
    {
        $truncated = $this->truncator->truncateOldest($messages, $targetTokens);

        if ($this->tokenCounter->countMessages($truncated) > $targetTokens && empty($truncated)) {
            throw new ContextWindowExceededException(
                $this->tokenCounter->countMessages($messages),
                $targetTokens
            );
        }

        return $truncated;
    }

    protected function availableTokens(string $model, float $reservedPercentage): int
    {
        $window = $this->getContextWindow($model);

        return (int) floor($window * (1 - $reservedPercentage));
    }
}
