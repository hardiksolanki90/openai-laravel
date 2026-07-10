<?php

namespace HardikSolanki\OpenAILaravel\Exceptions;

class InvalidAPIKeyException extends OpenAIException
{
    public function __construct(string $message = 'Invalid or inactive API key.')
    {
        parent::__construct($message);
    }
}
