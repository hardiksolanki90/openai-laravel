<?php

namespace App\Http\Controllers;

use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, UsageTrackingService $usage)
    {
        $teamId = $request->user()->current_team_id;

        $stats = $usage->getTeamUsage($teamId, now()->startOfMonth(), now());
        $budget = BudgetLimit::firstOrCreate(['team_id' => $teamId]);

        return view('dashboard', compact('stats', 'budget'));
    }
}
