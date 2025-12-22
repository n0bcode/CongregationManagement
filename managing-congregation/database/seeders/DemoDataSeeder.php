<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Community;
use App\Models\Member;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\Folder;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Ordination;
use App\Models\Reminder;
use App\Models\AuditLog;
use App\Models\Permission;
use App\Models\PeriodicEvent;
use App\Models\FormationEvent;
use App\Models\HealthRecord;
use App\Models\Assignment;
use App\Models\Skill;
use App\Models\Expense; // Added
use App\Enums\MemberStatus;
use App\Enums\FormationStage;
use App\Enums\UserRole;
use App\Enums\DocumentCategory;
use App\Enums\SkillCategory;
use App\Enums\SkillProficiency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Roles and Permissions first
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2. Create System Users (Restored)
        $users = [
            ['name' => 'System Administrator', 'email' => 'admin@congregation.org', 'role' => UserRole::SUPER_ADMIN],
            ['name' => 'General Secretary', 'email' => 'secretary@congregation.org', 'role' => UserRole::GENERAL],
            ['name' => 'General Treasurer', 'email' => 'treasurer@congregation.org', 'role' => UserRole::TREASURER]
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, ['password' => Hash::make('password')])
            );
        }

        $this->command->info('Parsing merged_directory.md...');
        $mergedPath = database_path('seeders/data/merged_directory.md');
        
        if (!file_exists($mergedPath)) {
            $this->command->error("Merged Directory file not found at: $mergedPath");
            return;
        }

        $lines = file($mergedPath, FILE_IGNORE_NEW_LINES);
        $memberRegistry = [];
        $students = [];
        $deceased = []; // Necrology
        
        // Pass 1: Build Master Registry from Tables
        $this->command->info('Building Master Registry...');
        foreach ($lines as $line) {
             // Registry pattern from basedData2 & db4
            if (preg_match('/\|\s*\*\*(.+?)\*\*\s*\|\s*([A-Z]+)\s*\|\s*(.*?)\s*\|\s*(.*?)\s*\|\s*(.*?)\s*\|\s*(.*?)\s*\|\s*(.*?)\s*\|/', $line, $matches)) {
                $fullName = trim($matches[1]);
                // ... same logic as before
                $memberRegistry[strtoupper($fullName)] = [
                    'email' => trim($matches[3]) !== '-' ? trim($matches[3]) : null,
                    'phone' => trim($matches[4]) !== '-' ? trim($matches[4]) : null,
                    'dob' => $this->parseDate(trim($matches[5])),
                    'first_profession' => $this->parseDate(trim($matches[6])),
                    'ordination' => $this->parseDate(trim($matches[7])),
                ];
            }
        }
        
        $this->command->info("Registry built with " . count($memberRegistry) . " entries.");
        
        // Pass 2: Directory & Assignments
        $currentCommunity = null;
        $createdCommunities = [];
        $allMembers = []; // Store references
        
        // Translation Map
        $roleMap = [
            'Giám đốc' => 'Rector',
            'Phó Giám đốc' => 'Vice Rector',
            'Quản lý' => 'Administrator',
            'Hiệu trưởng' => 'Principal',
            // ... (keep full map)
            'Cha sở' => 'Parish Priest',
            'Cha phó' => 'Assistant Parish Priest',
            'Mục vụ giới trẻ' => 'Youth Minister',
            'Thực tập sinh' => 'Practical Trainee',
            'Tiền tập viện' => 'Pre-Novitiate Director',
            'Sư huynh' => 'Brother',
            'Phụ trách' => 'In-charge',
            'Quản trị viên' => 'Administrator',
            'Giám đốc Dự án' => 'Project Director',
            'Trưởng ban' => 'Head of Commission',
            'Cố vấn' => 'Councilor',
            'Thư ký' => 'Secretary',
            'Giải tội' => 'Confessor',
        ];

        // Context Flags for Supplements
        $section = null; 

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Detect Sections
            if (str_contains($line, 'UTUME')) $section = 'utume';
            if (str_contains($line, 'MOSHI')) $section = 'moshi';
            if (str_contains($line, 'NECROLOGY') || str_contains($line, 'tưởng nhớ các hội viên')) $section = 'necrology';

            // Detect Community Header (AFE X: ...)
            if (preg_match('/^AFE\s(\d+):\s(.+)$/', $line, $matches)) {
                $section = 'directory'; // Switch back to directory mode
                $code = 'AFE' . $matches[1];
                $rawName = $matches[2];
                $name = str_replace(['-', '  '], ' ', $rawName);
                
                // Location Logic
                $location = trim(explode('-', $rawName)[0]);
                if (str_contains($rawName, 'Nairobi')) $location = 'Nairobi';
                elseif (str_contains($rawName, 'Tonj')) $location = 'Tonj';
                elseif (str_contains($rawName, 'Wau')) $location = 'Wau';
                elseif (str_contains($rawName, 'Juba')) $location = 'Juba';
                elseif (str_contains($rawName, 'Kuajok')) $location = 'Kuajok';
                elseif (str_contains($rawName, 'Provincial Office')) $location = 'Nairobi';

                $currentCommunity = Community::firstOrCreate(
                    ['code' => $code],
                    [
                        'name' => trim($name),
                        'location' => $location,
                        'email' => strtolower(str_replace(' ', '', $location)) . '@congregation.org',
                    ]
                );
                $createdCommunities[] = $currentCommunity;
                continue;
            }

            // Staff Assignments (Directory Mode)
            if ($section === 'directory' && $currentCommunity && preg_match('/^-\s+(P\.|S\.|L\.|D\.|Dn\.)\s+([^(]+)(?:\((.*)\))?.*$/', $line, $matches)) {
                 // ... (Keep existing logic)
                 $title = $matches[1];
                 $namePart = trim($matches[2]); 
                 $descPart = isset($matches[3]) ? trim($matches[3]) : '';
                 
                 // Reuse usage logic...
                 $parts = explode(' ', $namePart);
                 $lastName = $parts[0];
                 $firstName = implode(' ', array_slice($parts, 1));
                 $email = strtolower(str_replace([' ', '.'], '', $namePart)) . '@congregation.org';
                 
                 // Registry Lookup 
                 $upperName = strtoupper($namePart);
                 $regData = $memberRegistry[$upperName] ?? null;

                 $member = Member::firstOrCreate(
                    ['last_name' => $lastName, 'first_name' => $firstName],
                    [
                        'email' => $regData['email'] ?? $email,
                        'phone' => $regData['phone'] ?? null,
                        'dob' => $regData['dob'] ?? Carbon::now()->subYears(40),
                        'entry_date' => $regData['first_profession'] ?? Carbon::now()->subYears(20),
                        'ordination_date' => $regData['ordination'] ?? null,
                        'status' => MemberStatus::Active,
                        'religious_name' => ($title == 'P.') ? 'Father' : (($title == 'L.') ? 'Brother' : 'Cleric'),
                        'community_id' => $currentCommunity->id,
                     ]
                 );
                 
                 // Process Role Translation
                 // ...
                 $englishRole = 'Member';
                 foreach ($roleMap as $vn => $en) {
                     if (str_contains($descPart, $vn)) {
                         $englishRole = $en;
                         break;
                     }
                 }
                 
                 Assignment::create([
                     'member_id' => $member->id,
                     'community_id' => $currentCommunity->id,
                     'role' => $englishRole,
                     'start_date' => Carbon::now()->subYears(1),
                     'is_current' => true
                 ]);
                 $allMembers[] = $member;
            }
            
            // Student Parsing
            if (($section == 'utume' || $section == 'moshi') && preg_match('/^-\s+(S\.|D\.)\s+([^(]+)\s+\(([A-Z]+)\)$/', $line, $matches)) {
                $students[] = [
                    'title' => $matches[1],
                    'name' => trim($matches[2]),
                    'province' => $matches[3],
                    'location' => $section
                ];
            }
            
            // Necrology Parsing
            if ($section == 'necrology' && preg_match('/^[-*]\s+\*\*Tháng\s+\d+:\*\*(.*)$/', $line, $matches)) {
                 $content = $matches[1];
                 $records = explode(',', $content);
                 foreach ($records as $record) {
                    if (preg_match('/(P\.|L\.|S\.)\s+([^(]+)\s+\((\d{4})\)/', trim($record), $recMatch)) {
                        $deceased[] = [
                            'title' => $recMatch[1],
                            'name' => trim($recMatch[2]),
                            'year_death' => $recMatch[3]
                        ];
                    }
                 }
            }
        }


        
        $this->command->info("Seeded " . count($createdCommunities) . " communities and " . count($allMembers) . " members from Markdown.");

        // SEED STUDENTS from db3.md
        if (count($students) > 0) {
            $this->command->info("Seeding " . count($students) . " students...");
            $utumeId = Community::firstOrCreate(['code' => 'AFE17'], ['name' => 'Nairobi Utume', 'email' => 'utume@congregation.org'])->id;
            // Moshi is technically TZA province, but we track them. Let's create a placeholder community "TZA Moshi"
            $moshiId = Community::firstOrCreate(['code' => 'TZA01'], ['name' => 'Moshi Philosophate', 'email' => 'moshi@congregation.org'])->id;
            
            foreach ($students as $stu) {
                 // Check if exists
                $parts = explode(' ', $stu['name']);
                $lastName = $parts[0]; 
                $firstName = implode(' ', array_slice($parts, 1));
                
                $communityId = ($stu['location'] == 'utume') ? $utumeId : $moshiId;
                
                $member = Member::firstOrCreate(
                    ['email' => strtolower(str_replace(' ', '', $lastName . $firstName)) . '@student.org'],
                    [
                        'first_name' => $firstName,
                        'last_name' => $lastName . ' (' . $stu['province'] . ')', // Append Province to Name for clarity
                        'religious_name' => ($stu['title'] == 'D.') ? 'Deacon' : 'Cleric',
                        'community_id' => $communityId,
                        'status' => MemberStatus::Active,
                        'dob' => Carbon::now()->subYears(25),
                        'entry_date' => Carbon::now()->subYears(5),
                    ]
                );
                
                Assignment::create([
                    'member_id' => $member->id,
                    'community_id' => $communityId,
                    'role' => 'Student',
                    'start_date' => Carbon::now()->subMonths(6),
                    'is_current' => true
                ]);
                
                 FormationEvent::create(['member_id' => $member->id, 'stage' => FormationStage::FirstVows, 'started_at' => $member->entry_date, 'notes' => 'Imported Student']);
            }
        }

        // SEED NECROLOGY form db3.md
        if (count($deceased) > 0) {
             $this->command->info("Seeding " . count($deceased) . " deceased records...");
             $heavenId = Community::firstOrCreate(['code' => 'DEC'], ['name' => 'Deceased Members', 'email' => 'heaven@congregation.org'])->id;
             
             foreach ($deceased as $dec) {
                // Name cleanup
                $parts = explode(' ', $dec['name']);
                $lastName = $parts[0]; 
                $firstName = implode(' ', array_slice($parts, 1));
                
                Member::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'religious_name' => 'Member',
                    'email' => 'deceased_' . uniqid() . '@memory.org',
                    'community_id' => $heavenId,
                    'status' => MemberStatus::Deceased,
                    'dob' => Carbon::createFromFormat('Y', $dec['year_death'])->subYears(60),
                    'date_of_death' => Carbon::createFromFormat('Y', $dec['year_death'])->startOfYear(),
                    'entry_date' => Carbon::createFromFormat('Y', $dec['year_death'])->subYears(40),
                ]);
             }
        }

        // 5. Projects & Financial Data (Linked to real communities/members)
        $this->command->info('Creating projects and expenses...');
        
        $expenseCategories = ['Maintenance', 'Utilities', 'Food', 'Healthcare', 'Travel', 'Educational', 'Liturgical'];

        foreach ($createdCommunities as $community) {
             // A. General Community Expenses (Operational)
            for ($m = 0; $m < 12; $m++) { // Last 12 months
                $monthDate = Carbon::now()->subMonths($m)->startOfMonth();
                // Create 3-5 expenses per month
                for ($e = 0; $e < rand(3, 8); $e++) {
                    Expense::create([
                        'community_id' => $community->id,
                        'category' => $expenseCategories[array_rand($expenseCategories)],
                        'description' => fake()->sentence(3),
                        'amount' => rand(5000, 200000), // In cents: $50 to $2000
                        'date' => $monthDate->copy()->addDays(rand(1, 28)),
                        'created_by' => 1,
                        'is_locked' => $m > 1, // Lock older expenses
                    ]);
                }
            }

            // B. Projects with Expenses
            if (count($allMembers) > 0) {
                for ($p = 0; $p < rand(1, 2); $p++) {
                    $status = ['planned', 'active', 'completed'][rand(0, 2)];
                    $project = Project::create([
                        'name' => fake()->company() . ' Initiative',
                        'community_id' => $community->id,
                        'manager_id' => $allMembers[array_rand($allMembers)]->id, // Assign to random real member
                        'start_date' => Carbon::now()->subMonths(rand(1, 6)),
                        'status' => $status,
                        'budget' => rand(5000, 50000), 
                    ]);
                    
                    if ($status !== 'planned') {
                        $spent = 0;
                        $expenseCount = rand(5, 15);
                        for ($pem = 0; $pem < $expenseCount; $pem++) {
                             $amount = rand(10000, 500000); 
                             $spent += $amount;
                             if ($spent > ($project->budget * 100 * 1.2)) break;

                             Expense::create([
                                'community_id' => $community->id,
                                'project_id' => $project->id,
                                'category' => 'Project Materials',
                                'description' => 'Project expense: ' . fake()->word(),
                                'amount' => $amount,
                                'date' => Carbon::now()->subMonths(rand(0, 4))->subDays(rand(1, 30)),
                                'created_by' => 1,
                            ]);
                        }
                    }
                }
            }
        }

        // 6. Periodic Events
        foreach ($createdCommunities as $community) {
            $startDate = Carbon::now()->addDays(rand(10, 200));
            PeriodicEvent::create([
                'name' => 'Community Feast Day',
                'type' => 'feast',
                'start_date' => $startDate,
                'end_date' => $startDate->copy()->endOfDay(), // Fixed missing end_date
                'recurrence' => 'annual',
                'is_recurring' => true,
                'community_id' => $community->id,
            ]);
        }
        // 7. Reminders & Logs
        if (count($createdCommunities) > 0) {
            Reminder::create([
                'type' => 'other',
                'title' => 'Submit Financial Report',
                'reminder_date' => Carbon::now()->addDays(2),
                'community_id' => $createdCommunities[0]->id,
                'is_sent' => false,
                'created_by' => 1,
            ]);
        }
        
        $this->command->info('Financial demo data seeded successfully!');
    }

    private function parseDate($dateStr) {
        if (empty($dateStr) || $dateStr === '-') return null;
        try {
            // Format: 15.08.1966 or 15.08.1966 (VK)
            $cleanDate = trim(explode(' ', $dateStr)[0]);
            return Carbon::createFromFormat('d.m.Y', $cleanDate);
        } catch (\Exception $e) {
            return null;
        }
    }
}