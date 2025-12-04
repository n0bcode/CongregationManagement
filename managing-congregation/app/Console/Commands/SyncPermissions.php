<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\RouteScannerInterface;
use App\Models\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:sync
                            {--dry-run : Show what would be synced without making changes}
                            {--force : Force sync without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync permissions from route definitions to database';

    /**
     * Execute the console command.
     */
    public function handle(RouteScannerInterface $routeScanner): int
    {
        $this->info('🔍 Scanning routes for permissions...');

        try {
            // Scan routes for permissions
            $scannedPermissions = $routeScanner->scanRoutes();

            if ($scannedPermissions->isEmpty()) {
                $this->warn('⚠️  No permissions found in routes');

                return Command::SUCCESS;
            }

            $this->info("Found {$scannedPermissions->count()} permissions in routes");

            // Get existing permissions from database
            $existingPermissions = Permission::all()->keyBy('key');
            $scannedKeys = $scannedPermissions->pluck('key');

            // Calculate changes
            $newPermissions = $scannedPermissions->filter(function ($permission) use ($existingPermissions) {
                return ! $existingPermissions->has($permission['key']);
            });

            $updatedPermissions = $scannedPermissions->filter(function ($permission) use ($existingPermissions) {
                if (! $existingPermissions->has($permission['key'])) {
                    return false;
                }

                $existing = $existingPermissions->get($permission['key']);

                return $existing->name !== $permission['name']
                    || $existing->module !== $permission['module']
                    || ! $existing->is_active;
            });

            $orphanedPermissions = $existingPermissions->filter(function ($permission) use ($scannedKeys) {
                return ! $scannedKeys->contains($permission->key) && $permission->is_active;
            });

            // Display summary
            $this->newLine();
            $this->info('📊 Sync Summary:');
            $this->table(
                ['Type', 'Count'],
                [
                    ['New permissions', $newPermissions->count()],
                    ['Updated permissions', $updatedPermissions->count()],
                    ['Orphaned permissions', $orphanedPermissions->count()],
                    ['Total in routes', $scannedPermissions->count()],
                    ['Total in database', $existingPermissions->count()],
                ]
            );

            // Show details if there are changes
            if ($newPermissions->isNotEmpty()) {
                $this->newLine();
                $this->info('➕ New Permissions:');
                $this->table(
                    ['Key', 'Name', 'Module'],
                    $newPermissions->map(fn ($p) => [$p['key'], $p['name'], $p['module']])->toArray()
                );
            }

            if ($updatedPermissions->isNotEmpty()) {
                $this->newLine();
                $this->info('🔄 Updated Permissions:');
                $this->table(
                    ['Key', 'Name', 'Module'],
                    $updatedPermissions->map(fn ($p) => [$p['key'], $p['name'], $p['module']])->toArray()
                );
            }

            if ($orphanedPermissions->isNotEmpty()) {
                $this->newLine();
                $this->warn('⚠️  Orphaned Permissions (will be marked inactive):');
                $this->table(
                    ['Key', 'Name', 'Module'],
                    $orphanedPermissions->map(fn ($p) => [$p->key, $p->name, $p->module])->toArray()
                );
            }

            // Dry run mode
            if ($this->option('dry-run')) {
                $this->newLine();
                $this->info('🔍 Dry run mode - no changes made');

                return Command::SUCCESS;
            }

            // Confirm before proceeding
            if (! $this->option('force')) {
                if (! $this->confirm('Do you want to proceed with these changes?', true)) {
                    $this->info('❌ Sync cancelled');

                    return Command::SUCCESS;
                }
            }

            // Perform sync
            $this->newLine();
            $this->info('🔄 Syncing permissions...');

            DB::transaction(function () use ($newPermissions, $updatedPermissions, $orphanedPermissions) {
                $stats = [
                    'new' => 0,
                    'updated' => 0,
                    'orphaned' => 0,
                ];

                // Create new permissions
                foreach ($newPermissions as $permission) {
                    Permission::create([
                        'key' => $permission['key'],
                        'name' => $permission['name'],
                        'module' => $permission['module'],
                        'is_active' => true,
                    ]);
                    $stats['new']++;
                }

                // Update existing permissions
                foreach ($updatedPermissions as $permission) {
                    Permission::where('key', $permission['key'])->update([
                        'name' => $permission['name'],
                        'module' => $permission['module'],
                        'is_active' => true,
                    ]);
                    $stats['updated']++;
                }

                // Mark orphaned permissions as inactive
                foreach ($orphanedPermissions as $permission) {
                    $permission->update(['is_active' => false]);
                    $stats['orphaned']++;
                }

                $this->info("✓ Created {$stats['new']} new permissions");
                $this->info("✓ Updated {$stats['updated']} permissions");
                $this->info("✓ Marked {$stats['orphaned']} permissions as inactive");
            });

            Log::info('Permissions synced successfully', [
                'new' => $newPermissions->count(),
                'updated' => $updatedPermissions->count(),
                'orphaned' => $orphanedPermissions->count(),
            ]);

            $this->newLine();
            $this->info('✅ Permission sync completed successfully!');

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Failed to sync permissions: '.$e->getMessage());
            Log::error('Permission sync failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }
}
