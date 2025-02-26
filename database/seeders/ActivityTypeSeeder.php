<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [ 'name' => 'Wellness' ],
            [ 'name' => 'R&B' ],
            [ 'name' => 'Activities' ],
            [ 'name' => 'Kids' ],
            [ 'name' => 'Sales' ],
            [ 'name' => 'Other' ],
            [ 'name' => 'Afternoon / Aperif' ],
            [ 'name' => 'Lunch' ],
            [ 'name' => 'Dinner' ],
        ];

        foreach ($types as $type) {
            \App\Models\ActivityType::create($type);
        }
    }
}
