<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SmartForm extends Component
{
    public function __construct(
        public string $action,
        public string $method = 'POST',
        public bool $hasFiles = false,
        public ?string $id = null
    ) {
        $this->id = $id ?? 'form-' . uniqid();
    }

    public function render()
    {
        return view('components.smart-form');
    }
}
