<?php

declare(strict_types=1);

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\FormationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormationPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_and_view(): void
    {
        $user = User::factory()->create(['role' => UserRole::SUPER_ADMIN]);
        $policy = new FormationPolicy();

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->create($user));
    }

    public function test_director_can_create_and_view(): void
    {
        $user = User::factory()->create(['role' => UserRole::DIRECTOR]);
        $policy = new FormationPolicy();

        $this->assertTrue($policy->viewAny($user));
        $this->assertTrue($policy->create($user));
    }

    public function test_member_cannot_create(): void
    {
        $user = User::factory()->create(['role' => UserRole::MEMBER]);
        $policy = new FormationPolicy();

        $this->assertFalse($policy->create($user));
    }
}
