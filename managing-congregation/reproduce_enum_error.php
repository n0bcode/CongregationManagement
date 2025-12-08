<?php

use App\Models\Member;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Member statuses...\n";

try {
    Member::withoutGlobalScopes()->withTrashed()->chunk(100, function ($members) {
        foreach ($members as $member) {
            try {
                $status = $member->status; // This triggers the cast
            } catch (\ValueError $e) {
                echo "Error on Member ID {$member->id}: " . $e->getMessage() . "\n";
                echo "Raw value: " . \Illuminate\Support\Facades\DB::table('members')->where('id', $member->id)->value('status') . "\n";
            }
        }
    });
    echo "Check complete.\n";
} catch (\Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
