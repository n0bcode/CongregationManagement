<?php

namespace App\View\Components\Widgets;

use Illuminate\Contracts\View\View;

class QuickActionsWidget extends BaseWidget
{
    public function render(): View
    {
        return view('components.widgets.quick-actions');
    }
}
