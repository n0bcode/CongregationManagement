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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register observers
        Member::observe(MemberAuditObserver::class);

        // Define view-admin gate
        Gate::define('view-admin', function ($user) {
            return $user->hasRole(UserRole::SUPER_ADMIN) || $user->hasRole(UserRole::GENERAL);
        });

        // Register UserPolicy for automatic discovery
        Gate::policy(\App\Models\User::class, \App\Policies\UserPolicy::class);
        Gate::policy(\App\Models\FormationEvent::class, \App\Policies\FormationPolicy::class);
        Gate::policy(\App\Models\FormationDocument::class, \App\Policies\FormationPolicy::class);
        Gate::policy(\App\Models\AuditLog::class, \App\Policies\AuditLogPolicy::class);
    }
}
