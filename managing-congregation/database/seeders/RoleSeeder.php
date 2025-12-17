<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeds the roles table with Salesian role types from the directory.
     */
    public function run(): void
    {
        $roles = [
            [
                'code' => 'P',
                'title' => 'Presbyter',
                'description' => 'Father/Priest - Ordained priest in the Salesian congregation',
            ],
            [
                'code' => 'L',
                'title' => 'Laicus',
                'description' => 'Brother/Coadjutor - Lay brother in the Salesian congregation',
            ],
            [
                'code' => 'S',
                'title' => 'Scholasticus',
                'description' => 'Cleric - Salesian in formation studying for priesthood',
            ],
            [
                'code' => 'D',
                'title' => 'Diaconus',
                'description' => 'Deacon - Ordained deacon, transitional to priesthood',
            ],
            [
                'code' => 'n',
                'title' => 'Novitius',
                'description' => 'Novice - Member in the novitiate year of formation',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['code' => $role['code']],
                $role
            );
        }

        $this->command->info('✓ Seeded 5 Salesian roles (P, L, S, D, n)');
    }
}
