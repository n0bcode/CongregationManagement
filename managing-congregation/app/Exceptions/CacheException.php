<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class CacheException extends Exception
{
    public function __construct(string $operation, ?Throwable $previous = null)
    {
        parent::__construct("Cache operation '{$operation}' failed", 0, $previous);
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // Log the exception for monitoring
        \Log::error('Cache operation failed', [
            'message' => $this->getMessage(),
            'previous' => $this->getPrevious()?->getMessage(),
            'trace' => $this->getTraceAsString(),
        ]);
    }
}
