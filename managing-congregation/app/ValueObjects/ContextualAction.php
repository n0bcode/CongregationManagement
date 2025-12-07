<?php

namespace App\ValueObjects;

use Closure;

class ContextualAction
{
    public function __construct(
        public string $label,
        public string $url,
        public string $method = 'GET',
        public ?string $icon = null,
        public string $variant = 'secondary', // primary, secondary, danger
        public bool $confirm = false,
        public ?string $confirmMessage = null,
    ) {}

    public static function make(string $label, string $url): self
    {
        return new self($label, $url);
    }

    public function method(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function variant(string $variant): self
    {
        $this->variant = $variant;
        return $this;
    }

    public function danger(): self
    {
        return $this->variant('danger');
    }

    public function confirm(string $message = 'Are you sure?'): self
    {
        $this->confirm = true;
        $this->confirmMessage = $message;
        return $this;
    }
}
