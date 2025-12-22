<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CleanAndSeedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wipe the database and seed it with demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Wiping and migrating database...');
        Artisan::call('migrate:fresh', [], $this->getOutput());

        $this->info('Seeding demo data...');
        Artisan::call('db:seed', ['--class' => 'DemoDataSeeder'], $this->getOutput());

        $this->info('Database cleaned and seeded successfully!');
    }
}
