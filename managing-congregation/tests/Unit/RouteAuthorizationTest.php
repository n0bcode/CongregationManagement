<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Feature: congregation-management-mvp, Property 2: Routes Have Authorization Middleware
 * Validates: Requirements 1.4
 *
 * For any route that accesses protected resources, the route should have authentication
 * and authorization middleware applied
 */
class RouteAuthorizationTest extends TestCase
{
    /**
     * @test
     */
    public function all_protected_routes_have_auth_middleware(): void
    {
        $routes = Route::getRoutes();
        $protectedPrefixes = ['members', 'formation', 'profile'];
        $publicRoutes = ['/', 'login', 'register', 'password'];

        foreach ($routes as $route) {
            $uri = $route->uri();
            $middleware = $route->middleware();

            // Skip public routes
            $isPublic = false;
            foreach ($publicRoutes as $publicRoute) {
                if (str_starts_with($uri, $publicRoute)) {
                    $isPublic = true;
                    break;
                }
            }

            if ($isPublic) {
                continue;
            }

            // Check if route is protected
            $isProtected = false;
            foreach ($protectedPrefixes as $prefix) {
                if (str_starts_with($uri, $prefix)) {
                    $isProtected = true;
                    break;
                }
            }

            if ($isProtected) {
                $this->assertContains(
                    'auth',
                    $middleware,
                    "Route {$uri} should have 'auth' middleware"
                );
            }
        }
    }

    /**
     * @test
     * @dataProvider protectedRouteProvider
     */
    public function protected_routes_require_authentication(string $method, string $uri): void
    {
        $route = Route::getRoutes()->match(
            \Illuminate\Http\Request::create($uri, $method)
        );

        $middleware = $route->middleware();

        $this->assertContains(
            'auth',
            $middleware,
            "Route {$method} {$uri} should require authentication"
        );
    }

    public static function protectedRouteProvider(): array
    {
        return [
            ['GET', '/members'],
            ['GET', '/members/create'],
            ['POST', '/members'],
            ['GET', '/members/1'],
            ['GET', '/members/1/edit'],
            ['PUT', '/members/1'],
            ['DELETE', '/members/1'],
            ['POST', '/members/1/formation'],
            ['POST', '/members/1/transfer'],
            ['PUT', '/members/1/photo'],
            ['DELETE', '/members/1/photo'],
            ['GET', '/profile'],
            ['PATCH', '/profile'],
            ['DELETE', '/profile'],
        ];
    }

    /**
     * @test
     */
    public function dashboard_route_has_auth_and_verified_middleware(): void
    {
        $route = Route::getRoutes()->match(
            \Illuminate\Http\Request::create('/dashboard', 'GET')
        );

        $middleware = $route->middleware();

        $this->assertContains('auth', $middleware, 'Dashboard should require authentication');
        $this->assertContains('verified', $middleware, 'Dashboard should require email verification');
    }

    /**
     * @test
     */
    public function public_routes_do_not_have_auth_middleware(): void
    {
        $publicRoutes = [
            ['GET', '/'],
            ['GET', '/login'],
            ['POST', '/login'],
            ['GET', '/register'],
            ['POST', '/register'],
        ];

        foreach ($publicRoutes as [$method, $uri]) {
            $route = Route::getRoutes()->match(
                \Illuminate\Http\Request::create($uri, $method)
            );

            $middleware = $route->middleware();

            $this->assertNotContains(
                'auth',
                $middleware,
                "Public route {$method} {$uri} should not require authentication"
            );
        }
    }
}
