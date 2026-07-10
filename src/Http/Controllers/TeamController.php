<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use HardikSolanki\OpenAILaravel\Http\Requests\InviteTeamMemberRequest;
use HardikSolanki\OpenAILaravel\Http\Requests\UpdateTeamMemberRequest;
use HardikSolanki\OpenAILaravel\Models\TeamMember;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function members(Request $request, int $id)
    {
        return response()->json(
            TeamMember::where('team_id', $id)->with('user')->get()
        );
    }

    public function inviteMember(InviteTeamMemberRequest $request, int $id)
    {
        $member = TeamMember::create([
            'team_id' => $id,
            'user_id' => $request->validated('user_id'),
            'role' => $request->validated('role'),
            'invited_at' => now(),
        ]);

        $user = $member->user;

        if ($user && method_exists($user, 'assignRole')) {
            $user->assignRole($request->validated('role'));
        }

        return response()->json($member, 201);
    }

    public function updateMember(UpdateTeamMemberRequest $request, int $id, int $memberId)
    {
        $member = TeamMember::where('team_id', $id)->findOrFail($memberId);
        $member->update($request->validated());

        $user = $member->user;

        if ($user && method_exists($user, 'syncRoles')) {
            $user->syncRoles([$request->validated('role')]);
        }

        return response()->json($member);
    }

    public function removeMember(Request $request, int $id, int $memberId)
    {
        TeamMember::where('team_id', $id)->findOrFail($memberId)->delete();

        return response()->json(['message' => 'Member removed.']);
    }
}
