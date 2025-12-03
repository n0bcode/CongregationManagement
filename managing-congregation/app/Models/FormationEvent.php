<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\FormationStage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'stage',
        'started_at',
        'notes',
    ];

    protected $casts = [
        'stage' => FormationStage::class,
        'started_at' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
