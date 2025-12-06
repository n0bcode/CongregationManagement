<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyContact extends Model
{
    protected $fillable = [
        'member_id',
        'name',
        'relationship',
        'phone',
        'email',
        'address',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
