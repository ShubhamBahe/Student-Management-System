<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;

class AcademicYearSeeder extends Seeder {
    public function run(): void {
        AcademicYear::create([
            'name'       => '2025-2026',
            'start_date' => '2025-04-01',
            'end_date'   => '2026-03-31',
            'is_current' => true,
        ]);
        echo "Academic Year Created!\n";
    }
}