<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\FormationStage;
use App\Models\FormationEvent;
use App\Models\Member;
use Carbon\Carbon;

class FormationService
{
    public const NOVITIATE_MIN_MONTHS = 12;

    public const FIRST_VOWS_MIN_MONTHS = 36;

    public function calculateNextStageDate(FormationStage $currentStage, Carbon $startDate): ?Carbon
    {
        return match ($currentStage) {
            FormationStage::Novitiate => $startDate->copy()->addMonths(self::NOVITIATE_MIN_MONTHS),
            FormationStage::FirstVows => $startDate->copy()->addMonths(self::FIRST_VOWS_MIN_MONTHS),
            default => null,
        };
    }

    public function addEvent(Member $member, array $data): FormationEvent
    {
        return FormationEvent::create([
            'member_id' => $member->id,
            'stage' => $data['stage'],
            'started_at' => $data['started_at'],
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
