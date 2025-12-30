<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MemberScopingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionSeeder::class);
    }

    #[Test]
    public function director_sees_only_their_community_members(): void
    {
        // Arrange
        $myCommunity = Community::factory()->create();
        $otherCommunity = Community::factory()->create();

        $myMember = Member::factory()->create(['community_id' => $myCommunity->id]);
        $otherMember = Member::factory()->create(['community_id' => $otherCommunity->id]);

        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $myCommunity->id,
        ]);

        // Act
        $this->actingAs($director);
        $members = Member::all();

        // Assert
        $this->assertTrue($members->contains($myMember));
        $this->assertFalse($members->contains($otherMember));
        $this->assertCount(1, $members);
    }

    #[Test]
    public function member_sees_only_their_community_members(): void
    {
        // Arrange
        $myCommunity = Community::factory()->create();
        $otherCommunity = Community::factory()->create();

        $myMember = Member::factory()->create(['community_id' => $myCommunity->id]);
        $otherMember = Member::factory()->create(['community_id' => $otherCommunity->id]);

        $user = User::factory()->create([
            'role' => UserRole::MEMBER,
            'community_id' => $myCommunity->id,
        ]);

        // Act
        $this->actingAs($user);
        $members = Member::all();

        // Assert
        $this->assertTrue($members->contains($myMember));
        $this->assertFalse($members->contains($otherMember));
        $this->assertCount(1, $members);
    }

    #[Test]
    public function super_admin_sees_all_members(): void
    {
        // Arrange - ensure clean state
        Member::query()->forceDelete();
        
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        Member::factory()->create(['community_id' => $community1->id]);
        Member::factory()->create(['community_id' => $community2->id]);

        $superAdmin = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN,
        ]);

        // Act
        $this->actingAs($superAdmin);
        $members = Member::all();

        // Assert
        $this->assertCount(2, $members);
    }

    #[Test]
    public function general_sees_all_members(): void
    {
        // Arrange - ensure clean state
        Member::query()->forceDelete();
        
        $community1 = Community::factory()->create();
        $community2 = Community::factory()->create();

        Member::factory()->create(['community_id' => $community1->id]);
        Member::factory()->create(['community_id' => $community2->id]);

        $general = User::factory()->create([
            'role' => UserRole::GENERAL,
        ]);

        // Act
        $this->actingAs($general);
        $members = Member::all();

        // Assert
        $this->assertCount(2, $members);
    }

    #[Test]
    public function relationship_queries_respect_scoping(): void
    {
        // Arrange
        $myCommunity = Community::factory()->create();
        $otherCommunity = Community::factory()->create();

        // Create members in both communities
        Member::factory()->count(3)->create(['community_id' => $myCommunity->id]);
        Member::factory()->count(2)->create(['community_id' => $otherCommunity->id]);

        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $myCommunity->id,
        ]);

        // Act
        $this->actingAs($director);

        // Even if we load the other community explicitly, its members relationship should be empty
        // because the global scope applies to the Member model query
        $otherCommunityLoaded = Community::find($otherCommunity->id);

        $this->assertCount(0, $otherCommunityLoaded->members);

        // My community members should be visible
        $myCommunityLoaded = Community::find($myCommunity->id);
        $this->assertCount(3, $myCommunityLoaded->members);
    }

    #[Test]
    public function query_builder_methods_respect_scoping(): void
    {
        // Arrange
        $myCommunity = Community::factory()->create();
        $otherCommunity = Community::factory()->create();

        $myMember = Member::factory()->create(['community_id' => $myCommunity->id]);
        $otherMember = Member::factory()->create(['community_id' => $otherCommunity->id]);

        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $myCommunity->id,
        ]);

        // Act
        $this->actingAs($director);

        // find()
        $foundMyMember = Member::find($myMember->id);
        $foundOtherMember = Member::find($otherMember->id);

        // where()
        $queriedOtherMember = Member::where('id', $otherMember->id)->first();

        // Assert
        $this->assertNotNull($foundMyMember);
        $this->assertNull($foundOtherMember);
        $this->assertNull($queriedOtherMember);
    }

    #[Test]
    public function director_with_null_community_id_sees_no_members(): void
    {
        // Arrange
        $community = Community::factory()->create();
        Member::factory()->create(['community_id' => $community->id]);

        // Director with no community assigned (should not happen in practice but good edge case)
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => null,
        ]);

        // Act
        $this->actingAs($director);
        $members = Member::all();

        // Assert
        // Logic: if community_id is null, the scope check `if ($user->community_id)` returns false,
        // so NO where clause is added. Wait, if no where clause is added, they see ALL members?
        // Let's check the trait logic:
        // if ($user->community_id) { $builder->where(...) }
        // So if community_id is null, they see EVERYTHING? That's a security risk!
        // The AC says: "Director can ONLY see members from their assigned community".
        // If they have NO assigned community, they should see NOTHING.
        // I need to fix the trait logic if this test fails (it will fail if my logic allows full access).
        // Actually, let's write the test expecting 0 members, and if it fails, I fix the trait.

        $this->assertCount(0, $members);
    }

    #[Test]
    public function unauthenticated_requests_are_blocked_by_default(): void
    {
        // Arrange
        $community = Community::factory()->create();
        Member::factory()->create(['community_id' => $community->id]);

        // Act
        // No actingAs()
        $members = Member::all();

        // Assert
        // Unauthenticated users should see NOTHING by default for security
        $this->assertCount(0, $members);
    }

    #[Test]
    public function scoping_works_with_pagination(): void
    {
        // Arrange
        $myCommunity = Community::factory()->create();
        $otherCommunity = Community::factory()->create();

        Member::factory()->count(15)->create(['community_id' => $myCommunity->id]);
        Member::factory()->count(5)->create(['community_id' => $otherCommunity->id]);

        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $myCommunity->id,
        ]);

        // Act
        $this->actingAs($director);
        $members = Member::paginate(10);

        // Assert
        $this->assertCount(10, $members); // First page
        $this->assertEquals(15, $members->total()); // Total should be 15, not 20
    }

    #[Test]
    public function scoping_works_with_soft_deletes(): void
    {
        // Arrange
        $myCommunity = Community::factory()->create();
        $otherCommunity = Community::factory()->create();

        $myDeletedMember = Member::factory()->create([
            'community_id' => $myCommunity->id,
            'deleted_at' => now(),
        ]);
        $otherDeletedMember = Member::factory()->create([
            'community_id' => $otherCommunity->id,
            'deleted_at' => now(),
        ]);

        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $myCommunity->id,
        ]);

        // Act
        $this->actingAs($director);

        // Query with trashed
        $members = Member::onlyTrashed()->get();

        // Assert
        $this->assertTrue($members->contains($myDeletedMember));
        $this->assertFalse($members->contains($otherDeletedMember));
    }

    #[Test]
    public function scoping_does_not_introduce_n_plus_1_queries(): void
    {
        $community = Community::factory()->create();
        $director = User::factory()->create([
            'role' => UserRole::DIRECTOR,
            'community_id' => $community->id,
        ]);
        Member::factory()->count(5)->create(['community_id' => $community->id]);

        $this->actingAs($director);

        DB::enableQueryLog();
        // Eager load community to avoid N+1 on accessing relationship
        $members = Member::with('community')->get();

        // Access relationship to ensure it was eager loaded
        foreach ($members as $member) {
            $member->community;
        }

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);

        // Should be 2 queries:
        // 1. Select members with scope
        // 2. Select communities for those members
        $this->assertEquals(2, $queryCount);
    }
}
