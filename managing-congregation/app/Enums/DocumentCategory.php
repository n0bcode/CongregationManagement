<?php

namespace App\Enums;

enum DocumentCategory: string
{
    case APPOINTMENT = 'appointment';
    case TRANSFER = 'transfer';
    case VOWS = 'vows';
    case INTRODUCTION_LETTER = 'introduction_letter';
    case INTERNAL = 'internal';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::APPOINTMENT => 'Appointment',
            self::TRANSFER => 'Transfer',
            self::VOWS => 'Vows',
            self::INTRODUCTION_LETTER => 'Introduction Letter',
            self::INTERNAL => 'Internal',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::APPOINTMENT => 'Official appointment documents',
            self::TRANSFER => 'Transfer and assignment documents',
            self::VOWS => 'Vow-related documents',
            self::INTRODUCTION_LETTER => 'Introduction and recommendation letters',
            self::INTERNAL => 'Internal congregation documents',
            self::OTHER => 'Other documents',
        };
    }
}
