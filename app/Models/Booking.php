<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'customer_name',
        'user_name',
        'user_phone',
        'field_id',
        'schedule_id',
        'schedule_ids',
        'status',
        'total_price',
        'payment_method',
        'payment_proof',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'total_price' => 'decimal:2',
        // Remove schedule_ids casting - we'll handle JSON manually for better control
    ];

    /**
     * Get the user that owns the booking.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the field that is booked.
     */
    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    /**
     * Get the schedule for the booking.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // Payment relationships removed - payment fields integrated directly into booking table

    /**
     * Get multiple schedules for this booking.
     */
    public function schedules()
    {
        // Priority: gunakan schedule_ids jika tersedia
        if ($this->schedule_ids) {
            $scheduleIds = is_string($this->schedule_ids) ?
                json_decode($this->schedule_ids, true) :
                $this->schedule_ids;

            if (is_array($scheduleIds) && !empty($scheduleIds)) {
                return Schedule::whereIn('id', $scheduleIds)->get();
            }
        }

        // Fallback untuk backward compatibility
        if ($this->schedule_id) {
            return collect([$this->schedule]);
        }

        return collect();
    }

    /**
     * Get formatted schedule times for display.
     */
    public function getScheduleTimesAttribute()
    {
        $schedules = $this->schedules();
        if ($schedules->isEmpty()) {
            return '-';
        }

        return $schedules->map(function ($schedule) {
            return $schedule->date->format('d M Y') . ' (' . $schedule->start_time . ' - ' . $schedule->end_time . ')';
        })->join(', ');
    }

    /**
     * Calculate total price based on field price and number of schedules.
     */
    public function calculateTotalPrice()
    {
        if (!$this->field) {
            return 0;
        }

        $scheduleIds = null;
        if ($this->schedule_ids) {
            $scheduleIds = is_string($this->schedule_ids) ?
                json_decode($this->schedule_ids, true) :
                $this->schedule_ids;
        }

        if (!$scheduleIds && $this->schedule_id) {
            $scheduleIds = [$this->schedule_id];
        }

        if (!$scheduleIds) {
            return 0;
        }

        $scheduleCount = count($scheduleIds);
        return $this->field->price_per_hour * $scheduleCount;
    }
}
