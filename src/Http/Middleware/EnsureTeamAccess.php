<?php

namespace HardikSolanki\OpenAILaravel\Http\Middleware;

use Closure;
use HardikSolanki\OpenAILaravel\Models\TeamMember;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $teamId = (int) ($request->route('team') ?? $request->header('X-Team-ID') ?? $request->input('team_id'));

        if (! $teamId) {
            return response()->json(['message' => 'Team could not be resolved for this request.'], 400);
        }

        $membership = TeamMember::where('team_id', $teamId)
            ->where('user_id', $request->user()?->id)
            ->first();

        if (! $membership) {
            return response()->json(['message' => 'You do not have access to this team.'], 403);
        }

        $request->attributes->set('openai_team_id', $teamId);
        $request->attributes->set('openai_team_member', $membership);

        app(PermissionRegistrar::class)->setPermissionsTeamId($teamId);

        return $next($request);
    }
}
