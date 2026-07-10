<?php

namespace HardikSolanki\OpenAILaravel\Utilities;

class TokenCounter
{
    /**
     * Approximate token count (~4 characters per token, the common
     * heuristic for GPT-family tokenizers) without a bundled BPE table.
     */
    public function count(string $text): int
    {
        if ($text === '') {
            return 0;
        }

        return (int) ceil(mb_strlen($text) / 4);
    }

    public function countMessages(array $messages): int
    {
        $total = 0;

        foreach ($messages as $message) {
            $total += $this->count($message['content'] ?? '');
            $total += 4; // per-message role/formatting overhead
        }

        return $total;
    }
}
