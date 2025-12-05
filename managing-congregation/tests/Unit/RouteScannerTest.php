<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Contracts\RouteScannerInterface;
use App\Services\RouteScanner;
use Illuminate\Routing\Route;
use Tests\TestCase;

class RouteScannerTest extends TestCase
{
    protected RouteScannerInterface $scanner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->scanner = app(RouteScannerInterface::class);
    }

    public function test_extract_permission_from_middleware_with_permission_prefix(): void
    {
        $middleware = ['auth', 'permission:members.view', 'verified'];

        $result = $this->scanner->extractPermissionFromMiddleware($middleware);

        $this->assertEquals('members.view', $result);
    }

    public function test_extract_permission_from_middleware_with_can_prefix(): void
    {
        $middleware = ['auth', 'can:view-admin', 'verified'];

        $result = $this->scanner->extractPermissionFromMiddleware($middleware);

        $this->assertEquals('view-admin', $result);
    }

    public function test_extract_permission_from_middleware_returns_null_when_no_permission(): void
    {
        $middleware = ['auth', 'verified'];

        $result = $this->scanner->extractPermissionFromMiddleware($middleware);

        $this->assertNull($result);
    }

    public function test_extract_permission_from_middleware_returns_first_permission(): void
    {
        $middleware = ['auth', 'permission:members.view', 'permission:members.edit'];

        $result = $this->scanner->extractPermissionFromMiddleware($middleware);

        $this->assertEquals('members.view', $result);
    }

    public function test_generate_permission_metadata_creates_correct_structure(): void
    {
        // Create a mock route
        $route = new Route(['GET'], '/members', ['as' => 'members.index']);

        $metadata = $this->scanner->generatePermissionMetadata($route, 'members.view');

        $this->assertIsArray($metadata);
        $this->assertEquals('members.view', $metadata['key']);
        $this->assertEquals('View Members', $metadata['name']);
        $this->assertEquals('members', $metadata['module']);
        $this->assertEquals('members.index', $metadata['route_name']);
        $this->assertEquals('members', $metadata['route_uri']); // Route::uri() returns without leading slash
        $this->assertIsArray($metadata['methods']);
    }

    public function test_generate_permission_metadata_handles_permission_without_dot(): void
    {
        $route = new Route(['GET'], '/admin', ['as' => 'admin.index']);

        $metadata = $this->scanner->generatePermissionMetadata($route, 'view-admin');

        $this->assertEquals('view-admin', $metadata['key']);
        $this->assertEquals('View Admin', $metadata['name']);
        $this->assertEquals('admin', $metadata['module']); // Extracts last part after dash
    }

    public function test_scan_routes_returns_collection(): void
    {
        $result = $this->scanner->scanRoutes();

        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $result);
    }

    public function test_scan_routes_finds_permissions_from_actual_routes(): void
    {
        // The application has routes with 'can:view-admin' middleware
        $result = $this->scanner->scanRoutes();

        // Should find at least the admin routes with permissions
        $this->assertGreaterThan(0, $result->count());

        // Check that we found the view-admin permission
        $viewAdminPermission = $result->firstWhere('key', 'view-admin');
        $this->assertNotNull($viewAdminPermission);
        $this->assertEquals('view-admin', $viewAdminPermission['key']);
        $this->assertEquals('admin', $viewAdminPermission['module']);
    }

    public function test_scan_routes_removes_duplicate_permissions(): void
    {
        $result = $this->scanner->scanRoutes();

        // Get all permission keys
        $keys = $result->pluck('key')->toArray();

        // Check that all keys are unique
        $uniqueKeys = array_unique($keys);
        $this->assertCount(count($uniqueKeys), $keys);
    }

    public function test_scanner_is_registered_as_singleton(): void
    {
        $scanner1 = app(RouteScannerInterface::class);
        $scanner2 = app(RouteScannerInterface::class);

        $this->assertSame($scanner1, $scanner2);
    }

    public function test_scanner_implements_interface(): void
    {
        $this->assertInstanceOf(RouteScannerInterface::class, $this->scanner);
        $this->assertInstanceOf(RouteScanner::class, $this->scanner);
    }
}
