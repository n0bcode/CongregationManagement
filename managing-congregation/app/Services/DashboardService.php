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
        try {
            $preferences = \App\Models\DashboardWidget::where('user_id', $user->id)
                ->get()
                ->keyBy('widget_type');
        } catch (\Exception $e) {
            // If there's an issue querying preferences, use empty collection
            $preferences = collect();
        }

        return collect($this->widgets)
            ->map(function ($class, $key) use ($user) {
                $widget = app($class, ['user' => $user]);
                $widget->widgetKey = $key; // Store the key for sorting
                return $widget;
            })
            ->filter(fn (DashboardWidget $widget) => $widget->canView($user))
            ->sortBy(function ($widget) use ($preferences) {
                $widgetKey = $widget->widgetKey ?? get_class($widget);
                return $preferences->get($widgetKey)?->position ?? 999;
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
