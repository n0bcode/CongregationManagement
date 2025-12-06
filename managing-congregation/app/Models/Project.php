<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'community_id',
        'manager_id',
        'start_date',
        'end_date',
        'status',
        'budget',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function manager()
    {
        return $this->belongsTo(Member::class, 'manager_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
