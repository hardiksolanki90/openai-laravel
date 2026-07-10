<?php

namespace HardikSolanki\OpenAILaravel\Tests\Unit;

use HardikSolanki\OpenAILaravel\Tests\TestCase;
use HardikSolanki\OpenAILaravel\Utilities\PromptInterpolator;

class PromptInterpolatorTest extends TestCase
{
    public function test_interpolates_variables(): void
    {
        $interpolator = new PromptInterpolator;

        $result = $interpolator->interpolate(
            'Review {{ product_name }} in {{ category }}.',
            ['product_name' => 'MacBook Pro', 'category' => 'Computers']
        );

        $this->assertSame('Review MacBook Pro in Computers.', $result);
    }

    public function test_leaves_unresolved_placeholders_untouched(): void
    {
        $interpolator = new PromptInterpolator;

        $result = $interpolator->interpolate('Hello {{ name }}', []);

        $this->assertSame('Hello {{ name }}', $result);
    }

    public function test_extracts_variable_names(): void
    {
        $interpolator = new PromptInterpolator;

        $names = $interpolator->extractVariableNames('{{ a }} and {{ b }} and {{ a }}');

        $this->assertSame(['a', 'b'], $names);
    }
}
