<?php

declare(strict_types=1);

namespace App\Enums;

enum SkillCategory: string
{
    case Pastoral = 'pastoral';
    case Practical = 'practical';
    case Special = 'special';

    public function label(): string
    {
        return match ($this) {
            self::Pastoral => 'Pastoral Ministry',
            self::Practical => 'Practical Skills',
            self::Special => 'Special Abilities',
        };
    }
}
