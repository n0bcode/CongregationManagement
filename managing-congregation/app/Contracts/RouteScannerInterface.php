<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Routing\Route;
use Illuminate\Support\Collection;

interface RouteScannerInterface
{
    /**
     * Scan all routes and extract permission requirements
     *
     * @return Collection Collection of permission metadata arrays
     */
    public function scanRoutes(): Collection;

    /**
     * Extract permission from middleware array
     *
     * @param array $middleware Array of middleware strings
     * @return string|null Permission key or null if not found
     */
    public function extractPermissionFromMiddleware(array $middleware): ?string;

    /**
     * Generate permission metadata from route
     *
     * @param Route $route
     * @param string $permissionKey
     * @return array{key: string, name: string, module: string, route_name: string|null, route_uri: string, methods: array}
     */
    public function generatePermissionMetadata(Route $route, string $permissionKey): array;
}
