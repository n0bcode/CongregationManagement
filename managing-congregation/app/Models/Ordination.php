<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ordination extends Model
{
    protected $fillable = [
        'member_id',
        'step',
        'date',
        'place',
        'bishop_name',
        'certificate_path',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
