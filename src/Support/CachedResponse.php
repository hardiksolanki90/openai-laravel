<?php

namespace HardikSolanki\OpenAILaravel\Support;

use HardikSolanki\OpenAILaravel\Models\PromptCache;

class CachedResponse extends Response
{
    public static function from(PromptCache $cache): self
    {
        $response = $cache->response;

        return new self(
            content: $response['content'] ?? '',
            promptTokens: $response['prompt_tokens'] ?? 0,
            completionTokens: $response['completion_tokens'] ?? 0,
            cost: (float) $cache->cost,
            raw: $response,
            fromCache: true,
        );
    }
}
