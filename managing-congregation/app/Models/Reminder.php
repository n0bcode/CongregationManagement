<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'description',
        'reminder_date',
        'member_id',
        'community_id',
        'is_sent',
        'sent_at',
        'created_by',
    ];

    protected $casts = [
        'reminder_date' => 'date',
        'is_sent' => 'boolean',
        'sent_at' => 'datetime',
    ];

    /**
     * Get the member this reminder is for
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the community this reminder is for
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the user who created this reminder
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get pending reminders (not sent yet)
     */
    public function scopePending($query)
    {
        return $query->where('is_sent', false);
    }

    /**
     * Scope to get sent reminders
     */
    public function scopeSent($query)
    {
        return $query->where('is_sent', true);
    }

    /**
     * Scope to get reminders due today or earlier
     */
    public function scopeDue($query)
    {
        return $query->where('reminder_date', '<=', now()->toDateString());
    }

    /**
     * Scope to get upcoming reminders (within X days)
     */
    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->whereBetween('reminder_date', [
            now()->toDateString(),
            now()->addDays($days)->toDateString(),
        ]);
    }

    /**
     * Scope to filter by community
     */
    public function scopeForCommunity($query, ?int $communityId)
    {
        if ($communityId) {
            return $query->where('community_id', $communityId);
        }

        return $query;
    }

    /**
     * Scope to filter by member
     */
    public function scopeForMember($query, ?int $memberId)
    {
        if ($memberId) {
            return $query->where('member_id', $memberId);
        }

        return $query;
    }

    /**
     * Mark reminder as sent
     */
    public function markAsSent(): void
    {
        $this->update([
            'is_sent' => true,
            'sent_at' => now(),
        ]);
    }

    /**
     * Check if reminder is overdue
     */
    public function isOverdue(): bool
    {
        return ! $this->is_sent && $this->reminder_date->isPast();
    }

    /**
     * Get days until reminder
     */
    public function getDaysUntilAttribute(): int
    {
        return (int) now()->diffInDays($this->reminder_date, false);
    }
}
