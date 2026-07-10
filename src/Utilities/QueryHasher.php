<?php

namespace HardikSolanki\OpenAILaravel\Utilities;

class QueryHasher
{
    public function hash(string $model, array $messages, ?string $prompt = null): string
    {
        return hash('sha256', json_encode([
            'model' => $model,
            'messages' => $messages,
            'prompt' => $prompt,
        ]));
    }
}
