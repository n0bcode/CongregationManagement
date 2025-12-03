<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Concerns\ScopedByCommunity;

class Member extends Model
{
    use HasFactory, SoftDeletes, ScopedByCommunity;

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

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function formationEvents()
    {
        return $this->hasMany(FormationEvent::class)->orderBy('started_at');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class)->orderBy('start_date', 'desc');
    }

    public function currentAssignment()
    {
        return $this->hasOne(Assignment::class)->latestOfMany();
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
                  ->orWhere('last_name', 'like', "%{$term}%");
        });
    }
}
