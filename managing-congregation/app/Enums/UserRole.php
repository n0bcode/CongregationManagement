<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case GENERAL = 'general';
    case DIRECTOR = 'director';
    case TREASURER = 'treasurer';
    case MEMBER = 'member';

    public function label(): string
    {
        return match ($this) {
            self::SUPER_ADMIN => 'Super Admin',
            self::GENERAL => 'General',
            self::DIRECTOR => 'Director',
            self::TREASURER => 'Treasurer',
            self::MEMBER => 'Member',
        };
    }
}
