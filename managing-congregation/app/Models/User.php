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
                try {
                    $cacheManager = app(\App\Contracts\CacheManagerInterface::class);
                    $cacheManager->invalidateUserCache($user->id);

                    \Illuminate\Support\Facades\Log::info('User role changed, cache invalidated', [
                        'user_id' => $user->id,
                        'old_role' => $user->getOriginal('role'),
                        'new_role' => $user->role?->value,
                    ]);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to invalidate user cache on role change', [
                        'user_id' => $user->id,
                        'error' => $e->getMessage(),
                    ]);
                }
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
     * Uses CacheManager to prevent N+1 queries.
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

        // If user has no role, they have no permissions
        if ($this->role === null) {
            return false;
        }

        // Convert enum to string if needed
        $permissionKey = $permission instanceof PermissionKey ? $permission->value : $permission;

        try {
            // Get CacheManager instance
            $cacheManager = app(\App\Contracts\CacheManagerInterface::class);

            // Try to get cached permissions
            $cachedPermissions = $cacheManager->getUserPermissions($this->id);

            if ($cachedPermissions === null) {
                // Cache miss - query database
                $cachedPermissions = DB::table('role_permissions')
                    ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                    ->where('role_permissions.role', $this->role->value)
                    ->pluck('permissions.key')
                    ->toArray();

                // Cache the result
                $cacheManager->cacheUserPermissions($this->id, $cachedPermissions);
            }

            return in_array($permissionKey, $cachedPermissions);
        } catch (\Throwable $e) {
            // Graceful fallback to database on cache errors
            \Illuminate\Support\Facades\Log::warning('Permission check failed, falling back to database', [
                'user_id' => $this->id,
                'permission' => $permissionKey,
                'error' => $e->getMessage(),
            ]);

            return DB::table('role_permissions')
                ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
                ->where('role_permissions.role', $this->role->value)
                ->where('permissions.key', $permissionKey)
                ->exists();
        }
    }
}
