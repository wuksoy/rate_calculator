<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            ['name' => 'High Season'],
            ['name' => 'Low Season'],
            ['name' => 'Peak Season'],
            ['name' => 'Shoulder Season'],
        ];

        DB::table('seasons')->insert($seasons);
    }
}
