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
}
