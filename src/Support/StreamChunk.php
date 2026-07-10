<?php

namespace HardikSolanki\OpenAILaravel\Support;

class StreamChunk
{
    public function __construct(
        protected string $content,
        protected int $totalTokens = 0,
    ) {
    }

    public function content(): string
    {
        return $this->content;
    }

    public function totalTokens(): int
    {
        return $this->totalTokens;
    }
}
