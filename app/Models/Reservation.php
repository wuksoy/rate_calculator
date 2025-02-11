<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'room_id',
        'meal_id',
        'check_in', // date
        'nights',
        'adults',
        'children',
        'adavance_discount', //boolean
        'seaplane_discount_type', // boolean (base occupants or all occupants)
        'meal_discount_type', // boolean (base occupants or all occupants)
        'room_discount_type', // boolean (base occupants or all occupants)
        'seaplane_discount', // float
        'meal_discount', // float
        'room_discount', // float
        'total_without_discount',
        'discounted_amount',
        'total',
    ];

    protected $casts = [
        'room_id' => 'integer',
        'meal_id' => 'integer',
        'check_in' => 'date',
        'nights' => 'integer',
        'adults' => 'integer',
        'children' => 'integer',
        'adavance_discount' => 'boolean',
        'seaplane_discount_type' => 'boolean',
        'meal_discount_type' => 'boolean',
        'room_discount_type' => 'boolean',
        'seaplane_discount' => 'float',
        'meal_discount' => 'float',
        'room_discount' => 'float',
        'total_without_discount' => 'float',
        'discounted_amount' => 'float',
        'total' => 'float',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class);
    }
}
