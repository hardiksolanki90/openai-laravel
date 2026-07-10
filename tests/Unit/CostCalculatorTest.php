<?php

namespace HardikSolanki\OpenAILaravel\Tests\Unit;

use HardikSolanki\OpenAILaravel\Tests\TestCase;
use HardikSolanki\OpenAILaravel\Utilities\CostCalculator;

class CostCalculatorTest extends TestCase
{
    public function test_calculates_cost_from_configured_pricing(): void
    {
        config(['openai.models.gpt-4.pricing' => ['input' => 0.03, 'output' => 0.06]]);

        $calculator = new CostCalculator;

        $cost = $calculator->calculate('gpt-4', 100, 50);

        $this->assertSame(round(100 * 0.03 + 50 * 0.06, 6), $cost);
    }

    public function test_returns_zero_for_unknown_model(): void
    {
        $calculator = new CostCalculator;

        $this->assertSame(0.0, $calculator->calculate('unknown-model', 100, 50));
    }
}
