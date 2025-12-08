<?php

namespace Tests\Unit;

use App\Models\FilterPreset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterPresetTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_filter_preset()
    {
        $user = User::factory()->create();

        $preset = FilterPreset::create([
            'user_id' => $user->id,
            'name' => 'My Preset',
            'context' => 'members_list',
            'filters' => ['status' => 'active', 'search' => 'John'],
            'is_public' => true,
        ]);

        $this->assertDatabaseHas('filter_presets', [
            'id' => $preset->id,
            'name' => 'My Preset',
            'is_public' => true,
        ]);

        $this->assertEquals(['status' => 'active', 'search' => 'John'], $preset->filters);
        $this->assertTrue($preset->user->is($user));
    }

    public function test_filters_are_cast_to_array()
    {
        $user = User::factory()->create();
        $preset = FilterPreset::create([
            'user_id' => $user->id,
            'name' => 'Test',
            'context' => 'test',
            'filters' => ['foo' => 'bar'],
        ]);

        $this->assertIsArray($preset->filters);
        $this->assertEquals('bar', $preset->filters['foo']);
    }
}
