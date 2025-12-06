<?php

declare(strict_types=1);

namespace App\Enums;

enum FormationStage: string
{
    case Aspirant = 'aspirant';
    case Postulancy = 'postulancy';
    case Novitiate = 'novitiate';
    case FirstVows = 'first_vows';
    case FinalVows = 'final_vows';

    public function label(): string
    {
        return match ($this) {
            self::Aspirant => 'Aspirant',
            self::Postulancy => 'Postulancy',
            self::Novitiate => 'Novitiate',
            self::FirstVows => 'First Vows',
            self::FinalVows => 'Final Vows',
        };
    }
}
