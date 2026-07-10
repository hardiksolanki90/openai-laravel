<?php

namespace HardikSolanki\OpenAILaravel\Exceptions;

class ContextWindowExceededException extends OpenAIException
{
    public function __construct(protected int $requiredTokens, protected int $availableTokens)
    {
        parent::__construct("Context window exceeded: required {$requiredTokens} tokens, {$availableTokens} available.");
    }

    public function requiredTokens(): int
    {
        return $this->requiredTokens;
    }

    public function availableTokens(): int
    {
        return $this->availableTokens;
    }
}
