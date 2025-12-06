<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ScopedByCommunity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, ScopedByCommunity;

    protected $fillable = [
        'community_id',
        'category',
        'amount',
        'date',
        'description',
        'receipt_path',
        'created_by',
        'is_locked',
        'locked_at',
        'locked_by',
        'project_id', // Added project_id to fillable
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
    ];

    /**
     * Get the community that owns the expense.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the project that the expense belongs to.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get the user who created the expense.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who locked the expense.
     */
    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Scope to filter by category.
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter locked expenses.
     */
    public function scopeLocked($query)
    {
        return $query->where('is_locked', true);
    }

    /**
     * Scope to filter unlocked expenses.
     */
    public function scopeUnlocked($query)
    {
        return $query->where('is_locked', false);
    }

    /**
     * Get the amount in dollars (formatted).
     */
    public function getAmountInDollarsAttribute(): float
    {
        return $this->amount / 100;
    }

    /**
     * Set the amount from dollars (convert to cents).
     */
    public function setAmountInDollarsAttribute(float $value): void
    {
        $this->attributes['amount'] = (int) ($value * 100);
    }
}
