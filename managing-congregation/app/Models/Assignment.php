<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'community_id',
        'start_date',
        'end_date',
        'role',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function community(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the duration of the assignment in days
     */
    public function getDurationAttribute(): int
    {
        $endDate = $this->end_date ?? now();

        return $this->start_date->diffInDays($endDate);
    }

    /**
     * Get the duration in human-readable format
     */
    public function getDurationHumanAttribute(): string
    {
        $days = $this->duration;

        if ($days < 30) {
            return $days.' '.str('day')->plural($days);
        }

        if ($days < 365) {
            $months = round($days / 30);

            return $months.' '.str('month')->plural($months);
        }

        $years = round($days / 365, 1);

        return $years.' '.str('year')->plural($years);
    }

    /**
     * Check if assignment is currently active
     */
    public function isActive(): bool
    {
        return $this->end_date === null || $this->end_date->isFuture();
    }

    /**
     * Scope to get active assignments
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
                ->orWhere('end_date', '>', now());
        });
    }

    /**
     * Scope to get completed assignments
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('end_date')
            ->where('end_date', '<=', now());
    }
}
