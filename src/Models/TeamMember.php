<?php

namespace HardikSolanki\OpenAILaravel\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class TeamMember extends Model
{
    protected $fillable = [
        'team_id', 'user_id', 'role', 'permissions', 'invited_at', 'joined_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'invited_at' => 'datetime',
        'joined_at' => 'datetime',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model', Authenticatable::class), 'user_id');
    }

    public function hasPermission(string $permission): bool
    {
        if (is_array($this->permissions) && array_key_exists($permission, $this->permissions)) {
            return (bool) $this->permissions[$permission];
        }

        $rolePermissions = config("openai.rbac.role_permissions.{$this->role}", []);

        return in_array($permission, $rolePermissions, true);
    }
}
