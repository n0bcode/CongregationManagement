<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreFormationEventRequest;
use Tests\TestCase;

class StoreFormationEventRequestTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_rules_are_correct(): void
    {
        $member = \App\Models\Member::factory()->create();
        
        $request = new StoreFormationEventRequest;
        $request->setRouteResolver(function () use ($member, $request) {
            $route = new \Illuminate\Routing\Route('POST', 'dummy', []);
            $route->bind($request);
            $route->setParameter('member', $member);
            return $route;
        });

        $rules = $request->rules();

        $this->assertArrayHasKey('stage', $rules);
        $this->assertArrayHasKey('started_at', $rules);
        $this->assertArrayHasKey('notes', $rules);

        // Basic checks
        $this->assertContains('required', $rules['stage']);
        $this->assertContains('required', $rules['started_at']);
        $this->assertContains('date', $rules['started_at']);
    }

    public function test_authorize_returns_true(): void
    {
        $member = \App\Models\Member::factory()->create();
        
        $user = \Mockery::mock(\App\Models\User::class);
        $user->shouldReceive('can')->with('create', \App\Models\FormationEvent::class)->andReturn(true);
        $user->shouldReceive('can')->with('view', $member)->andReturn(true);

        $request = new StoreFormationEventRequest;
        $request->setUserResolver(fn () => $user);
        
        $request->setRouteResolver(function () use ($member, $request) {
            $route = new \Illuminate\Routing\Route('POST', 'dummy', []);
            $route->bind($request);
            $route->setParameter('member', $member);
            return $route;
        });

        $this->assertTrue($request->authorize());
    }
}
