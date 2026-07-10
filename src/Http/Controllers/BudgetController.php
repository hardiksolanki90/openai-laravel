<?php

namespace HardikSolanki\OpenAILaravel\Http\Controllers;

use HardikSolanki\OpenAILaravel\Http\Requests\UpdateBudgetRequest;
use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function show(Request $request)
    {
        $budget = BudgetLimit::firstOrCreate(['team_id' => $this->teamId($request)]);

        return response()->json([
            'monthly_limit' => (float) $budget->monthly_limit,
            'current_spend' => (float) $budget->current_spend,
            'remaining' => $budget->remainingBudget(),
            'percentage_used' => $budget->percentageUsed(),
            'warning_threshold' => (float) $budget->warning_threshold,
            'block_on_limit' => $budget->block_on_limit,
            'month_ends_at' => now()->endOfMonth()->toIso8601String(),
        ]);
    }

    public function update(UpdateBudgetRequest $request)
    {
        $budget = BudgetLimit::updateOrCreate(
            ['team_id' => $this->teamId($request)],
            $request->validated()
        );

        return response()->json($budget);
    }
}
