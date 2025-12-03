<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditService
{
    /**
     * Log an audit event.
     *
     * @param  Model  $model  The model being audited
     * @param  string  $action  The action performed (created, updated, deleted, etc.)
     * @param  array|null  $oldValues  Previous state of the model
     * @param  array|null  $newValues  New state of the model
     * @param  string|null  $description  Human-readable description
     */
    public function log(
        Model $model,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description ?? $this->generateDescription($model, $action),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Generate a human-readable description for the audit log.
     */
    protected function generateDescription(Model $model, string $action): string
    {
        $modelName = class_basename($model);
        $identifier = $this->getModelIdentifier($model);

        return match ($action) {
            'created' => "{$modelName} '{$identifier}' was created",
            'updated' => "{$modelName} '{$identifier}' was updated",
            'deleted' => "{$modelName} '{$identifier}' was deleted",
            'transferred' => "{$modelName} '{$identifier}' was transferred",
            default => "{$modelName} '{$identifier}' - {$action}",
        };
    }

    /**
     * Get a human-readable identifier for the model.
     */
    protected function getModelIdentifier(Model $model): string
    {
        // Try common identifier fields
        if (isset($model->name)) {
            return $model->name;
        }

        if (isset($model->first_name) && isset($model->last_name)) {
            return "{$model->first_name} {$model->last_name}";
        }

        if (isset($model->title)) {
            return $model->title;
        }

        return "ID: {$model->id}";
    }

    /**
     * Generate a tamper-evident report of audit logs.
     *
     * This creates a report with checksums to verify data integrity.
     *
     * @param  \Illuminate\Support\Carbon|null  $startDate
     * @param  \Illuminate\Support\Carbon|null  $endDate
     */
    public function generateTamperEvidentReport($startDate = null, $endDate = null): array
    {
        $query = AuditLog::with('user');

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $logs = $query->orderBy('created_at')->get();

        // Generate checksums for each log entry
        $entries = $logs->map(function ($log) {
            $data = [
                'id' => $log->id,
                'user_id' => $log->user_id,
                'action' => $log->action,
                'auditable_type' => $log->auditable_type,
                'auditable_id' => $log->auditable_id,
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
                'created_at' => $log->created_at->toIso8601String(),
            ];

            return [
                'log' => $log,
                'checksum' => hash('sha256', json_encode($data)),
            ];
        });

        // Generate overall report checksum
        $reportData = $entries->pluck('checksum')->join('');
        $reportChecksum = hash('sha256', $reportData);

        return [
            'entries' => $entries,
            'total_count' => $logs->count(),
            'start_date' => $startDate?->toDateString(),
            'end_date' => $endDate?->toDateString(),
            'generated_at' => now()->toIso8601String(),
            'report_checksum' => $reportChecksum,
        ];
    }

    /**
     * Verify the integrity of an audit log report.
     *
     * @param  array  $report  The report to verify
     */
    public function verifyReportIntegrity(array $report): bool
    {
        $checksums = collect($report['entries'])->pluck('checksum')->join('');
        $calculatedChecksum = hash('sha256', $checksums);

        return $calculatedChecksum === $report['report_checksum'];
    }
}
