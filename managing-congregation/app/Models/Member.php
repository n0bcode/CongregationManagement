<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ScopedByCommunity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use HasFactory, ScopedByCommunity, SoftDeletes;

    protected $fillable = [
        'community_id',
        'first_name',
        'last_name',
        'religious_name',
        'email',
        'dob',
        'entry_date',
        'entry_date',
        'status',
        'profile_photo_path',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'dob' => 'date',
        'entry_date' => 'date',
    ];

    public function community(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function formationEvents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FormationEvent::class)->orderBy('started_at');
    }

    public function assignments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Assignment::class)->orderBy('start_date', 'desc');
    }

    public function currentAssignment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Assignment::class)->latestOfMany();
    }

    public function healthRecords(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(HealthRecord::class)->orderBy('recorded_at', 'desc');
    }

    public function skills(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Skill::class)->orderBy('category')->orderBy('name');
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->profile_photo_path)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->first_name.' '.$this->last_name).'&color=7F9CF5&background=EBF4FF';
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('religious_name', 'like', "%{$term}%")
                ->orWhere('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%")
                // Search by community name
                ->orWhereHas('community', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%");
                })
                // Search by formation stage
                ->orWhereHas('formationEvents', function ($q) use ($term) {
                    $q->where('stage', 'like', "%{$term}%");
                })
                // Search by skills
                ->orWhereHas('skills', function ($q) use ($term) {
                    $q->where('name', 'like', "%{$term}%")
                        ->orWhere('category', 'like', "%{$term}%");
                })
                // Search by health conditions
                ->orWhereHas('healthRecords', function ($q) use ($term) {
                    $q->where('condition', 'like', "%{$term}%");
                });
        });
    }

    /**
     * Scope to find members with upcoming birthdays.
     * Replaces CURDATE() with a parameter for testability.
     */
    public function scopeUpcomingBirthdays($query, $startDate = null, $days = 30)
    {
        $date = $startDate ? \Carbon\Carbon::parse($startDate) : now();
        $dateString = $date->format('Y-m-d');
        $endDateString = $date->copy()->addDays($days)->format('Y-m-d');

        return $query->whereRaw("
            DATE_ADD(dob, INTERVAL YEAR('{$dateString}')-YEAR(dob) + IF(DAYOFYEAR('{$dateString}') > DAYOFYEAR(dob),1,0) YEAR) 
            BETWEEN '{$dateString}' AND '{$endDateString}'
        ");
    }

    /**
     * Get complete timeline of member's history
     * Aggregates formation events, assignments, and transfers
     * Returns chronologically sorted collection
     */
    public function timeline(): \Illuminate\Support\Collection
    {
        $timeline = collect();

        // Add entry date
        if ($this->entry_date) {
            $timeline->push((object) [
                'type' => 'entry',
                'date' => $this->entry_date,
                'title' => 'Entered Congregation',
                'description' => 'Joined the congregation',
                'icon' => 'door-open',
                'color' => 'blue',
            ]);
        }

        // Add formation events
        foreach ($this->formationEvents as $event) {
            $timeline->push((object) [
                'type' => 'formation',
                'date' => $event->started_at,
                'title' => ucfirst(str_replace('_', ' ', $event->stage)),
                'description' => $event->notes ?? 'Formation milestone',
                'icon' => 'book',
                'color' => 'amber',
                'model' => $event,
            ]);
        }

        // Add assignments
        foreach ($this->assignments as $assignment) {
            $timeline->push((object) [
                'type' => 'assignment',
                'date' => $assignment->start_date,
                'title' => $assignment->role ?? 'Assignment',
                'description' => "Assigned to {$assignment->community->name}".
                    ($assignment->end_date ? " (ended {$assignment->end_date->format('M Y')})" : ' (ongoing)'),
                'icon' => 'briefcase',
                'color' => 'green',
                'model' => $assignment,
            ]);
        }

        // Sort chronologically (most recent first)
        return $timeline->sortByDesc('date')->values();
    }

    /**
     * Get timeline events for a specific date range
     */
    public function timelineForPeriod(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate): \Illuminate\Support\Collection
    {
        return $this->timeline()->filter(function ($event) use ($startDate, $endDate) {
            return $event->date->between($startDate, $endDate);
        });
    }

    /**
     * Get timeline events by type
     */
    public function timelineByType(string $type): \Illuminate\Support\Collection
    {
        return $this->timeline()->where('type', $type);
    }
}
