<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $fillable = [
        'name',
        'details',
        'activity_type_id'
    ];

    protected $casts = [
        'activity_type_id' => 'integer'
    ];

    public function activity_type()
    {
        return $this->belongsTo(ActivityType::class);
    }
}
