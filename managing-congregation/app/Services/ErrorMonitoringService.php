<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Error Monitoring Service
 *
 * Provides integration points for external monitoring services
 * (e.g., Sentry, Bugsnag, New Relic, etc.)
 */
class ErrorMonitoringService
{
    /**
     * Report a critical RBAC error to monitoring service
     */
    public function reportCriticalError(string $context, \Throwable $exception, array $metadata = []): void
    {
        $errorData = [
            'context' => $context,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'metadata' => $metadata,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'timestamp' => now()->toIso8601String(),
        ];

        // Log locally
        Log::critical("RBAC Critical Error: {$context}", $errorData);

        // Integration point for external monitoring services
        // Uncomment and configure based on your monitoring service:

        // Sentry example:
        // if (app()->bound('sentry')) {
        //     app('sentry')->captureException($exception, [
        //         'extra' => $errorData,
        //         'level' => 'critical',
        //     ]);
        // }

        // Bugsnag example:
        // if (app()->bound('bugsnag')) {
        //     app('bugsnag')->notifyException($exception, function ($report) use ($errorData) {
        //         $report->setSeverity('error');
        //         $report->setMetaData($errorData);
        //     });
        // }

        // Custom webhook example:
        // $this->sendToWebhook($errorData);
    }

    /**
     * Report a security event to monitoring service
     */
    public function reportSecurityEvent(string $event, array $context = []): void
    {
        $eventData = [
            'event' => $event,
            'context' => $context,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        // Log locally
        Log::warning("RBAC Security Event: {$event}", $eventData);

        // Integration point for security monitoring
        // Example: Send to SIEM system, security dashboard, etc.
    }

    /**
     * Report cache performance metrics
     */
    public function reportCacheMetrics(array $metrics): void
    {
        // Log metrics
        Log::info('RBAC Cache Metrics', $metrics);

        // Integration point for metrics services
        // Example: StatsD, Prometheus, CloudWatch, etc.
        // $this->sendToMetricsService($metrics);
    }

    /**
     * Report permission check performance
     */
    public function reportPerformanceMetric(string $operation, float $duration, array $context = []): void
    {
        $metricData = [
            'operation' => $operation,
            'duration_ms' => round($duration * 1000, 2),
            'context' => $context,
            'timestamp' => now()->toIso8601String(),
        ];

        // Log if slow
        if ($duration > 0.1) { // 100ms threshold
            Log::warning('Slow RBAC operation detected', $metricData);
        } else {
            Log::debug('RBAC operation performance', $metricData);
        }

        // Integration point for APM services
        // Example: New Relic, DataDog APM, etc.
    }

    /**
     * Check system health and report issues
     */
    public function healthCheck(): array
    {
        $health = [
            'status' => 'healthy',
            'checks' => [],
            'timestamp' => now()->toIso8601String(),
        ];

        // Check cache connectivity
        try {
            $cacheManager = app(\App\Contracts\CacheManagerInterface::class);
            $stats = $cacheManager->getCacheStats();
            $health['checks']['cache'] = [
                'status' => 'ok',
                'stats' => $stats,
            ];
        } catch (\Throwable $e) {
            $health['status'] = 'degraded';
            $health['checks']['cache'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        // Check database connectivity
        try {
            \DB::connection()->getPdo();
            $health['checks']['database'] = ['status' => 'ok'];
        } catch (\Throwable $e) {
            $health['status'] = 'unhealthy';
            $health['checks']['database'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        // Check audit log system
        try {
            $recentLogs = \App\Models\AuditLog::latest()->limit(1)->count();
            $health['checks']['audit_log'] = [
                'status' => 'ok',
                'recent_logs' => $recentLogs,
            ];
        } catch (\Throwable $e) {
            $health['status'] = 'degraded';
            $health['checks']['audit_log'] = [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        return $health;
    }

    /**
     * Send alert for critical issues
     */
    private function sendAlert(string $message, array $context = []): void
    {
        // Integration point for alerting services
        // Example: PagerDuty, Slack, email, SMS, etc.

        Log::alert($message, $context);

        // Slack example:
        // if (config('services.slack.webhook_url')) {
        //     Http::post(config('services.slack.webhook_url'), [
        //         'text' => $message,
        //         'attachments' => [
        //             ['text' => json_encode($context, JSON_PRETTY_PRINT)],
        //         ],
        //     ]);
        // }
    }
}
