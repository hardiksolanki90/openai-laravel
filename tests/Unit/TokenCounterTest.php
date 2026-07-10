<?php

namespace HardikSolanki\OpenAILaravel\Tests\Unit;

use HardikSolanki\OpenAILaravel\Tests\TestCase;
use HardikSolanki\OpenAILaravel\Utilities\TokenCounter;

class TokenCounterTest extends TestCase
{
    public function test_counts_tokens_from_character_heuristic(): void
    {
        $counter = new TokenCounter;

        $this->assertSame(0, $counter->count(''));
        $this->assertSame(3, $counter->count('12345678901')); // 11 chars / 4 => 3
    }

    public function test_counts_messages_with_overhead(): void
    {
        $counter = new TokenCounter;

        $messages = [
            ['role' => 'user', 'content' => 'hello'],
            ['role' => 'assistant', 'content' => 'world'],
        ];

        $expected = $counter->count('hello') + 4 + $counter->count('world') + 4;

        $this->assertSame($expected, $counter->countMessages($messages));
    }
}
