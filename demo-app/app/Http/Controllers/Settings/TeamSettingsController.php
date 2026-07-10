<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use HardikSolanki\OpenAILaravel\Models\TeamMember;
use Illuminate\Http\Request;

class TeamSettingsController extends Controller
{
    public function index(Request $request)
    {
        $members = TeamMember::where('team_id', $request->user()->current_team_id)->with('user')->get();

        return view('settings.team', compact('members'));
    }

    public function invite(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role' => 'required|string|in:admin,member,viewer',
        ]);

        TeamMember::create([
            'team_id' => $request->user()->current_team_id,
            'user_id' => $data['user_id'],
            'role' => $data['role'],
            'invited_at' => now(),
        ]);

        return redirect()->route('settings.team');
    }
}
