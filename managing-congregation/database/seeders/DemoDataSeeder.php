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
        
        // 8. FDB Data Enrichment (Address, Phone, Patron, Specific Roles)
        $this->command->info('Running FDB Enrichment Pass...');
        $fullContent = file_get_contents($mergedPath);
        $this->parseFdbData($fullContent);

        $this->command->info('Financial demo data seeded successfully!');
    }

    private function parseFdbData(string $content)
    {
        // Extract FDB section (Added via append)
        // Look for header "### FDB_DATA"
        $parts = explode('### FDB_DATA', $content);
        if (count($parts) < 2) {
            $this->command->warn('No FDB_DATA section found.');
            return;
        }
        $fdbContent = $parts[1];

        // Split by numbered sections (e.g., "1. EMBU - AFE 2")
        // Regex to split: line start, number, dot, space...
        $sections = preg_split('/^(\d+)\.\s+(.+)$/m', $fdbContent, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        // Sections array: [0]=>"Intro Text", [1]=>"1", [2]=>"EMBU...", [3]=>"body", ...
        $this->command->info("FDB Split Segments: " . count($sections));

        for ($i = 0; $i < count($sections); $i++) {
             // Look for the "Number" part of the triplet
            if (is_numeric(trim($sections[$i])) && isset($sections[$i+1]) && isset($sections[$i+2])) {
                
                $header = trim($sections[$i+1]);
                $body = $sections[$i+2];

                // Extract Community Name
                // Header usually: "EMBU - AFE 2" or "NAIROBI BOSCO BOYS - AFE 13"
                $namePart = explode('-', $header)[0];
                $communityName = trim($namePart);

                // Fuzzy Find Community
                $community = $this->findCommunityFuzzy($communityName);
                if (!$community) {
                     // Try cleaning more?
                     // Sometimes headers are "MERU PRESENCE"
                     $community = $this->findCommunityFuzzy(preg_replace('/PRESENCE/i', '', $communityName));
                }

                if ($community) {
                    $this->enrichCommunityData($community, $body);
                    $this->parseFdbMembers($community, $body);
                } else {
                    // Create Missing Community (e.g., Meru, Kitale)
                    $cleanName = trim(preg_replace('/(PRESENCE|PARISH|V\.T\.C\.)/i', '', $communityName));
                    $cleanName = trim($cleanName, " -");
                    if (empty($cleanName)) $cleanName = $communityName;

                    $code = 'EXT-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $cleanName), 0, 4)) . rand(10, 99);
                    
                    $community = Community::create([
                        'code' => $code,
                        'name' => $cleanName,
                        'location' => $cleanName, // Default location to name, will be enriched
                        'email' => strtolower(str_replace(' ', '', $cleanName)) . '@congregation.org',
                    ]);
                    
                    $this->command->info("Created missing FDB community: {$cleanName}");
                    $this->enrichCommunityData($community, $body);
                    $this->parseFdbMembers($community, $body);
                }
                
                // Advance index by 2 (plus loop's 1 = 3) to skip Header and Body
                $i += 2;
            }
        }
    }

    private function enrichCommunityData($community, $body)
    {
        $lines = explode("\n", $body);
        $updateData = [];

        foreach ($lines as $line) {
            $line = trim($line);
            
            // Bilingual Patterns
            // Patron: "Thánh bổn mạng:" or "Patron:" or "Patron Saint:"
            if (preg_match('/(Thánh bổn mạng|Patron Saint|Patron):\s*\**([^*]+)\**/ui', $line, $m)) {
                $updateData['patron_saint'] = trim($m[2], " .");
            }
            // Address: "Địa chỉ:" or "Address:" or "Location:"
            if (preg_match('/(Địa chỉ|Address|Location):\s*\**([^*]+)\**/ui', $line, $m)) {
                $updateData['location'] = trim($m[2], " .");
            }
            // Contact: "Liên hệ:" or "Contact:"
            if (preg_match('/(Liên hệ|Contact|Contact Info):\s*\**([^*]+)\**/ui', $line, $m)) {
                $contact = trim($m[2], " .");
                // Email extraction
                if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $contact, $em)) {
                    $updateData['email'] = $em[0];
                }
                // Phone extraction
                if (preg_match('/(\+\d[\d\s]+)/', $contact, $pm)) {
                    $updateData['phone'] = trim($pm[1]);
                }
            }
        }

        if (!empty($updateData)) {
            $community->update($updateData);
        }
    }

    private function parseFdbMembers($community, $body)
    {
        // Line regex: "- Cha Name: Role" or "- Fr. Name: Role"
        // Titles: Cha|Fr\.?|Sư huynh|Br\.?|Bro\.?|Thầy|Cl\.?|Phó tế|Dcn\.?|Đức Cha|Bp\.?|Bishop
        // Uses 'ui' modifier for case-insensitivity
        $titles = 'Cha|Fr\.?|Father|Sư huynh|Br\.?|Bro\.?|Thầy|Cl\.?|Cleric|Phó tế|Dcn\.?|Deacon|Đức Cha|Bp\.?|Bishop';
        
        $lines = explode("\n", $body);
        foreach ($lines as $line) {
            if (preg_match('/^\s*-\s+('.$titles.')\s+([^:]+)(?::(.*)|$)/ui', $line, $matches)) {
                 $titleRaw = trim($matches[1]);
                 $nameRaw = trim($matches[2]);
                 $roleRaw = isset($matches[3]) ? trim($matches[3], " .") : '';

                 $member = $this->findMemberFuzzy($nameRaw);
                 
                 if (!$member) {
                     // Create missing member (e.g. from Presences)
                     $parts = explode(' ', $nameRaw);
                     $firstName = array_shift($parts);
                     $lastName = implode(' ', $parts);
                     if (empty($lastName)) { $lastName = $firstName; $firstName = ''; }

                     $email = strtolower(preg_replace('/[^a-z0-9]/i', '', $firstName . $lastName)) . rand(10,99) . '@congregation.org';
                     
                     $member = Member::create([
                         'first_name' => $firstName,
                         'last_name' => $lastName,
                         'email' => $email,
                         'community_id' => $community->id,
                         'status' => MemberStatus::Active,
                         'religious_name' => (str_contains($titles, 'Cha') || str_contains($titles, 'Fr')) ? 'Father' : 'Brother',
                         'dob' => Carbon::now()->subYears(40),
                         'entry_date' => Carbon::now()->subYears(20),
                     ]);
                     $this->command->info("Created missing FDB Member: {$nameRaw}");
                 }

                 if ($member) {
                     // Check if titled indicates religious status change?
                     // E.g. found as "Cleric" in registry but listed as "Fr." now?
                     // Currently only updating Assignments.
                     
                     if ($member->community_id !== $community->id && $community->id) {
                         $member->update(['community_id' => $community->id]);
                     }
                     
                     // Upsert Assignment/Role
                     if (!empty($roleRaw)) {
                         $roleName = $this->translateDetailedRole($roleRaw);
                         if ($roleName) {
                             // Deactivate old assignment if role differs? No, members can have multiple roles.
                             // Just ensure this role exists.
                             $exists = Assignment::where('member_id', $member->id)
                                 ->where('community_id', $community->id)
                                 ->where('role', $roleName)
                                 ->exists();
                             
                             if (!$exists) {
                                 Assignment::create([
                                     'member_id' => $member->id,
                                     'community_id' => $community->id,
                                     'role' => $roleName,
                                     'start_date' => Carbon::now()->startOfYear(),
                                     'is_current' => true
                                 ]);
                             }
                         }
                     }
                 }
            }
        }
    }

    private function findCommunityFuzzy($name)
    {
        $name = trim($name);
        if (empty($name)) return null;
        
        // Exact
        $c = Community::where('name', $name)->first();
        if ($c) return $c;
        
        // Like
        $c = Community::where('name', 'like', "%{$name}%")->first();
        if ($c) return $c;

        return null;
    }

    private function findMemberFuzzy($nameRaw)
    {
        // Logic: Try to match surnames or significant name parts
        // "Patrick Mugendi Njiru" -> DB: "NJIRU Patrick Mugendi"
        
        $parts = explode(' ', $nameRaw);
        if (count($parts) < 2) return null;

        // Try last word as surname
        $maybeSurname = end($parts);
        
        // Try query
        $candidates = Member::where('last_name', 'like', "%{$maybeSurname}%")
                      ->orWhere('first_name', 'like', "%{$maybeSurname}%")
                      ->get();

        foreach ($candidates as $candidate) {
            $dbFull = strtolower($candidate->first_name . ' ' . $candidate->last_name);
            $inputFull = strtolower($nameRaw);
            
            // Check intersection of parts
            $inputParts = explode(' ', $inputFull);
            $matchCount = 0;
            foreach ($inputParts as $p) {
                if (strlen($p) > 2 && str_contains($dbFull, $p)) {
                    $matchCount++;
                }
            }
            
            // If significant match
            if ($matchCount >= 2 || ($matchCount == 1 && count($inputParts) == 2)) {
                return $candidate;
            }
        }
        return null;
    }

    private function translateDetailedRole($roleRaw) {
        // 1. Check for English in Parentheses: "Giám đốc (Rector)"
        if (preg_match('/\(([^)]+)\)/', $roleRaw, $m)) {
            $en = $m[1];
             $map = [
                'Rector' => 'Rector',
                'VR' => 'Vice Rector',
                'Princ' => 'Principal',
                'Principal' => 'Principal',
                'Adm' => 'Administrator',
                'Admin' => 'Administrator',
                'PP' => 'Parish Priest',
                'Parish Priest' => 'Parish Priest',
                'Asst PP' => 'Assistant Parish Priest',
                'YMC' => 'Youth Minister',
                'Confessor' => 'Confessor',
                'PT' => 'Practical Trainee',
                'In-charge' => 'In-Charge',
                'Curate' => 'Curate',
            ];
            foreach ($map as $k => $v) {
                if (stripos($en, $k) !== false) return $v;
            }
            return ucfirst($en);
        }
        
        // 2. Direct English Roles (if no parens or standalone)
        $englishRoles = ['Rector', 'Vice Rector', 'Principal', 'Administrator', 'Parish Priest', 'Curate', 'Assistant Parish Priest', 'Youth Minister', 'Confessor', 'Practical Trainee', 'In-Charge', 'Treasurer', 'Secretary'];
        foreach ($englishRoles as $er) {
            if (stripos($roleRaw, $er) !== false) return $er;
        }

        // 3. Fallback for Vietnamese
        $vnMap = [
            'Giám đốc' => 'Rector',
            'Phó Giám đốc' => 'Vice Rector',
            'Hiệu trưởng' => 'Principal',
            'Quản lý' => 'Administrator',
            'Cha sở' => 'Parish Priest',
            'Phó cha sở' => 'Assistant Parish Priest',
            'Mục vụ giới trẻ' => 'Youth Minister',
            'Thực tập sinh' => 'Practical Trainee',
            'Phụ trách' => 'In-Charge',
        ];
        foreach ($vnMap as $k => $v) {
            if (stripos($roleRaw, $k) !== false) return $v;
        }
        return null;
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