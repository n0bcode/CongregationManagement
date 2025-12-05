<?php

namespace App\Rules;

use App\Models\Member;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AssignmentOverlapRule implements ValidationRule
{
    public function __construct(
        protected Member $member,
        protected ?string $endDate = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $newStart = \Carbon\Carbon::parse($value);
        $newEnd = $this->endDate ? \Carbon\Carbon::parse($this->endDate) : null;

        $assignments = $this->member->assignments()->get();

        foreach ($assignments as $assignment) {
            /** @var \App\Models\Assignment $assignment */
            $existingStart = \Carbon\Carbon::parse($assignment->start_date);
            $existingEnd = $assignment->end_date ? \Carbon\Carbon::parse($assignment->end_date) : null;

            // Check overlap
            // Overlap if (StartA <= EndB) and (EndA >= StartB)
            // Treat null end date as infinity

            $startA = $newStart;
            $endA = $newEnd;
            $startB = $existingStart;
            $endB = $existingEnd;

            $overlap = true;

            if ($endA && $endA->lt($startB)) {
                $overlap = false;
            }
            if ($endB && $endB->lt($startA)) {
                $overlap = false;
            }

            if ($overlap) {
                $fail('The assignment dates overlap with an existing assignment.');

                return;
            }
        }
    }
}
