<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds the roles table with system RBAC roles mapped to existing codes.
     */
    public function run(): void
    {
        // Map system roles to existing short codes (max 10 chars)
        // P, L, S, D, n are existing codes in database
        $systemRoles = [
            [
                'code' => 'SA',  // Super Admin
                'title' => UserRole::SUPER_ADMIN->label(),
                'description' => 'Full system access with all permissions',
                'is_system' => true,
            ],
            [
                'code' => 'GS',  // General Secretary
                'title' => UserRole::GENERAL->label(),
                'description' => 'General Secretary with administrative access',
                'is_system' => true,
            ],
            [
                'code' => 'DR',  // Director
                'title' => UserRole::DIRECTOR->label(),
                'description' => 'Community Director with community-scoped access',
                'is_system' => true,
            ],
            [
                'code' => 'TR',  // Treasurer
                'title' => UserRole::TREASURER->label(),
                'description' => 'Financial management and reporting',
                'is_system' => true,
            ],
            [
                'code' => 'MB',  // Member
                'title' => UserRole::MEMBER->label(),
                'description' => 'Basic member access',
                'is_system' => true,
            ],
        ];

        foreach ($systemRoles as $roleData) {
            Role::updateOrCreate(
                ['code' => $roleData['code']],
                $roleData
            );
        }

        $this->command->info('✓ Seeded 5 system RBAC roles (SA, GS, DR, TR, MB)');
    }
}
