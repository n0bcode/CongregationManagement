<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Community;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\DatabaseSeeder;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_seeds_communities_and_members()
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertGreaterThan(0, Community::count());
        $this->assertGreaterThan(0, Member::withoutGlobalScopes()->count());
    }
}
