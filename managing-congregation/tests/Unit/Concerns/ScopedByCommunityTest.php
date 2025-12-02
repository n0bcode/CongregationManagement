<?php

declare(strict_types=1);

namespace Tests\Unit\Concerns;

use App\Models\Concerns\ScopedByCommunity;
use App\Models\User;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class ScopedByCommunityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_applies_scope_for_director_role(): void
    {
        $model = new class extends Model {
            use ScopedByCommunity;
            protected $table = 'users';
        };

        $community = \App\Models\Community::factory()->create();
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);

        Auth::login($director);

        $query = $model::query()->toSql();
        $this->assertStringContainsString('community_id', $query);
    }

    #[Test]
    public function it_applies_scope_for_member_role(): void
    {
        $model = new class extends Model {
            use ScopedByCommunity;
            protected $table = 'users';
        };

        $community = \App\Models\Community::factory()->create();
        $member = User::factory()->create([
            'role' => UserRole::MEMBER,
            'community_id' => $community->id,
        ]);

        Auth::login($member);

        $query = $model::query()->toSql();
        $this->assertStringContainsString('community_id', $query);
    }

    #[Test]
    public function it_bypasses_scope_for_super_admin(): void
    {
        $model = new class extends Model {
            use ScopedByCommunity;
            protected $table = 'users';
        };

        $superAdmin = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
        ]);

        Auth::login($superAdmin);

        $query = $model::query()->toSql();
        $this->assertStringNotContainsString('community_id', $query);
    }

    #[Test]
    public function it_bypasses_scope_for_general_role(): void
    {
        $model = new class extends Model {
            use ScopedByCommunity;
            protected $table = 'users';
        };

        $general = User::factory()->create([
            'role' => UserRole::GENERAL,
        ]);

        Auth::login($general);

        $query = $model::query()->toSql();
        $this->assertStringNotContainsString('community_id', $query);
    }

    #[Test]
    public function without_global_scopes_removes_the_scope(): void
    {
        $model = new class extends Model {
            use ScopedByCommunity;
            protected $table = 'users';
        };

        $community = \App\Models\Community::factory()->create();
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);

        Auth::login($director);

        // Verify scope is present by default
        $query = $model::query()->toSql();
        $this->assertStringContainsString('community_id', $query);

        // Verify scope is removed
        $queryWithoutScope = $model::withoutGlobalScopes()->toSql();
        $this->assertStringNotContainsString('community_id', $queryWithoutScope);
    }
    #[Test]
    public function it_blocks_access_for_unauthenticated_users(): void
    {
        $model = new class extends Model {
            use ScopedByCommunity;
            protected $table = 'users';
        };

        // Ensure no user is logged in
        Auth::logout();

        $query = $model::query()->toSql();
        // Our implementation uses `whereRaw('1 = 0')`
        $this->assertStringContainsString('1 = 0', $query);
    }
}
