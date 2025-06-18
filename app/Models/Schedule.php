<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'field_id',
        'date',
        'start_time',
        'end_time',
        'is_available',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date' => 'date',
        'is_available' => 'boolean',
    ];

    /**
     * Get the field for the schedule.
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the bookings for the schedule.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get all bookings for this schedule (including those with multiple schedules).
     */
    public function getAllBookings()
    {
        return Booking::where(function ($query) {
            $query->where('schedule_id', $this->id)
                ->orWhere('schedule_ids', 'LIKE', '%"' . $this->id . '"%');
        })->where('status', '!=', 'cancelled');
    }

    /**
     * Check if this schedule slot is available for booking.
     */
    public function isAvailable()
    {
        return $this->is_available && $this->getAllBookings()->count() === 0;
    }
}
