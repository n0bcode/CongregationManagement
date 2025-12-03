<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Permission>
 */
class PermissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $module = fake()->randomElement(['territories', 'publishers', 'reports', 'users']);
        $action = fake()->randomElement(['view', 'create', 'update', 'delete', 'manage']);

        return [
            'key' => "{$module}.{$action}",
            'name' => ucfirst($action).' '.ucfirst($module),
            'module' => $module,
        ];
    }
}
