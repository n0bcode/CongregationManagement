<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Display the dashboard
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        // Get community ID based on user role
        $communityId = $user->role === \App\Enums\UserRole::DIRECTOR ? $user->community_id : null;

        // Get upcoming reminders (next 30 days)
        $upcomingReminders = $this->notificationService->getUpcomingReminders(30, $communityId);

        // Get overdue reminders
        $overdueReminders = $this->notificationService->getOverdueReminders($communityId);

        // Get member statistics
        $activeMembersCount = Member::where('status', 'active')
            ->when($communityId, fn ($q) => $q->where('community_id', $communityId))
            ->count();

        $needsAttentionCount = $overdueReminders->count();

        // Get recent activity
        $recentActivity = \App\Models\AuditLog::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'upcomingReminders',
            'overdueReminders',
            'activeMembersCount',
            'needsAttentionCount',
            'recentActivity'
        ));
    }
}
