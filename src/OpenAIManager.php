<?php

namespace HardikSolanki\OpenAILaravel;

use HardikSolanki\OpenAILaravel\Builders\ImageBuilder;
use HardikSolanki\OpenAILaravel\Builders\TextBuilder;
use Illuminate\Contracts\Container\Container;

class OpenAIManager
{
    public function __construct(protected Container $container)
    {
    }

    public function text(): TextBuilder
    {
        return $this->container->make(TextBuilder::class);
    }

    public function image(): ImageBuilder
    {
        return $this->container->make(ImageBuilder::class);
    }
}
