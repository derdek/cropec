<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DayoffTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('dayoff_types')->insert([
            [
                'name' => 'Vacation',
                'default_days_per_year' => 20,
                'color' => '#62a0ea',
            ],
            [
                'name' => 'Day Off',
                'default_days_per_year' => 5,
                'color' => '#8ff0a4',
            ],
            [
                'name' => 'Sick Leave',
                'default_days_per_year' => null,
                'color' => '#f9f06b',
            ],
            [
                'name' => 'Business Trip',
                'default_days_per_year' => null,
                'color' => '#ffbe6f',
            ],
        ]);
    }
}
