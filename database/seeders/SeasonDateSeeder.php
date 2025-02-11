<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\SeasonDate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeasonDateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            'High Season' => [['01-10', '04-13']],
            'Low Season' => [['05-10', '07-31'], ['09-01', '09-30']],
            'Peak Season' => [['12-24', '12-31'], ['01-01', '01-09']],
            'Shoulder Season' => [['04-14', '05-09'], ['08-01', '08-31'], ['10-01', '12-23']],
        ];

        foreach ($seasons as $seasonName => $dateRanges) {
            // Get the season id
            $season_id = Season::where('name', $seasonName)->first()->id;
            foreach ($dateRanges as $range) {
                SeasonDate::create([
                    'season_id' => $season_id,
                    'start_date' => date('Y') . '-' . $range[0],
                    'end_date' => date('Y') . '-' . $range[1]
                ]);
            }
        }
    }
}
