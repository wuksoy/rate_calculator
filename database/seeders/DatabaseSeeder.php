<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoomSeeder::class);
        $this->call(SeasonSeeder::class);
        $this->call(SeasonDateSeeder::class);
        $this->call(MealsSeeder::class);
        $this->call(ActivityTypeSeeder::class);
        $this->call(ActivitySeeder::class);
    }
}
