<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use Illuminate\Http\Request;

class BudgetSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $budget = BudgetLimit::firstOrCreate(['team_id' => $request->user()->current_team_id]);

        return view('settings.budget', compact('budget'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'monthly_limit' => 'required|numeric|min:0',
            'warning_threshold' => 'required|numeric|min:0',
            'block_on_limit' => 'sometimes|boolean',
        ]);

        BudgetLimit::updateOrCreate(['team_id' => $request->user()->current_team_id], $data);

        return redirect()->route('settings.budget');
    }
}
