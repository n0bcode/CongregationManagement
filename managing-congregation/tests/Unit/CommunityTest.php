<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Community;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_community_has_name_and_location()
    {
        $community = Community::factory()->create([
            'name' => 'Test Community',
            'location' => 'Test Location',
        ]);

        $this->assertEquals('Test Community', $community->name);
        $this->assertEquals('Test Location', $community->location);
    }

    public function test_community_uses_soft_deletes()
    {
        $this->assertContains(SoftDeletes::class, class_uses(Community::class));
    }
}
