<?php

namespace HardikSolanki\OpenAILaravel\Facades;

use HardikSolanki\OpenAILaravel\Builders\ImageBuilder;
use HardikSolanki\OpenAILaravel\Builders\TextBuilder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static TextBuilder text()
 * @method static ImageBuilder image()
 *
 * @see \HardikSolanki\OpenAILaravel\OpenAIManager
 */
class OpenAI extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'openai';
    }
}
