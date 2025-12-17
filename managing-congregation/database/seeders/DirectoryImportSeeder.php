<?php

namespace Database\Seeders;

use App\Models\Community;
use App\Models\Member;
use App\Models\Role;
use App\Models\Assignment;
use App\Models\PeriodicEvent;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DirectoryImportSeeder extends Seeder
{
    private string $filePath;
    private array $roles = [];
    private array $communities = [];
    private int $importedMembers = 0;
    private int $importedEvents = 0;
    private int $importedAssignments = 0;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->filePath = database_path('seeders/data/Directory-2025-2026.txt');

        if (!file_exists($this->filePath)) {
            $this->command->error("❌ File not found: {$this->filePath}");
            $this->command->info("💡 Please copy Directory-2025-2026.txt to database/seeders/data/");
            return;
        }

        $this->command->info("📖 Reading directory file...");
        
        // Load roles into memory for quick lookup
        $this->loadRoles();

        DB::beginTransaction();
        
        try {
            // Step 1: Import Houses/Communities
            $this->command->info("\n🏠 Step 1: Importing Houses/Communities...");
            $this->importHouses();

            // Step 2: Import Members from Index
            $this->command->info("\n👥 Step 2: Importing Members from Index...");
            $this->importMembersFromIndex();

            // Step 3: Import Birthdays
            $this->command->info("\n🎂 Step 3: Importing Birthdays...");
            $this->importBirthdays();

            // Step 4: Import Deceased Members
            $this->command->info("\n⚰️  Step 4: Importing Deceased Members...");
            $this->importDeceased();

            DB::commit();

            // Summary
            $this->command->info("\n✅ Import completed successfully!");
            $this->command->table(
                ['Category', 'Count'],
                [
                    ['Communities', count($this->communities)],
                    ['Members', $this->importedMembers],
                    ['Assignments', $this->importedAssignments],
                    ['Events', $this->importedEvents],
                ]
            );

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("\n❌ Import failed: " . $e->getMessage());
            Log::error('Directory import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Load roles into memory
     */
    private function loadRoles(): void
    {
        $this->roles = Role::all()->keyBy('code')->toArray();
        $this->command->info("✓ Loaded " . count($this->roles) . " roles");
    }

    /**
     * Import houses/communities from directory
     */
    private function importHouses(): void
    {
        $content = file_get_contents($this->filePath);
        $lines = explode("\n", $content);

        $currentHouse = null;
        $inHouseSection = false;

        foreach ($lines as $lineNum => $line) {
            $line = trim($line);

            // Detect house code (AFE X - AFE XX)
            if (preg_match('/^(AFE\s+\d+)\s*$/i', $line, $matches)) {
                // Save previous house if exists
                if ($currentHouse && isset($currentHouse['code'])) {
                    $this->saveHouse($currentHouse);
                }

                $currentHouse = [
                    'code' => $matches[1],
                    'name' => '',
                    'location' => '',
                    'address_lines' => [],
                ];
                $inHouseSection = true;
                continue;
            }

            // Stop at certain sections
            if (preg_match('/^(CONFRERES OUTSIDE|DECEASED SALESIANS|BIRTHDAYS|INDEX CONFRERES)/i', $line)) {
                if ($currentHouse && isset($currentHouse['code'])) {
                    $this->saveHouse($currentHouse);
                }
                $inHouseSection = false;
                break;
            }

            if ($inHouseSection && $currentHouse) {
                // Extract house name (usually starts with St., Don Bosco, Mary, etc.)
                if (empty($currentHouse['name']) && preg_match('/^(St\.|Don\s+Bosco|Mary|Blessed|Our\s+Lady)/i', $line)) {
                    $currentHouse['name'] = $line;
                    continue;
                }

                // Extract address (P.O. Box, Salesians of Don Bosco, etc.)
                if (preg_match('/^(P\.O\.|P\.o|Salesians|Box|Tel:|Phone:|Email:)/i', $line)) {
                    $currentHouse['address_lines'][] = $line;
                }
            }
        }

        // Save last house
        if ($currentHouse && isset($currentHouse['code'])) {
            $this->saveHouse($currentHouse);
        }
    }

    /**
     * Save house to database
     */
    private function saveHouse(array $houseData): void
    {
        $community = Community::updateOrCreate(
            ['code' => $houseData['code']],
            [
                'name' => $houseData['name'] ?: $houseData['code'],
                'location' => implode("\n", $houseData['address_lines']),
            ]
        );

        $this->communities[$houseData['code']] = $community;
        $this->command->info("  ✓ {$houseData['code']}: {$community->name}");
    }

    /**
     * Import members from INDEX CONFRERES section
     */
    private function importMembersFromIndex(): void
    {
        $content = file_get_contents($this->filePath);
        
        // Normalize line endings (handle both \r\n and \n)
        $content = str_replace("\r\n", "\n", $content);
        $content = str_replace("\r", "\n", $content);

        // Find INDEX CONFRERES section - look for the actual section
        $lines = explode("\n", $content);
        $inIndexSection = false;
        $memberCount = 0;
        $lineCount = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            $lineCount++;

            // Detect start of INDEX CONFRERES - look for the header line
            if (preg_match('/NAME\s+BIRTH\s+1st\s+PR\s+ORD/i', $line)) {
                $inIndexSection = true;
                $this->command->info("  Found INDEX CONFRERES header at line {$lineCount}");
                continue;
            }

            // Stop at end of file or other major sections AFTER index
            if ($inIndexSection && preg_match('/^(CONFRERES OUTSIDE|GENERAL INFORMATION)/i', $line)) {
                $this->command->info("  Stopped at {$line} (line {$lineCount})");
                break;
            }

            if (!$inIndexSection) {
                continue;
            }

            // Skip headers, page numbers, and empty lines
            if (empty($line) || 
                preg_match('/^(NAME|BIRTH|AFE SDB|PROVINCIAL|DIRECTORY|\d+\s*$)/i', $line) ||
                preg_match('/^\s*\d+\s*$/i', $line)) {
                continue;
            }

            // Debug: Show first 5 lines that pass filters
            if ($memberCount < 5) {
                $this->command->info("  Processing line {$lineCount}: " . substr($line, 0, 80));
            }

            // Parse member line with flexible regex
            // Format: Surname Given Name(Role) DD.MM.YYYY DD.MM.YYYY DD.MM.YYYY AFE XX
            // or: Surname Given Name(Role) D.M.YYYY D.M.YYYY AFE XX
            if (preg_match('/^([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\s+([A-Z][a-z]+(?:\s+[A-Z][a-z]+)*)\s*\(([PLSDN])\)\s+(\d{1,2}\.\d{1,2}\.\d{4})\s+(\d{1,2}\.\d{1,2}\.\d{4})\s*(\d{1,2}\.\d{1,2}\.\d{4})?\s+(AFE\s+\d+|OP)/i', $line, $matches)) {
                
                $this->importMember([
                    'surname' => trim($matches[1]),
                    'given_name' => trim($matches[2]),
                    'role_code' => $matches[3],
                    'dob' => $this->parseDate($matches[4]),
                    'first_profession' => $this->parseDate($matches[5]),
                    'ordination' => isset($matches[6]) && !empty(trim($matches[6])) ? $this->parseDate($matches[6]) : null,
                    'house_code' => trim($matches[7]),
                ]);
                $memberCount++;
                
                if ($memberCount % 10 == 0) {
                    $this->command->info("  Imported {$memberCount} members...");
                }
            }
        }

        $this->command->info("  ✓ Total members imported: {$memberCount}");
    }

    /**
     * Import a single member
     */
    private function importMember(array $data): void
    {
        try {
            // Find or create community
            $community = $this->communities[$data['house_code']] ?? 
                         Community::firstOrCreate(['code' => $data['house_code']], ['name' => $data['house_code']]);

            // Check if member already exists (bypass global scopes for seeder)
            $existingMember = Member::withoutGlobalScopes()
                ->where('surname', $data['surname'])
                ->where('given_name', $data['given_name'])
                ->first();

            if ($existingMember) {
                $member = $existingMember;
                $member->update([
                    'community_id' => $community->id,
                    'first_name' => $data['given_name'],
                    'last_name' => $data['surname'],
                    'religious_name' => "{$data['surname']} {$data['given_name']}",
                    'dob' => $data['dob'],
                    'entry_date' => $data['first_profession'],
                    'first_profession_date' => $data['first_profession'],
                    'ordination_date' => $data['ordination'],
                    'status' => 'active',
                ]);
            } else {
                // Create new member (bypass global scopes for seeder)
                $member = Member::withoutGlobalScopes()->create([
                    'community_id' => $community->id,
                    'first_name' => $data['given_name'],
                    'last_name' => $data['surname'],
                    'surname' => $data['surname'],
                    'given_name' => $data['given_name'],
                    'religious_name' => "{$data['surname']} {$data['given_name']}",
                    'dob' => $data['dob'],
                    'entry_date' => $data['first_profession'],
                    'first_profession_date' => $data['first_profession'],
                    'ordination_date' => $data['ordination'],
                    'status' => 'active',
                ]);
            }

            // Create assignment with role
            if (isset($this->roles[$data['role_code']])) {
                Assignment::updateOrCreate(
                    [
                        'member_id' => $member->id,
                        'community_id' => $community->id,
                    ],
                    [
                        'role_id' => $this->roles[$data['role_code']]['id'],
                        'start_date' => $data['first_profession'],
                        'is_current' => true,
                    ]
                );
                $this->importedAssignments++;
            }

            $this->importedMembers++;

        } catch (\Exception $e) {
            $this->command->error("  ❌ Failed to import: {$data['surname']} {$data['given_name']} - " . $e->getMessage());
            throw $e; // Re-throw to trigger rollback
        }
    }

    /**
     * Import birthdays
     */
    private function importBirthdays(): void
    {
        $content = file_get_contents($this->filePath);

        // Find BIRTHDAYS section
        if (!preg_match('/BIRTHDAYS.*?INDEX CONFRERES/s', $content, $sectionMatch)) {
            $this->command->warn("⚠️  BIRTHDAYS section not found");
            return;
        }

        $currentMonth = null;
        $lines = explode("\n", $sectionMatch[0]);

        foreach ($lines as $line) {
            $line = trim($line);

            // Detect month header
            if (preg_match('/^(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER)$/i', $line, $monthMatch)) {
                $currentMonth = $monthMatch[1];
                continue;
            }

            // Parse birthday line: "1 SURNAME Given Name"
            if ($currentMonth && preg_match('/^(\d{1,2})\s+([A-Z]+)\s+([A-Za-z\s]+)$/i', $line, $matches)) {
                $day = $matches[1];
                $surname = trim($matches[2]);
                $givenName = trim($matches[3]);

                // Find member (bypass global scopes for seeder)
                $member = Member::withoutGlobalScopes()
                    ->where('surname', $surname)
                    ->where('given_name', 'LIKE', "%{$givenName}%")
                    ->first();

                if ($member && $member->dob) {
                    PeriodicEvent::updateOrCreate(
                        [
                            'member_id' => $member->id,
                            'type' => 'birthday',
                        ],
                        [
                            'name' => "Birthday - {$member->first_name} {$member->last_name}",
                            'start_date' => $member->dob,
                            'end_date' => $member->dob,
                            'recurrence' => 'annual',
                            'is_recurring' => true,
                        ]
                    );
                    $this->importedEvents++;
                }
            }
        }
    }

    /**
     * Import deceased members
     */
    private function importDeceased(): void
    {
        $content = file_get_contents($this->filePath);

        // Find DECEASED SALESIANS section
        if (!preg_match('/DECEASED SALESIANS.*?BIRTHDAYS/s', $content, $sectionMatch)) {
            $this->command->warn("⚠️  DECEASED SALESIANS section not found");
            return;
        }

        $lines = explode("\n", $sectionMatch[0]);

        foreach ($lines as $line) {
            $line = trim($line);

            // Parse: "P. SURNAME Name Location DD-MM-YYYY"
            if (preg_match('/^([PLS])\.\s+([A-Z]+)\s+([A-Za-z\s,]+?)\s+(\d{2}-\d{2}-\d{4})$/i', $line, $matches)) {
                $surname = trim($matches[2]);
                $deathDate = $this->parseDateDash($matches[4]);

                $member = Member::withoutGlobalScopes()->where('surname', $surname)->first();

                if ($member) {
                    $member->update([
                        'date_of_death' => $deathDate,
                        'is_deceased' => true,
                        'status' => 'deceased',
                    ]);

                    PeriodicEvent::updateOrCreate(
                        [
                            'member_id' => $member->id,
                            'type' => 'death',
                        ],
                        [
                            'name' => "RIP - {$member->first_name} {$member->last_name}",
                            'start_date' => $deathDate,
                            'end_date' => $deathDate,
                            'recurrence' => 'annual',
                            'is_recurring' => false,
                        ]
                    );
                    $this->importedEvents++;
                }
            }
        }
    }

    /**
     * Parse date in DD.MM.YYYY format
     */
    private function parseDate(string $date): ?Carbon
    {
        try {
            return Carbon::createFromFormat('d.m.Y', trim($date));
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse date in DD-MM-YYYY format
     */
    private function parseDateDash(string $date): ?Carbon
    {
        try {
            return Carbon::createFromFormat('d-m-Y', trim($date));
        } catch (\Exception $e) {
            return null;
        }
    }
}
