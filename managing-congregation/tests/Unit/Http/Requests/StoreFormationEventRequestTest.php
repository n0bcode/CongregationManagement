<?php

declare(strict_types=1);

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreFormationEventRequest;
use App\Enums\FormationStage;
use Illuminate\Validation\Rule;
use Tests\TestCase;

class StoreFormationEventRequestTest extends TestCase
{
    public function test_rules_are_correct(): void
    {
        $request = new StoreFormationEventRequest();
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
        $request = new StoreFormationEventRequest();
        $this->assertTrue($request->authorize());
    }
}
