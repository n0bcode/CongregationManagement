<?php

namespace App\View\Components\Widgets;

use App\Contracts\DashboardWidget;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

abstract class BaseWidget extends Component implements DashboardWidget
{
    public function __construct(
        public User $user
    ) {}

    abstract public function render(): View;

    public function getData(): array
    {
        return [];
    }

    public function canView(User $user): bool
    {
        return true;
    }

    public function getRefreshInterval(): int
    {
        return 300; // 5 minutes default
    }
}
