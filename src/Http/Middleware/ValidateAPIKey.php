<?php

namespace HardikSolanki\OpenAILaravel\Http\Middleware;

use Closure;
use HardikSolanki\OpenAILaravel\Models\APIKey;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateAPIKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $apiKeyId = $request->input('api_key_id');

        if ($apiKeyId === null) {
            return $next($request);
        }

        $teamId = (int) $request->attributes->get('openai_team_id');

        $valid = APIKey::where('id', $apiKeyId)
            ->where('team_id', $teamId)
            ->where('is_active', true)
            ->exists();

        if (! $valid) {
            return response()->json(['message' => 'Invalid or inactive API key.'], 422);
        }

        return $next($request);
    }
}
