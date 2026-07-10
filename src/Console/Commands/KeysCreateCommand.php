<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Models\APIKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;

class KeysCreateCommand extends Command
{
    protected $signature = 'openai:keys:create {--team=} {--name=} {--key=} {--created-by=1}';

    protected $description = 'Create a new encrypted OpenAI API key for a team';

    public function handle(): int
    {
        $teamId = $this->option('team') ?? $this->ask('Team ID');
        $name = $this->option('name') ?? $this->ask('Key name');
        $key = $this->option('key') ?? $this->secret('OpenAI API key (sk-...)');

        $apiKey = APIKey::create([
            'team_id' => $teamId,
            'name' => $name,
            'key_encrypted' => Crypt::encryptString($key),
            'key_hash' => hash('sha256', $key),
            'is_active' => true,
            'created_by' => $this->option('created-by'),
        ]);

        $this->info("API key [{$apiKey->id}] created for team [{$teamId}].");

        return self::SUCCESS;
    }
}
