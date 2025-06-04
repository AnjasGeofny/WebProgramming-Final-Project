<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'start_time',
        'end_time',
        // 'field_id', // Add if you uncommented the foreign key in the migration
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
    ];

    /**
     * Get the bookings for the schedule.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Optional: if a schedule belongs to a specific field directly
    // public function field()
    // {
    //     return $this->belongsTo(Field::class);
    // }
}
