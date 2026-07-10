<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Models\APIKey;
use Illuminate\Console\Command;

class KeysRevokeCommand extends Command
{
    protected $signature = 'openai:keys:revoke {keyId}';

    protected $description = 'Revoke (deactivate) an OpenAI API key';

    public function handle(): int
    {
        $key = APIKey::findOrFail($this->argument('keyId'));
        $key->update(['is_active' => false]);

        $this->info("API key [{$key->id}] revoked.");

        return self::SUCCESS;
    }
}
