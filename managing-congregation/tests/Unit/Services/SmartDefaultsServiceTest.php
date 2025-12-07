<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Services\SmartDefaultsService;
use Mockery;
use Tests\TestCase;

class SmartDefaultsServiceTest extends TestCase
{
    public function test_it_returns_defaults_for_expense()
    {
        $service = new SmartDefaultsService();
        $user = Mockery::mock(User::class);

        $defaults = $service->getDefaults('expense', $user);

        $this->assertArrayHasKey('date', $defaults);
        $this->assertArrayHasKey('currency', $defaults);
        $this->assertEquals(now()->format('Y-m-d'), $defaults['date']);
    }

    public function test_it_returns_empty_array_for_unknown_type()
    {
        $service = new SmartDefaultsService();
        $user = Mockery::mock(User::class);

        $defaults = $service->getDefaults('unknown', $user);

        $this->assertEmpty($defaults);
    }
}
