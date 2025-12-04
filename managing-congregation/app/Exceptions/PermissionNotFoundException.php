<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class PermissionNotFoundException extends Exception
{
    public function __construct(string $permissionKey)
    {
        parent::__construct("Permission '{$permissionKey}' not found in system");
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // Log the exception for monitoring
        \Log::warning('Permission not found', [
            'message' => $this->getMessage(),
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
