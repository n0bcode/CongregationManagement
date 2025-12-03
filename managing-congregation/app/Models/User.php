<?php

declare(strict_types=1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\PermissionKey;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    /**
     * Boot the model and register event listeners.
     */
    protected static function boot(): void
    {
        parent::boot();

        // Clear permission cache when user role changes
        static::updated(function ($user) {
            if ($user->isDirty('role')) {
                \Illuminate\Support\Facades\Cache::flush();
            }
        });
    }

    /**
     * Get the community that the user belongs to.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SUPER_ADMIN;
    }

    /**
     * Check if user has a specific permission.
     * Super admin bypass pattern for performance.
     * Uses caching to prevent N+1 queries.
     *
     * @param  PermissionKey|string  $permission  The permission to check
     * @return bool True if user has permission, false otherwise
     */
    public function hasPermission(PermissionKey|string $permission): bool
    {
        // Super admin bypass
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Convert enum to string if needed
        $permissionKey = $permission instanceof PermissionKey ? $permission->value : $permission;

        // Cache permission check for 1 hour to prevent N+1 queries
        $cacheKey = "user.{$this->id}.permission.{$permissionKey}";

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($permissionKey) {
            return DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->where('role_permissions.role', $this->role->value)
                ->where('permissions.key', $permissionKey)
                ->exists();
        });
    }
}
