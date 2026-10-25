<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = [
        'classroom_category_id',
        'room',
        'description',
        'equipment',
        'capacity',
        'photo',
        'google_calendar_id',
        'is_hidden',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function scopeBookable($query)
    {
        return $query->where('is_hidden', false);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function category()
    {
        return $this->belongsTo(ClassroomCategory::class, 'classroom_category_id');
    }
}