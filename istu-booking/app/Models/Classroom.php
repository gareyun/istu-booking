<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class Classroom extends Model
{
    protected $fillable = [
        'room',
        'description',
        'equipment',
        'capacity',
        'google_calendar_id'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
