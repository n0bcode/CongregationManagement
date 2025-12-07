<?php

namespace App\Services;

use App\ValueObjects\ContextualAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ContextualActionService
{
    protected array $registries = [];

    public function register(string $modelClass, callable $callback): void
    {
        $this->registries[$modelClass] = $callback;
    }

    public function getActions(Model $model): Collection
    {
        $class = get_class($model);

        if (!isset($this->registries[$class])) {
            return collect();
        }

        $actions = ($this->registries[$class])($model);

        return collect($actions);
    }
}
