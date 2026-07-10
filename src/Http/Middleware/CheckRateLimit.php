<?php

namespace HardikSolanki\OpenAILaravel\Http\Middleware;

use Closure;
use HardikSolanki\OpenAILaravel\Services\RateLimitService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRateLimit
{
    public function __construct(protected RateLimitService $rateLimiter)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('openai.rate_limiting.enabled', true)) {
            return $next($request);
        }

        $teamId = (int) $request->attributes->get('openai_team_id');
        $apiKeyId = (int) $request->input('api_key_id', 0);

        if (! $this->rateLimiter->isAllowed($teamId, $apiKeyId)) {
            if (config('openai.rate_limiting.block_when_exceeded', false)) {
                return response()->json([
                    'message' => 'Rate limit exceeded.',
                    'retry_after' => 3600,
                ], 429);
            }
        } else {
            $this->rateLimiter->consumeTokens($teamId, $apiKeyId);
        }

        return $next($request);
    }
}
