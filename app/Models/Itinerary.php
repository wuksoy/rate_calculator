<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    /** @use HasFactory<\Database\Factories\ItineraryFactory> */
    use HasFactory;

    protected $fillable =[
        'name',
        'activities',
    ];

    protected $casts = [
        'activities' => 'array',
    ];
}
