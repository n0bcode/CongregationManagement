<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class FooterSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $footerSettings = [
            [
                'key' => 'footer_description',
                'value' => 'Supporting our community with grace and efficiency. Managing member records, events, and reports for the congregation.',
                'type' => 'string',
                'description' => 'Footer description text',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_address',
                'value' => '123 Congregation Ave, City, Country',
                'type' => 'string',
                'description' => 'Footer contact address',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_email',
                'value' => 'contact@congregation.org',
                'type' => 'string',
                'description' => 'Footer contact email',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_copyright',
                'value' => '&copy; ' . date('Y') . ' ' . config('app.name') . '. All rights reserved.',
                'type' => 'string',
                'description' => 'Footer copyright text',
                'group' => 'footer',
            ],
            [
                'key' => 'footer_logo_path',
                'value' => null,
                'type' => 'string',
                'description' => 'Footer custom logo file path',
                'group' => 'footer',
            ],
        ];

        foreach ($footerSettings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }

        $this->command->info('✅ Footer settings seeded successfully.');
    }
}
