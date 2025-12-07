<?php

namespace Tests\Unit\Services;

use App\Contracts\DashboardWidget;
use App\Models\User;
use App\Services\DashboardService;
use Illuminate\Contracts\View\View;
use Mockery;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    public function test_it_can_register_and_retrieve_widgets()
    {
        $service = new DashboardService();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Mock DashboardWidget model query
        $preferenceMock = Mockery::mock('alias:\App\Models\DashboardWidget');
        $preferenceMock->shouldReceive('where->get->keyBy')->andReturn(collect());

        // Create a mock widget
        $widget = Mockery::mock(DashboardWidget::class);
        $widget->shouldReceive('canView')->with($user)->andReturn(true);
        
        // We need to mock the app container resolution for the widget
        // Since the service uses app($class, ['user' => $user]), we need to ensure the mock is returned
        $widgetClass = 'App\Widgets\TestWidget';
        
        $this->app->bind($widgetClass, function ($app, $params) use ($widget, $user) {
            $this->assertEquals($user, $params['user']);
            return $widget;
        });

        $service->registerWidget('test_widget', $widgetClass);

        $widgets = $service->getWidgetsForUser($user);

        $this->assertCount(1, $widgets);
        $this->assertEquals($widget, $widgets->first());
    }

    public function test_it_filters_widgets_user_cannot_view()
    {
        $service = new DashboardService();
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Mock DashboardWidget model query
        $preferenceMock = Mockery::mock('alias:\App\Models\DashboardWidget');
        $preferenceMock->shouldReceive('where->get->keyBy')->andReturn(collect());

        $widget = Mockery::mock(DashboardWidget::class);
        $widget->shouldReceive('canView')->with($user)->andReturn(false);

        $widgetClass = 'App\Widgets\TestWidget';
        
        $this->app->bind($widgetClass, function ($app, $params) use ($widget) {
            return $widget;
        });

        $service->registerWidget('test_widget', $widgetClass);

        $widgets = $service->getWidgetsForUser($user);

        $this->assertCount(0, $widgets);
    }
}
