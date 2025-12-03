<?php

declare(strict_types=1);

namespace App\Enums;

enum MemberStatus: string
{
    case Active = 'active';
    case Deceased = 'deceased';
    case Exited = 'exited';
    case Transferred = 'transferred';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Deceased => 'Deceased',
            self::Exited => 'Exited',
            self::Transferred => 'Transferred',
        };
    }
}
