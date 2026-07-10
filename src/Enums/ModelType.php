<?php

namespace HardikSolanki\OpenAILaravel\Enums;

enum ModelType: string
{
    case GPT4 = 'gpt-4';
    case GPT4_TURBO = 'gpt-4-turbo';
    case GPT35_TURBO = 'gpt-3.5-turbo';

    public function contextWindow(): int
    {
        return match ($this) {
            self::GPT4 => 8192,
            self::GPT4_TURBO => 128000,
            self::GPT35_TURBO => 4096,
        };
    }
}
