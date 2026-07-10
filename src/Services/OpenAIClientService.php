<?php

namespace HardikSolanki\OpenAILaravel\Services;

use HardikSolanki\OpenAILaravel\Exceptions\InvalidAPIKeyException;
use HardikSolanki\OpenAILaravel\Models\APIKey;

class OpenAIClientService
{
    /**
     * Resolve the OpenAI PHP client for a given team API key, falling back
     * to the package-wide default configured in config/openai.php.
     */
    public function clientFor(?int $apiKeyId): \OpenAI\Client
    {
        $apiKey = config('openai.api_key');

        if ($apiKeyId !== null) {
            $record = APIKey::find($apiKeyId);

            if (! $record || ! $record->is_active) {
                throw new InvalidAPIKeyException;
            }

            $apiKey = $record->key;
            $record->forceFill(['last_used_at' => now()])->saveQuietly();
        }

        if (! $apiKey) {
            throw new InvalidAPIKeyException('No OpenAI API key configured.');
        }

        return \OpenAI::client($apiKey);
    }

    public function chat(?int $apiKeyId, array $payload): array
    {
        $response = $this->clientFor($apiKeyId)->chat()->create($payload);

        return $response->toArray();
    }

    public function chatStream(?int $apiKeyId, array $payload): \Generator
    {
        $stream = $this->clientFor($apiKeyId)->chat()->createStreamed($payload);

        foreach ($stream as $response) {
            yield $response->toArray();
        }
    }

    public function image(?int $apiKeyId, array $payload): array
    {
        $response = $this->clientFor($apiKeyId)->images()->create($payload);

        return $response->toArray();
    }
}
