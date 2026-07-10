<?php

namespace HardikSolanki\OpenAILaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

class APIKey extends Model
{
    protected $table = 'api_keys';

    protected $fillable = ['team_id', 'name', 'key_encrypted', 'key_hash', 'is_active', 'created_by'];

    protected $hidden = ['key_encrypted', 'key_hash', 'key'];

    protected $appends = ['key_masked'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    public function getKeyAttribute(): string
    {
        return Crypt::decryptString($this->key_encrypted);
    }

    public function getKeyMaskedAttribute(): string
    {
        $key = $this->key;

        return substr($key, 0, 3).'...***';
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class), 'created_by');
    }
}
