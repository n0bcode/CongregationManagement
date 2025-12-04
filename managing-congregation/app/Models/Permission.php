<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'key',
        'name',
        'module',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
