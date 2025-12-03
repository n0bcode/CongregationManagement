<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Display a listing of audit logs with filtering.
     */
    public function index(Request $request): View
    {
        // Authorization: Only Super Admin and General can view audit logs
        $this->authorize('viewAny', AuditLog::class);

        $query = AuditLog::with(['user', 'auditable'])
            ->orderBy('created_at', 'desc');

        // Filter by user
        if ($request->filled('user_id')) {
            $query->byUser($request->user_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->action($request->action);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by auditable type
        if ($request->filled('auditable_type')) {
            $query->where('auditable_type', $request->auditable_type);
        }

        $logs = $query->paginate(50);

        // Get unique users for filter dropdown
        $users = User::orderBy('name')->get();

        // Get unique actions for filter dropdown
        $actions = AuditLog::distinct()->pluck('action')->sort();

        // Get unique auditable types for filter dropdown
        $auditableTypes = AuditLog::distinct()
            ->pluck('auditable_type')
            ->map(fn ($type) => class_basename($type))
            ->sort();

        return view('audit-logs.index', compact('logs', 'users', 'actions', 'auditableTypes'));
    }

    /**
     * Export audit logs as a tamper-evident PDF report.
     */
    public function export(Request $request): Response
    {
        // Authorization
        $this->authorize('viewAny', AuditLog::class);

        // Generate tamper-evident report
        $startDate = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date) : null;
        $endDate = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date) : null;

        $report = $this->auditService->generateTamperEvidentReport($startDate, $endDate);

        // Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('audit-logs.export', compact('report'));

        return $pdf->download('audit-log-report-'.now()->format('Y-m-d-His').'.pdf');
    }
}
