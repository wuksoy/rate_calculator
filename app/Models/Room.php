<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<\Database\Factories\RoomFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'code',
        'unit',
        'bedding',
        'extra_bed',
        'max_total_occupancy',
        'max_adult_occupancy',
        'max_child_occupancy',
        'base_rate_occupancy',
        'size',
        'rate_high_season',
        'rate_low_season',
        'rate_peak_season',
        'rate_shoulder_season',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'integer',
        'unit' => 'integer',
        'max_total_occupancy' => 'integer',
        'max_adult_occupancy' => 'integer',
        'max_child_occupancy' => 'integer',
        'base_rate_occpancy' => 'float',
        'size' => 'float',
        'rate_high_season' => 'float',
        'rate_low_season' => 'float',
        'rate_peak_season' => 'float',
        'rate_shoulder_season' => 'float',
    ];

}
