<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Member;
use App\Services\AuditService;

class MemberAuditObserver
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Handle the Member "created" event.
     */
    public function created(Member $member): void
    {
        $this->auditService->log(
            model: $member,
            action: 'created',
            oldValues: null,
            newValues: $member->getAttributes()
        );
    }

    /**
     * Handle the Member "updated" event.
     */
    public function updated(Member $member): void
    {
        // Get the original (old) values before the update
        $oldValues = $member->getOriginal();

        // Get the new values after the update
        $newValues = $member->getAttributes();

        // Only log if there are actual changes
        if ($oldValues !== $newValues) {
            $this->auditService->log(
                model: $member,
                action: 'updated',
                oldValues: $oldValues,
                newValues: $newValues
            );
        }
    }

    /**
     * Handle the Member "deleted" event.
     */
    public function deleted(Member $member): void
    {
        $this->auditService->log(
            model: $member,
            action: 'deleted',
            oldValues: $member->getAttributes(),
            newValues: null
        );
    }

    /**
     * Handle the Member "restored" event.
     */
    public function restored(Member $member): void
    {
        $this->auditService->log(
            model: $member,
            action: 'restored',
            oldValues: null,
            newValues: $member->getAttributes()
        );
    }

    /**
     * Handle the Member "force deleted" event.
     */
    public function forceDeleted(Member $member): void
    {
        $this->auditService->log(
            model: $member,
            action: 'force_deleted',
            oldValues: $member->getAttributes(),
            newValues: null
        );
    }
}
