<?php

namespace App\Http\Controllers;

use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use Illuminate\Http\Request;

class UsageReportController extends Controller
{
    public function __invoke(Request $request, UsageTrackingService $usage)
    {
        $teamId = $request->user()->current_team_id;

        $stats = $usage->getTeamUsage($teamId, now()->startOfMonth(), now());
        $byModel = $usage->getModelBreakdown($teamId);

        return view('usage.reports', compact('stats', 'byModel'));
    }
}
