<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'payment_status',
        'proof',
    ];

    /**
     * Get the booking that this payment belongs to.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
