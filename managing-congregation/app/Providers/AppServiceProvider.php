<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Member;
use App\Observers\MemberAuditObserver;
use App\Services\PermissionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PermissionService::class);

        // Register CacheManager as singleton
        $this->app->singleton(
            \App\Contracts\CacheManagerInterface::class,
            \App\Services\CacheManager::class
        );

        // Register AuditLogger as singleton
        $this->app->singleton(
            \App\Contracts\AuditLoggerInterface::class,
            \App\Services\AuditLogger::class
        );

        // Register RouteScanner as singleton
        $this->app->singleton(
            \App\Contracts\RouteScannerInterface::class,
            \App\Services\RouteScanner::class
        );

        // Register UI/UX Optimization Services
        $this->app->singleton(\App\Services\DashboardService::class);
        $this->app->singleton(\App\Services\SmartDefaultsService::class);
        $this->app->singleton(\App\Services\GlobalSearchService::class);
        $this->app->singleton(\App\Services\ContextualActionService::class);
        $this->app->singleton(\App\Services\ChartService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Dashboard Widgets
        $dashboardService = $this->app->make(\App\Services\DashboardService::class);
        $dashboardService->registerWidget('member_stats', \App\View\Components\Widgets\MemberStatsWidget::class);
        $dashboardService->registerWidget('financial_summary', \App\View\Components\Widgets\FinancialSummaryWidget::class);
        $dashboardService->registerWidget('upcoming_events', \App\View\Components\Widgets\UpcomingEventsWidget::class);
        $dashboardService->registerWidget('recent_activity', \App\View\Components\Widgets\RecentActivityWidget::class);
        $dashboardService->registerWidget('quick_actions', \App\View\Components\Widgets\QuickActionsWidget::class);

        // Register observers
        Member::observe(MemberAuditObserver::class);
        \App\Models\Project::observe(\App\Observers\AuditObserver::class);
        \App\Models\PeriodicEvent::observe(\App\Observers\AuditObserver::class);

        // Define view-admin gate
        Gate::define('view-admin', function ($user) {
            return $user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::GENERAL);
        });

        // Register UserPolicy for automatic discovery
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
        Gate::policy(\App\Models\FormationEvent::class, \App\Policies\FormationPolicy::class);
        Gate::policy(\App\Models\FormationDocument::class, \App\Policies\FormationPolicy::class);
        Gate::policy(\App\Models\AuditLog::class, \App\Policies\AuditLogPolicy::class);
        Gate::policy(\App\Models\Expense::class, \App\Policies\ExpensePolicy::class);
        Gate::policy(\App\Models\Document::class, \App\Policies\DocumentPolicy::class);

        // Register Contextual Actions
        $actionService = $this->app->make(\App\Services\ContextualActionService::class);
        
        $actionService->register(\App\Models\Member::class, function ($member) {
            $actions = [];

            if (auth()->user()->can('update', $member)) {
                $actions[] = \App\ValueObjects\ContextualAction::make('Edit', route('members.edit', $member))
                    ->icon('pencil')
                    ->variant('secondary');
            }

            if (auth()->user()->can('delete', $member)) {
                $actions[] = \App\ValueObjects\ContextualAction::make('Delete', route('members.destroy', $member))
                    ->method('DELETE')
                    ->icon('trash')
                    ->variant('danger')
                    ->confirm('Are you sure you want to delete this member?');
            }

            return $actions;
        });
    }
}
