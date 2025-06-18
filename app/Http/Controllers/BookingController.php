<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Field;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'field_id' => 'required|exists:fields,id',
            'schedule_id' => 'required|exists:schedules,id',
            'user_name' => 'required|string|max:255',
            'user_phone' => 'required|string|max:20',
        ]);

        $field = Field::findOrFail($request->field_id);
        $schedule = Schedule::findOrFail($request->schedule_id);
        
        // Cek apakah schedule masih tersedia
        if (!$schedule->is_available) {
            return back()->with('error', 'Jadwal sudah tidak tersedia');
        }

        // Hitung durasi dan total harga
        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $duration = $startTime->diffInHours($endTime);
        $totalPrice = $field->price_per_hour * $duration;

        // Buat booking
        $booking = Booking::create([
            'field_id' => $request->field_id,
            'schedule_id' => $request->schedule_id,
            'user_name' => $request->user_name,
            'user_phone' => $request->user_phone,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        // Update schedule menjadi tidak tersedia
        $schedule->update(['is_available' => false]);

        return redirect()->route('booking.success', $booking->id)
            ->with('success', 'Booking berhasil dibuat!');
    }

    public function success($id)
    {
        $booking = Booking::with(['field', 'schedule'])->findOrFail($id);
        
        return view('user.booking-success', compact('booking'));
    }
}