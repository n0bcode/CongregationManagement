<?php

declare(strict_types=1);

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

class AuditObserver
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    public function created(Model $model): void
    {
        $this->auditService->log(
            model: $model,
            action: 'created',
            oldValues: null,
            newValues: $model->getAttributes()
        );
    }

    public function updated(Model $model): void
    {
        $oldValues = $model->getOriginal();
        $newValues = $model->getAttributes();

        if ($oldValues !== $newValues) {
            $this->auditService->log(
                model: $model,
                action: 'updated',
                oldValues: $oldValues,
                newValues: $newValues
            );
        }
    }

    public function deleted(Model $model): void
    {
        $this->auditService->log(
            model: $model,
            action: 'deleted',
            oldValues: $model->getAttributes(),
            newValues: null
        );
    }

    public function restored(Model $model): void
    {
        $this->auditService->log(
            model: $model,
            action: 'restored',
            oldValues: null,
            newValues: $model->getAttributes()
        );
    }

    public function forceDeleted(Model $model): void
    {
        $this->auditService->log(
            model: $model,
            action: 'force_deleted',
            oldValues: $model->getAttributes(),
            newValues: null
        );
    }
}
