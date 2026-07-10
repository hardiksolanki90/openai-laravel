<?php

namespace HardikSolanki\OpenAILaravel\Services;

use HardikSolanki\OpenAILaravel\Utilities\CostCalculator;
use Illuminate\Support\Facades\Config;

class CostCalculationService
{
    public function __construct(protected CostCalculator $calculator)
    {
    }

    public function calculateCost(string $model, int $promptTokens, int $completionTokens): float
    {
        return $this->calculator->calculate($model, $promptTokens, $completionTokens);
    }

    public function costPerModel(string $model): array
    {
        return Config::get("openai.models.{$model}.pricing", ['input' => 0.0, 'output' => 0.0]);
    }

    /**
     * @param  array<string, array{input: float, output: float}>  $newPricing  keyed by model name
     */
    public function updatePricing(array $newPricing): void
    {
        foreach ($newPricing as $model => $pricing) {
            Config::set("openai.models.{$model}.pricing", $pricing);
        }
    }
}
