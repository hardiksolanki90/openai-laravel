<?php

namespace HardikSolanki\OpenAILaravel\Services;

use HardikSolanki\OpenAILaravel\Models\PromptTemplate;
use HardikSolanki\OpenAILaravel\Support\ValidationResult;
use HardikSolanki\OpenAILaravel\Utilities\PromptInterpolator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PromptTemplateService
{
    public function __construct(protected PromptInterpolator $interpolator)
    {
    }

    public function interpolate(PromptTemplate $template, array $variables): string
    {
        return $this->interpolator->interpolate($template->content, $variables);
    }

    public function validate(PromptTemplate $template, array $variables): ValidationResult
    {
        $errors = [];

        foreach ($template->variables as $definition) {
            $name = $definition['name'];
            $required = (bool) ($definition['required'] ?? false);

            if ($required && ! array_key_exists($name, $variables)) {
                $errors[$name] = "The {$name} field is required.";

                continue;
            }

            if (array_key_exists($name, $variables) && ($definition['type'] ?? null) === 'number' && ! is_numeric($variables[$name])) {
                $errors[$name] = "The {$name} field must be a number.";
            }
        }

        return empty($errors) ? ValidationResult::pass() : ValidationResult::fail($errors);
    }

    public function create(int $teamId, int $userId, array $data): PromptTemplate
    {
        $data['team_id'] = $teamId;
        $data['created_by'] = $userId;
        $data['slug'] ??= Str::slug($data['name']);

        return PromptTemplate::create($data);
    }

    public function getPublicTemplates(int $teamId): Collection
    {
        return PromptTemplate::where('team_id', $teamId)->where('is_public', true)->get();
    }

    public function getUserTemplates(int $teamId, int $userId): Collection
    {
        return PromptTemplate::where('team_id', $teamId)->where('created_by', $userId)->get();
    }
}
