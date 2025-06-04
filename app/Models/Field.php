<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'type',
        'price_per_hour',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price_per_hour' => 'decimal:2',
    ];

    /**
     * Get the bookings for the field.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
