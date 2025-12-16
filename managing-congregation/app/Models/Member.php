<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\ScopedByCommunity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $community_id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $religious_name
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $dob
 * @property \Illuminate\Support\Carbon|null $entry_date
 * @property \App\Enums\MemberStatus $status
 * @property string|null $profile_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Community|null $community
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\FormationEvent[] $formationEvents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Assignment[] $assignments
 * @property-read \App\Models\Assignment|null $currentAssignment
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HealthRecord[] $healthRecords
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Skill[] $skills
 * @property int|null $count
 */
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
        'passport_number',
        'passport_issued_at',
        'passport_expired_at',
        'passport_place_of_issue',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected $casts = [
        'dob' => 'date',
        'entry_date' => 'date',
        'passport_issued_at' => 'date',
        'passport_expired_at' => 'date',
        'status' => \App\Casts\CaseInsensitiveMemberStatus::class,
    ];

    public function community(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    public function projects(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('id', 'role', 'status')
            ->withTimestamps();
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

    public function ordinations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Ordination::class)->orderBy('date');
    }

    public function educations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Education::class)->orderByDesc('end_year');
    }

    public function emergencyContacts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo_path) {
            return 'https://ui-avatars.com/api/?name='.urlencode($this->first_name.' '.$this->last_name).'&color=7F9CF5&background=EBF4FF';
        }

        $url = \Illuminate\Support\Facades\Storage::disk('public')->url($this->profile_photo_path);
        
        // Fix for server configuration where public/ is needed in URL
        if (!str_contains($url, '/public/storage/')) {
            $url = str_replace('/storage/', '/public/storage/', $url);
        }

        return $url;
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Search members by various fields.
     * Escapes SQL wildcards to prevent unintended matches.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $term Search term
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, $term)
    {
        // Escape SQL wildcards (% and _) to treat them as literal characters
        $escapedTerm = addcslashes($term, '%_');
        
        return $query->where(function ($query) use ($escapedTerm) {
            $query->where('religious_name', 'like', "%{$escapedTerm}%")
                ->orWhere('first_name', 'like', "%{$escapedTerm}%")
                ->orWhere('last_name', 'like', "%{$escapedTerm}%")
                ->orWhere('status', 'like', "%{$escapedTerm}%")
                ->orWhereHas('community', function ($q) use ($escapedTerm) {
                    $q->where('name', 'like', "%{$escapedTerm}%");
                })
                // Search by passport number
                ->orWhere('passport_number', 'like', "%{$escapedTerm}%")
                // Search by formation stage
                ->orWhereHas('formationEvents', function ($q) use ($escapedTerm) {
                    $q->where('stage', 'like', "%{$escapedTerm}%");
                })
                // Search by skills
                ->orWhereHas('skills', function ($q) use ($escapedTerm) {
                    $q->where('name', 'like', "%{$escapedTerm}%")
                        ->orWhere('category', 'like', "%{$escapedTerm}%");
                })
                // Search by health conditions
                ->orWhereHas('healthRecords', function ($q) use ($escapedTerm) {
                    $q->where('condition', 'like', "%{$escapedTerm}%");
                });
        });
    }

    /**
     * Scope to find members with upcoming birthdays.
     * Uses parameter binding to prevent SQL injection.
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $startDate Optional start date (Y-m-d format)
     * @param int $days Number of days to look ahead (default: 30)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUpcomingBirthdays($query, $startDate = null, $days = 30)
    {
        $date = $startDate ? \Carbon\Carbon::parse($startDate) : now();
        $dateString = $date->format('Y-m-d');
        $endDateString = $date->copy()->addDays($days)->format('Y-m-d');

        // Use parameter binding (?) instead of string interpolation to prevent SQL injection
        return $query->whereRaw("
            DATE_ADD(dob, INTERVAL YEAR(?)-YEAR(dob) + IF(DAYOFYEAR(?) > DAYOFYEAR(dob),1,0) YEAR) 
            BETWEEN ? AND ?
        ", [$dateString, $dateString, $dateString, $endDateString]);
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
                'title' => ucfirst(str_replace('_', ' ', $event->stage->value)),
                'description' => $event->notes ?? 'Formation milestone',
                'icon' => 'book',
                'color' => 'amber',
                'model' => $event,
            ]);
        }

        // Add assignments
        foreach ($this->assignments as $assignment) {
            /** @var \App\Models\Assignment $assignment */
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

    public function audits(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(AuditLog::class, 'auditable')->orderByDesc('created_at');
    }
}
