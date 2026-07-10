<?php

namespace HardikSolanki\OpenAILaravel\Http\Middleware;

use Closure;
use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBudgetLimit
{
    public function handle(Request $request, Closure $next): Response
    {
        $teamId = (int) $request->attributes->get('openai_team_id');

        $budget = BudgetLimit::where('team_id', $teamId)->first();

        if ($budget && $budget->is_active && $budget->block_on_limit && $budget->isExceeded()) {
            return response()->json([
                'message' => 'Budget limit exceeded.',
                'remaining' => $budget->remainingBudget(),
            ], 402);
        }

        return $next($request);
    }
}
