<?php

namespace App\Services;

use App\Contracts\DashboardWidget;
use App\Models\User;
use Illuminate\Support\Collection;

class DashboardService
{
    /**
     * The registered widgets.
     *
     * @var array<string, string>
     */
    protected array $widgets = [];

    /**
     * Register a widget class.
     *
     * @param string $key
     * @param class-string<DashboardWidget> $widgetClass
     * @return void
     */
    public function registerWidget(string $key, string $widgetClass): void
    {
        $this->widgets[$key] = $widgetClass;
    }

    /**
     * Get the available widgets for a user.
     *
     * @param User $user
     * @return Collection<int, DashboardWidget>
     */
    public function getWidgetsForUser(User $user): Collection
    {
        $preferences = \App\Models\DashboardWidget::where('user_id', $user->id)
            ->get()
            ->keyBy('widget_type');

        return collect($this->widgets)
            ->map(fn ($class) => app($class, ['user' => $user]))
            ->filter(fn (DashboardWidget $widget) => $widget->canView($user))
            ->sortBy(function ($widget) use ($preferences) {
                $class = get_class($widget);
                return $preferences[$class]->position ?? 999;
            });
    }

    /**
     * Get all registered widgets.
     *
     * @return array<string, string>
     */
    public function getRegisteredWidgets(): array
    {
        return $this->widgets;
    }
}
