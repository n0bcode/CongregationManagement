<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class AuditLogException extends Exception
{
    public function __construct(string $action, ?Throwable $previous = null)
    {
        parent::__construct("Failed to log audit action '{$action}'", 0, $previous);
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // Log the exception - this is critical for security
        \Log::critical('Audit logging failed', [
            'message' => $this->getMessage(),
            'previous' => $this->getPrevious()?->getMessage(),
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
