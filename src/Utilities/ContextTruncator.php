<?php

namespace HardikSolanki\OpenAILaravel\Utilities;

class ContextTruncator
{
    public function __construct(protected TokenCounter $tokenCounter = new TokenCounter)
    {
    }

    /**
     * Drop the oldest non-system messages until the remaining messages
     * fit within $targetTokens.
     */
    public function truncateOldest(array $messages, int $targetTokens): array
    {
        $system = array_values(array_filter($messages, fn ($m) => ($m['role'] ?? null) === 'system'));
        $rest = array_values(array_filter($messages, fn ($m) => ($m['role'] ?? null) !== 'system'));

        while ($this->tokenCounter->countMessages(array_merge($system, $rest)) > $targetTokens && count($rest) > 0) {
            array_shift($rest);
        }

        return array_merge($system, $rest);
    }
}
