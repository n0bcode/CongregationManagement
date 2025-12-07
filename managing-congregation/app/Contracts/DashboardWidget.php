<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Contracts\View\View;

interface DashboardWidget
{
    /**
     * Render the widget view.
     */
    public function render(): View;

    /**
     * Get the data for the widget.
     */
    public function getData(): array;

    /**
     * Determine if the user can view the widget.
     */
    public function canView(User $user): bool;

    /**
     * Get the refresh interval in seconds.
     */
    public function getRefreshInterval(): int;
}
