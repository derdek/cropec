<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PublicHolidaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $year = now()->addYear()->format('Y');
        DB::table('public_holidays')->insert([
            [
                'name' => 'New Year',
                'date' => '2024-12-31',
            ],
            [
                'name' => 'New Year',
                'date' => $year . '-01-01',
            ]
        ]);
    }
}
