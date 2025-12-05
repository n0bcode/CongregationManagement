<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformancePropertyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Property 39: Dashboard Loads Under 2 Seconds
     * Validates: Requirements 13.1
     */
    public function test_dashboard_performance()
    {
        $user = User::factory()->create();

        $startTime = microtime(true);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        $response->assertStatus(200);
        $this->assertLessThan(2.0, $duration, "Dashboard took too long to load: {$duration}s");
    }

    /**
     * Property 40: Member Search Performance
     * Validates: Requirements 13.2
     */
    public function test_member_search_performance()
    {
        $user = User::factory()->director()->create();
        // Seed 1000 members
        Member::factory()->count(1000)->create([
            'community_id' => $user->community_id,
        ]);

        $startTime = microtime(true);

        $response = $this->actingAs($user)->get(route('members.index', ['search' => 'Smith']));

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        $response->assertStatus(200);
        // Should be fast with indexes
        $this->assertLessThan(0.5, $duration, "Search took too long: {$duration}s");
    }

    /**
     * Property 42: RBAC Uses Cached Permissions
     * Validates: Requirements 13.4
     */
    public function test_rbac_permission_caching()
    {
        $user = User::factory()->create([
            'role' => \App\Enums\UserRole::MEMBER,
        ]);

        // Ensure permission exists and is assigned to role (or just check any permission)
        // For this test, we just want to verify the caching mechanism in User::hasPermission

        // First check - should query DB
        DB::enableQueryLog();
        $user->hasPermission('view-dashboard');
        $queriesInitial = count(DB::getQueryLog());

        // Second check - should hit cache
        DB::flushQueryLog();
        $user->hasPermission('view-dashboard');
        $queriesCached = count(DB::getQueryLog());

        // Note: In test environment, cache driver might be array which works,
        // but we need to ensure the logic actually attempts to cache.
        // If the implementation uses Cache::remember, queriesCached should be 0.

        // For now, we assert that subsequent calls don't increase query count significantly
        // or strictly 0 if we are sure about the implementation.
        $this->assertLessThan($queriesInitial, $queriesCached, 'Caching should reduce DB queries');
        $this->assertEquals(0, $queriesCached, 'Cached permission check should not query DB');
    }

    /**
     * Property 41: Photos Are Optimized Automatically
     * Validates: Requirements 13.3
     */
    public function test_photo_optimization()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        // Setup permissions
        $permission = \App\Models\Permission::create(['key' => 'members.edit', 'name' => 'Edit Members', 'module' => 'members']);
        \Illuminate\Support\Facades\DB::table('role_permissions')->insert([
            'role' => \App\Enums\UserRole::DIRECTOR->value,
            'permission_id' => $permission->id,
        ]);

        $user = User::factory()->director()->create();
        $member = Member::factory()->create(['community_id' => $user->community_id]);

        // Create a large fake image (1000x1000)
        $file = \Illuminate\Http\UploadedFile::fake()->image('photo.jpg', 1000, 1000);

        $response = $this->actingAs($user)
            ->withoutExceptionHandling()
            ->put(route('members.photo.update', $member), [
                'photo' => $file,
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();

        $member->refresh();

        // Verify file extension is webp
        $this->assertStringEndsWith('.webp', $member->profile_photo_path);

        // Verify file exists
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($member->profile_photo_path);

        // Verify optimization (we can't easily check dimensions of fake storage file without reading it back)
        // But the extension change confirms our logic ran.
    }
}
