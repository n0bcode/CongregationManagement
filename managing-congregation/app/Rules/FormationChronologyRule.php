<?php

namespace App\Rules;

use App\Enums\FormationStage;
use App\Models\Member;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FormationChronologyRule implements ValidationRule
{
    public function __construct(
        protected Member $member,
        protected ?string $stage
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->stage) {
            return;
        }

        $newDate = \Carbon\Carbon::parse($value);
        $newStage = FormationStage::tryFrom($this->stage);

        if (! $newStage) {
            return;
        }

        $events = $this->member->formationEvents()->get();

        // Define order
        $order = [
            FormationStage::Postulancy->value => 1,
            FormationStage::Novitiate->value => 2,
            FormationStage::FirstVows->value => 3,
            FormationStage::FinalVows->value => 4,
        ];

        $currentOrder = $order[$newStage->value];

        foreach ($events as $event) {
            /** @var \App\Models\FormationEvent $event */
            $eventStage = $event->stage;
            $eventOrder = $order[$eventStage->value];
            $eventDate = \Carbon\Carbon::parse($event->started_at);

            // If existing event is earlier stage, its date must be <= new date
            if ($eventOrder < $currentOrder) {
                if ($eventDate->gt($newDate)) {
                    $fail("The date must be after the {$eventStage->label()} date ({$eventDate->format('Y-m-d')}).");
                }
            }

            // If existing event is later stage, its date must be >= new date
            if ($eventOrder > $currentOrder) {
                if ($eventDate->lt($newDate)) {
                    $fail("The date must be before the {$eventStage->label()} date ({$eventDate->format('Y-m-d')}).");
                }
            }
        }

        // Also check entry_date from member model if it exists
        if ($this->member->entry_date) {
            $entryDate = \Carbon\Carbon::parse($this->member->entry_date);
            if ($entryDate->gt($newDate)) {
                $fail("The date must be after the entry date ({$entryDate->format('Y-m-d')}).");
            }
        }
    }
}
