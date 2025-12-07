<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Models\User;
use App\Services\ContextualActionService;
use App\ValueObjects\ContextualAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContextualActionServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_registers_and_retrieves_actions()
    {
        $service = new ContextualActionService();
        $member = Member::factory()->create();

        $service->register(Member::class, function ($model) {
            return [
                ContextualAction::make('Test Action', '/test'),
            ];
        });

        $actions = $service->getActions($member);

        $this->assertCount(1, $actions);
        $this->assertEquals('Test Action', $actions->first()->label);
        $this->assertEquals('/test', $actions->first()->url);
    }

    /** @test */
    public function it_returns_empty_collection_if_no_actions_registered()
    {
        $service = new ContextualActionService();
        $member = Member::factory()->create();

        $actions = $service->getActions($member);

        $this->assertCount(0, $actions);
    }
}
