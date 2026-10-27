<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder {
    public function run(): void {
        $settings = [
            ['key' => 'school_name',    'value' => 'Al-Noor Public School'],
            ['key' => 'school_email',   'value' => 'info@school.com'],
            ['key' => 'school_phone',   'value' => '+92-300-0000000'],
            ['key' => 'school_address', 'value' => 'Hyderabad, Sindh, Pakistan'],
            ['key' => 'school_logo',    'value' => 'logo.png'],
            ['key' => 'currency',       'value' => 'PKR'],
            ['key' => 'timezone',       'value' => 'Asia/Karachi'],
            ['key' => 'session_year',   'value' => '2025-2026'],
            ['key' => 'school_tagline', 'value' => 'Education is the key to success'],
            ['key' => 'fine_per_day',   'value' => '5'],
        ];
        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
        echo "Settings Created!\n";
    }
}