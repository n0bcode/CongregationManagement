<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilterPreset extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'context',
        'filters',
        'is_public',
    ];

    protected $casts = [
        'filters' => 'array',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
