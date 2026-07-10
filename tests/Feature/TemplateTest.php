<?php

namespace HardikSolanki\OpenAILaravel\Tests\Feature;

use HardikSolanki\OpenAILaravel\Models\Team;
use HardikSolanki\OpenAILaravel\Services\PromptTemplateService;
use HardikSolanki\OpenAILaravel\Tests\TestCase;

class TemplateTest extends TestCase
{
    public function test_creates_and_interpolates_a_template(): void
    {
        [$team, $userId] = $this->makeTeam();

        $service = app(PromptTemplateService::class);

        $template = $service->create($team->id, $userId, [
            'name' => 'Product Review',
            'content' => 'Review the product {{ product_name }}.',
            'variables' => [
                ['name' => 'product_name', 'type' => 'string', 'required' => true],
            ],
        ]);

        $this->assertSame('product-review', $template->slug);

        $content = $service->interpolate($template, ['product_name' => 'MacBook Pro']);

        $this->assertSame('Review the product MacBook Pro.', $content);
    }

    public function test_validation_fails_when_required_variable_missing(): void
    {
        [$team, $userId] = $this->makeTeam();

        $service = app(PromptTemplateService::class);

        $template = $service->create($team->id, $userId, [
            'name' => 'Product Review',
            'content' => 'Review {{ product_name }}',
            'variables' => [
                ['name' => 'product_name', 'type' => 'string', 'required' => true],
            ],
        ]);

        $result = $service->validate($template, []);

        $this->assertTrue($result->fails());
        $this->assertArrayHasKey('product_name', $result->errors());
    }

    /**
     * @return array{0: Team, 1: int}
     */
    protected function makeTeam(): array
    {
        $userId = \DB::table('users')->insertGetId([
            'name' => 'Owner', 'email' => 'owner2@example.com', 'password' => 'secret', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $team = Team::create(['name' => 'Acme', 'slug' => 'acme-2', 'owner_id' => $userId]);

        return [$team, $userId];
    }
}
