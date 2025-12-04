<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    /**
     * Indicates if the model should be timestamped.
     * We only use created_at, no updated_at.
     */
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'auditable_type',
        'auditable_id',
        'target_type',
        'target_id',
        'old_values',
        'new_values',
        'changes',
        'description',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Prevent updates to audit logs (immutable).
     */
    protected static function booted(): void
    {
        static::updating(function ($model) {
            throw new \RuntimeException('Audit logs are immutable and cannot be updated.');
        });

        static::deleting(function ($model) {
            throw new \RuntimeException('Audit logs are immutable and cannot be deleted.');
        });
    }

    /**
     * Get the user who performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by action.
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user.
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to permission-related actions
     */
    public function scopePermissionActions($query)
    {
        return $query->whereIn('action', [
            'permission_updated',
            'role_changed',
        ]);
    }

    /**
     * Scope to a specific target
     */
    public function scopeTarget($query, string $type, $id)
    {
        return $query->where('target_type', $type)
            ->where('target_id', $id);
    }
}
