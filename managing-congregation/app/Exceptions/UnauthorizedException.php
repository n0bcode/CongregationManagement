<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UnauthorizedException extends Exception
{
    public function __construct(string $action, string $resource)
    {
        parent::__construct("Unauthorized to perform '{$action}' on '{$resource}'");
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        // Log the exception for security monitoring
        \Log::warning('Unauthorized access attempt', [
            'message' => $this->getMessage(),
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthorized',
                'error' => $this->getMessage(),
            ], 403);
        }

        return response()->view('errors.403', [
            'message' => $this->getMessage(),
        ], 403);
    }
}
