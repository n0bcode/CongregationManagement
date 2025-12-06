<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodicEvent extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'recurrence',
        'community_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }
}
