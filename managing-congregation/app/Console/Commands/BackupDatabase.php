<?php

namespace App\Console\Commands;

use App\Models\SystemSetting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\DbDumper\Databases\MySql;

class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the database to storage';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! SystemSetting::get('backup_enabled', false)) {
            $this->info('Backups are disabled in system settings.');
            return;
        }

        $this->info('Starting database backup...');

        $filename = 'backup-' . date('Y-m-d-H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        // Ensure directory exists
        if (! file_exists(storage_path('app/backups'))) {
            mkdir(storage_path('app/backups'), 0755, true);
        }

        try {
            MySql::create()
                ->setDbName(config('database.connections.mysql.database'))
                ->setUserName(config('database.connections.mysql.username'))
                ->setPassword(config('database.connections.mysql.password'))
                ->setHost(config('database.connections.mysql.host'))
                ->setPort(config('database.connections.mysql.port'))
                ->dumpToFile($path);

            // Encrypt and store
            $content = file_get_contents($path);
            $encrypted = encrypt($content);
            
            Storage::put('backups/' . $filename . '.encrypted', $encrypted);
            
            // Remove raw SQL file
            unlink($path);

            $this->info('Backup completed successfully: ' . $filename . '.encrypted');
        } catch (\Exception $e) {
            $this->error('Backup failed: ' . $e->getMessage());
        }
    }
}
