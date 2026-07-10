<?php

namespace HardikSolanki\OpenAILaravel\Exceptions;

class BudgetExceededException extends OpenAIException
{
    public function __construct(protected float $remainingBudget = 0.0)
    {
        parent::__construct('Budget limit exceeded.');
    }

    public function remainingBudget(): float
    {
        return $this->remainingBudget;
    }
}
