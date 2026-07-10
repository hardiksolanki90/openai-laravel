<?php

namespace HardikSolanki\OpenAILaravel\Utilities;

class CostCalculator
{
    public function calculate(string $model, int $promptTokens, int $completionTokens): float
    {
        $pricing = config("openai.models.{$model}.pricing", [
            'input' => 0.0,
            'output' => 0.0,
        ]);

        $cost = ($promptTokens * $pricing['input']) + ($completionTokens * $pricing['output']);

        return round($cost, 6);
    }
}
