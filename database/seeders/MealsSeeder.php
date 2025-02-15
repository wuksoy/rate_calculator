<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MealsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $meals = [
            [
                'name' => 'Half Board',
                'base_rate' => 130.00,
                'promo_rate' => 0.00,
            ],
            [
                'name' => 'Full Board',
                'base_rate' => 190.00,
                'promo_rate' => 60.00,
            ],
            [
                'name' => 'All Inclusive',
                'base_rate' => 235.00,
                'promo_rate' => 105.00,
            ],
            [
                'name' => 'Premium All Inclusive',
                'base_rate' => 310.00,
                'promo_rate' => 180.00,
            ],
        ];

        foreach ($meals as $meal) {
            \App\Models\Meal::create($meal);
        }
    }
}
