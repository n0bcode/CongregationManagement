<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RouteScannerInterface;
use Illuminate\Routing\Route;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route as RouteFacade;

class RouteScanner implements RouteScannerInterface
{
    /**
     * Scan all routes and extract permission requirements
     */
    public function scanRoutes(): Collection
    {
        try {
            $routes = RouteFacade::getRoutes()->getRoutes();
            $permissions = collect();

            foreach ($routes as $route) {
                $middleware = $this->getRouteMiddleware($route);
                $permissionKey = $this->extractPermissionFromMiddleware($middleware);

                if ($permissionKey) {
                    $metadata = $this->generatePermissionMetadata($route, $permissionKey);
                    $permissions->push($metadata);
                }
            }

            // Remove duplicates based on permission key
            return $permissions->unique('key');
        } catch (\Throwable $e) {
            Log::error('Failed to scan routes for permissions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return collect();
        }
    }

    /**
     * Extract permission from middleware array
     */
    public function extractPermissionFromMiddleware(array $middleware): ?string
    {
        foreach ($middleware as $m) {
            // Handle both string middleware and middleware with parameters
            $middlewareString = is_string($m) ? $m : (string) $m;

            // Check for 'permission:' prefix
            if (str_starts_with($middlewareString, 'permission:')) {
                return str_replace('permission:', '', $middlewareString);
            }

            // Check for 'can:' prefix (Laravel's built-in authorization)
            if (str_starts_with($middlewareString, 'can:')) {
                return str_replace('can:', '', $middlewareString);
            }
        }

        return null;
    }

    /**
     * Generate permission metadata from route
     */
    public function generatePermissionMetadata(Route $route, string $permissionKey): array
    {
        return [
            'key' => $permissionKey,
            'name' => $this->generateName($permissionKey),
            'module' => $this->extractModule($permissionKey),
            'route_name' => $route->getName(),
            'route_uri' => $route->uri(),
            'methods' => $route->methods(),
        ];
    }

    /**
     * Get all middleware for a route
     */
    private function getRouteMiddleware(Route $route): array
    {
        try {
            // Get middleware from the route
            $middleware = $route->middleware();

            // Also check for middleware from controller
            $action = $route->getAction();
            if (isset($action['middleware'])) {
                $controllerMiddleware = is_array($action['middleware'])
                    ? $action['middleware']
                    : [$action['middleware']];

                $middleware = array_merge($middleware, $controllerMiddleware);
            }

            return $middleware;
        } catch (\Throwable $e) {
            Log::warning('Failed to get middleware for route', [
                'route_name' => $route->getName(),
                'route_uri' => $route->uri(),
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Generate a human-readable name from permission key
     *
     * Converts 'members.view' to 'View Members'
     */
    private function generateName(string $key): string
    {
        try {
            // Handle special cases like 'view-admin'
            if (! str_contains($key, '.')) {
                return ucwords(str_replace(['-', '_'], ' ', $key));
            }

            [$module, $action] = explode('.', $key, 2);

            $actionName = ucfirst(str_replace(['-', '_'], ' ', $action));
            $moduleName = ucfirst(str_replace(['-', '_'], ' ', $module));

            return "{$actionName} {$moduleName}";
        } catch (\Throwable $e) {
            Log::warning('Failed to generate permission name', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return ucwords(str_replace(['.', '-', '_'], ' ', $key));
        }
    }

    /**
     * Extract module name from permission key
     *
     * Extracts 'members' from 'members.view'
     * Extracts 'admin' from 'view-admin'
     */
    private function extractModule(string $key): string
    {
        try {
            if (! str_contains($key, '.')) {
                // For keys without dots, use the last word (e.g., 'view-admin' -> 'admin')
                $parts = explode('-', $key);

                return end($parts);
            }

            return explode('.', $key)[0];
        } catch (\Throwable $e) {
            Log::warning('Failed to extract module from permission key', [
                'key' => $key,
                'error' => $e->getMessage(),
            ]);

            return 'general';
        }
    }
}
