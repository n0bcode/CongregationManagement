<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed permissions first
        $this->call(PermissionSeeder::class);

        // Create communities for testing
        $community1 = Community::factory()->create(['name' => 'Test Community 1']);
        $community2 = Community::factory()->create(['name' => 'Test Community 2']);

        // Create test users for each role using Enum constants
        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'role' => UserRole::SUPER_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'General Staff',
            'email' => 'general@example.com',
            'role' => UserRole::GENERAL,
        ]);

        User::factory()->create([
            'name' => 'Director User',
            'email' => 'director@example.com',
            'role' => UserRole::DIRECTOR,
            'community_id' => $community1->id,
        ]);

        User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'role' => UserRole::MEMBER,
            'community_id' => $community1->id,
        ]);

        // Seed additional communities with members
        Community::factory(3)
            ->has(Member::factory()->count(10))
            ->create();
    }
}
