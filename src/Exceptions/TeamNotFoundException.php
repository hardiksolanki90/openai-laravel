<?php

namespace HardikSolanki\OpenAILaravel\Exceptions;

class TeamNotFoundException extends OpenAIException
{
    public function __construct(int $teamId)
    {
        parent::__construct("Team [{$teamId}] not found.");
    }
}
