<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Models\APIKey;
use HardikSolanki\OpenAILaravel\Services\OpenAIClientService;
use Illuminate\Console\Command;

class KeysTestCommand extends Command
{
    protected $signature = 'openai:keys:test {keyId}';

    protected $description = 'Test an OpenAI API key by making a minimal request';

    public function handle(OpenAIClientService $client): int
    {
        $key = APIKey::findOrFail($this->argument('keyId'));

        try {
            $client->chat($key->id, [
                'model' => config('openai.default_model'),
                'messages' => [['role' => 'user', 'content' => 'ping']],
                'max_tokens' => 1,
            ]);

            $this->info("API key [{$key->id}] is valid.");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error("API key [{$key->id}] failed: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
