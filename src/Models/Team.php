<?php

namespace HardikSolanki\OpenAILaravel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'owner_id'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class), 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(APIKey::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function promptTemplates(): HasMany
    {
        return $this->hasMany(PromptTemplate::class);
    }

    public function budgetLimit(): HasOne
    {
        return $this->hasOne(BudgetLimit::class);
    }

    public function usageLogs(): HasMany
    {
        return $this->hasMany(UsageLog::class);
    }
}
