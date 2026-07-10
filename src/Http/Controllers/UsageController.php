<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use Carbon\Carbon;
use HardikSolanki\OpenAILaravel\Services\UsageTrackingService;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    public function __construct(protected UsageTrackingService $usage)
    {
    }

    public function summary(Request $request)
    {
        $stats = $this->usage->getTeamUsage(
            $this->teamId($request),
            $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : now()->startOfMonth(),
            $request->filled('end_date') ? Carbon::parse($request->input('end_date')) : now(),
        );

        return response()->json(array_merge(['period' => now()->format('Y-m')], $stats->toArray()));
    }

    public function daily(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->input('date')) : now();

        return response()->json($this->usage->getDailyBreakdown($this->teamId($request), $date));
    }

    public function user(Request $request, int $userId)
    {
        $stats = $this->usage->getUserUsage(
            $this->teamId($request),
            $userId,
            $request->filled('start_date') ? Carbon::parse($request->input('start_date')) : null,
            $request->filled('end_date') ? Carbon::parse($request->input('end_date')) : null,
        );

        return response()->json($stats->toArray());
    }
}
