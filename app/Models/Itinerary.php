<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    /** @use HasFactory<\Database\Factories\ItineraryFactory> */
    use HasFactory;

    protected $fillable =[
        'guest_name',
        'meal_plan',
        'checkin',
        'checkout',
        'activities',
    ];

    protected $casts = [
        'checkin' => 'date',
        'checkout' => 'date',
        'meal_plan' => 'integer',
        'activities' => 'array',
    ];
}
