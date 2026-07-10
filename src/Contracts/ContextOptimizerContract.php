<?php

namespace HardikSolanki\OpenAILaravel\Contracts;

interface ContextOptimizerContract
{
    public function getContextWindow(string $model): int;

    public function optimizeMessages(array $messages, string $model, float $reservedPercentage = 0.1): array;

    public function estimateTokens(string $text): int;

    public function canFitInContext(array $messages, string $model, string $newPrompt): bool;
}
