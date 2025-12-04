<?php

declare(strict_types=1);

namespace App\Exceptions;

use App\Enums\UserRole;
use Exception;
use Throwable;

class PermissionUpdateException extends Exception
{
    public function __construct(UserRole $role, ?Throwable $previous = null)
    {
        parent::__construct(
            "Failed to update permissions for role '{$role->value}'",
            0,
            $previous
        );
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // Log the exception with full context
        \Log::error('Permission update failed', [
            'message' => $this->getMessage(),
            'previous' => $this->getPrevious()?->getMessage(),
            'user_id' => auth()->id(),
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
