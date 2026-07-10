<?php

namespace HardikSolanki\OpenAILaravel\Console\Commands;

use HardikSolanki\OpenAILaravel\Models\APIKey;
use Illuminate\Console\Command;

class KeysListCommand extends Command
{
    protected $signature = 'openai:keys:list {--team=}';

    protected $description = 'List OpenAI API keys (masked) for a team';

    public function handle(): int
    {
        $keys = APIKey::when($this->option('team'), fn ($q) => $q->where('team_id', $this->option('team')))->get();

        $this->table(
            ['ID', 'Team', 'Name', 'Key', 'Active', 'Last Used'],
            $keys->map(fn (APIKey $key) => [
                $key->id, $key->team_id, $key->name, $key->key_masked,
                $key->is_active ? 'yes' : 'no', $key->last_used_at?->toDateTimeString() ?? '-',
            ])
        );

        return self::SUCCESS;
    }
}
