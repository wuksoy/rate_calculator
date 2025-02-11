<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    /** @use HasFactory<\Database\Factories\MealsFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'base_rate',
        'promo_rate',
    ];

    protected $casts = [
        'base_rate' => 'float',
        'promo_rate' => 'float',
    ];
}
