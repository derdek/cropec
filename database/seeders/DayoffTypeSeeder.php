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
            ['name' => 'Vacation', 'default_days_per_year' => 20],
            ['name' => 'Day Off', 'default_days_per_year' => 5],
            ['name' => 'Sick Leave', 'default_days_per_year' => null],
            ['name' => 'Business Trip', 'default_days_per_year' => null],
        ]);
    }
}
