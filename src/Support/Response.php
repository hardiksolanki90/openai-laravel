<?php

namespace HardikSolanki\OpenAILaravel\Support;

class Response
{
    public function __construct(
        protected string $content,
        protected int $promptTokens = 0,
        protected int $completionTokens = 0,
        protected float $cost = 0.0,
        protected array $raw = [],
        protected bool $fromCache = false,
    ) {
    }

    public function content(): string
    {
        return $this->content;
    }

    public function tokensUsed(): int
    {
        return $this->promptTokens + $this->completionTokens;
    }

    public function promptTokens(): int
    {
        return $this->promptTokens;
    }

    public function completionTokens(): int
    {
        return $this->completionTokens;
    }

    public function costIncurred(): float
    {
        return $this->cost;
    }

    public function raw(): array
    {
        return $this->raw;
    }

    public function fromCache(): bool
    {
        return $this->fromCache;
    }
}
