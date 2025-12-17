<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'title',
        'description',
    ];

    /**
     * Get all assignments with this role.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Get members with this role through assignments.
     */
    public function members()
    {
        return $this->hasManyThrough(
            Member::class,
            Assignment::class,
            'role_id',      // Foreign key on assignments table
            'id',           // Foreign key on members table
            'id',           // Local key on roles table
            'member_id'     // Local key on assignments table
        );
    }

    /**
     * Scope to get role by code.
     */
    public function scopeByCode($query, string $code)
    {
        return $query->where('code', $code);
    }

    /**
     * Get the full role name with code.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->code} - {$this->title}";
    }
}
