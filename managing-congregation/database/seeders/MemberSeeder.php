<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $communities = Community::all();

        if ($communities->isEmpty()) {
            $this->command->warn('No communities found. Creating sample communities first...');
            $communities = collect([
                Community::create(['name' => 'St. Joseph House', 'location' => 'Main Campus']),
                Community::create(['name' => 'Bethany House', 'location' => 'East Wing']),
                Community::create(['name' => 'Sacred Heart House', 'location' => 'West Wing']),
            ]);
        }

        $this->command->info('Creating members for each community...');

        foreach ($communities as $community) {
            // Create 10 members per community
            // Use withoutGlobalScopes to bypass community scoping during seeding
            for ($i = 0; $i < 10; $i++) {
                Member::withoutGlobalScopes()->create([
                    'community_id' => $community->id,
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'religious_name' => fake()->optional()->firstName(),
                    'dob' => fake()->dateTimeBetween('-70 years', '-18 years'),
                    'entry_date' => fake()->dateTimeBetween('-30 years', 'now'),
                    'status' => 'Active',
                ]);
            }
        }

        $this->command->info('Members created successfully!');
        $this->command->info('Total members: '.Member::withoutGlobalScopes()->count());
    }
}
