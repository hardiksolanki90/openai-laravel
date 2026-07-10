<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

abstract class Controller extends BaseController
{
    use AuthorizesRequests;

    protected function teamId(\Illuminate\Http\Request $request): int
    {
        return (int) $request->attributes->get('openai_team_id');
    }
}
