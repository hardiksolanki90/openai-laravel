<?php

namespace HardikSolanki\OpenAILaravel\Utilities;

class PromptInterpolator
{
    public function interpolate(string $content, array $data): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($matches) use ($data) {
            return array_key_exists($matches[1], $data) ? (string) $data[$matches[1]] : $matches[0];
        }, $content);
    }

    public function extractVariableNames(string $content): array
    {
        preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $content, $matches);

        return array_values(array_unique($matches[1]));
    }
}
