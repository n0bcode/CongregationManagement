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
        'status'
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

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($query) use ($term) {
            $query->where('religious_name', 'like', "%{$term}%")
                  ->orWhere('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%");
        });
    }
}
