<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $fillable = [
        'member_id',
        'degree',
        'major',
        'school',
        'start_year',
        'end_year',
        'is_graduated',
        'certificate_path',
    ];

    protected $casts = [
        'is_graduated' => 'boolean',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
