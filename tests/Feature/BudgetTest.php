<?php

namespace HardikSolanki\OpenAILaravel\Tests\Feature;

use HardikSolanki\OpenAILaravel\Models\BudgetLimit;
use HardikSolanki\OpenAILaravel\Models\Team;
use HardikSolanki\OpenAILaravel\Tests\TestCase;

class BudgetTest extends TestCase
{
    public function test_budget_is_exceeded_once_spend_reaches_limit(): void
    {
        $team = $this->makeTeam();

        $budget = BudgetLimit::create([
            'team_id' => $team->id,
            'monthly_limit' => 100,
            'warning_threshold' => 80,
            'block_on_limit' => true,
        ]);

        $this->assertFalse($budget->isExceeded());

        $budget->increment('current_spend', 100);

        $this->assertTrue($budget->fresh()->isExceeded());
    }

    public function test_percentage_used_and_remaining_budget(): void
    {
        $team = $this->makeTeam();

        $budget = BudgetLimit::create([
            'team_id' => $team->id,
            'monthly_limit' => 100,
            'current_spend' => 25,
            'warning_threshold' => 80,
        ]);

        $this->assertSame(25.0, $budget->percentageUsed());
        $this->assertSame(75.0, $budget->remainingBudget());
    }

    protected function makeTeam(): Team
    {
        $userId = \DB::table('users')->insertGetId([
            'name' => 'Owner', 'email' => 'owner3@example.com', 'password' => 'secret', 'created_at' => now(), 'updated_at' => now(),
        ]);

        return Team::create(['name' => 'Acme', 'slug' => 'acme-3', 'owner_id' => $userId]);
    }
}
