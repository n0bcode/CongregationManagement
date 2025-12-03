<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SkillCategory;
use App\Enums\SkillProficiency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Skill extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'category',
        'name',
        'proficiency',
        'notes',
    ];

    protected $casts = [
        'category' => SkillCategory::class,
        'proficiency' => SkillProficiency::class,
    ];

    /**
     * Get the member that owns the skill.
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
