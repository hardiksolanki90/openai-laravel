<?php

namespace HardikSolanki\OpenAILaravel\Support;

class ValidationResult
{
    public function __construct(
        protected bool $passed,
        protected array $errors = [],
    ) {
    }

    public static function pass(): self
    {
        return new self(true);
    }

    public static function fail(array $errors): self
    {
        return new self(false, $errors);
    }

    public function passes(): bool
    {
        return $this->passed;
    }

    public function fails(): bool
    {
        return ! $this->passed;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
