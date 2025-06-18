<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Field;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class LiveBookingWidget extends Widget
{
    protected static string $view = 'filament.widgets.live-booking-widget';

    public function getRecentBookings($limit = 10)
    {
        return Booking::with(['user', 'field', 'schedule'])
            ->latest()
            ->take($limit)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'customer_name' => $booking->customer_name ?? ($booking->user?->name ?? 'Unknown'),
                    'customer_email' => $booking->user?->email ?? 'N/A',
                    'field_name' => $booking->field?->name ?? 'Unknown Field',
                    'court_number' => $booking->field?->court_number ?? 'N/A',
                    'field_type' => $booking->field?->type ?? 'Unknown',
                    'schedule_date' => $booking->schedule?->date?->format('d M Y') ?? 'No Date',
                    'schedule_time' => ($booking->schedule?->start_time && $booking->schedule?->end_time)
                        ? $booking->schedule->start_time . ' - ' . $booking->schedule->end_time
                        : 'No Time',
                    'status' => $booking->status,
                    'payment_method' => $booking->payment_method ?? 'N/A',
                    'total_price' => $booking->total_price,
                    'created_at' => $booking->created_at->format('d M Y H:i'),
                    'is_today' => $booking->schedule?->date?->isToday() ?? false,
                    'is_upcoming' => $booking->schedule?->date?->isFuture() ?? false,
                    'time_until_start' => ($booking->schedule?->date?->isToday() && $booking->schedule) ?
                        $this->getTimeUntilStart($booking->schedule) : null
                ];
            });
    }

    public function getTodayBookings()
    {
        $today = Carbon::today();

        return Booking::with(['user', 'field', 'schedule'])
            ->whereHas('schedule', function ($query) use ($today) {
                $query->whereDate('date', $today);
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($booking) {
                $currentTime = Carbon::now()->format('H:i:s');

                $status = 'upcoming';
                if ($booking->schedule && $currentTime >= $booking->schedule->start_time && $currentTime <= $booking->schedule->end_time) {
                    $status = 'ongoing';
                } elseif ($booking->schedule && $currentTime > $booking->schedule->end_time) {
                    $status = 'completed';
                }

                return [
                    'id' => $booking->id,
                    'customer_name' => $booking->customer_name ?? ($booking->user?->name ?? 'Unknown'),
                    'field_name' => $booking->field?->name ?? 'Unknown Field',
                    'court_number' => $booking->field?->court_number ?? 'N/A',
                    'field_type' => $booking->field?->type ?? 'Unknown',
                    'schedule_time' => ($booking->schedule?->start_time && $booking->schedule?->end_time)
                        ? $booking->schedule->start_time . ' - ' . $booking->schedule->end_time
                        : 'No Time',
                    'booking_status' => $booking->status,
                    'session_status' => $status,
                    'total_price' => $booking->total_price,
                    'created_at' => $booking->created_at->format('H:i'),
                ];
            });
    }

    public function getPendingActions()
    {
        // Booking yang perlu tindakan admin
        $pendingBookings = Booking::with(['user', 'field', 'schedule'])
            ->where('status', 'pending')
            ->whereHas('schedule', function ($query) {
                $query->where('date', '>=', Carbon::today());
            })
            ->latest()
            ->take(5)
            ->get();

        // Booking yang memiliki payment_proof dan perlu verifikasi
        $pendingPayments = Booking::with(['user', 'field', 'schedule'])
            ->whereNotNull('payment_proof')
            ->where('status', 'pending')
            ->latest()
            ->take(5)
            ->get();

        return [
            'pending_bookings' => $pendingBookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'customer_name' => $booking->customer_name ?? ($booking->user?->name ?? 'Unknown'),
                    'field_name' => $booking->field?->name ?? 'Unknown Field',
                    'court_number' => $booking->field?->court_number ?? 'N/A',
                    'schedule_date' => $booking->schedule?->date?->format('d M') ?? 'No Date',
                    'schedule_time' => $booking->schedule?->start_time ?? 'No Time',
                    'total_price' => $booking->total_price,
                    'created_at' => $booking->created_at->diffForHumans(),
                ];
            }),
            'pending_payments' => $pendingPayments->map(function ($booking) {
                return [
                    'booking_id' => $booking->id,
                    'customer_name' => $booking->customer_name ?? ($booking->user?->name ?? 'Unknown'),
                    'field_name' => $booking->field?->name ?? 'Unknown Field',
                    'total_price' => $booking->total_price,
                    'payment_method' => $booking->payment_method ?? 'N/A',
                    'proof_url' => $booking->payment_proof ? asset('storage/' . $booking->payment_proof) : null,
                    'created_at' => $booking->created_at?->diffForHumans() ?? 'N/A',
                ];
            })
        ];
    }

    public function getUpcomingSchedules($limit = 8)
    {
        $now = Carbon::now();
        $endOfDay = Carbon::today()->endOfDay();

        return Schedule::with(['field', 'bookings.user'])
            ->where('date', Carbon::today())
            ->where('start_time', '>', $now->format('H:i:s'))
            ->where('start_time', '<=', $endOfDay->format('H:i:s'))
            ->orderBy('start_time')
            ->take($limit)
            ->get()
            ->map(function ($schedule) {
                $booking = $schedule->bookings->first();
                $isBooked = $booking ? true : false;

                return [
                    'id' => $schedule->id,
                    'field_name' => $schedule->field->name,
                    'court_number' => $schedule->field->court_number,
                    'field_type' => $schedule->field->type,
                    'time' => $schedule->start_time . ' - ' . $schedule->end_time,
                    'price' => $schedule->field->price_per_hour,
                    'is_booked' => $isBooked,
                    'is_available' => $schedule->is_available,
                    'booking_info' => $booking ? [
                        'customer_name' => $booking->customer_name ?? ($booking->user?->name ?? 'Unknown'),
                        'status' => $booking->status
                    ] : null,
                    'time_until_start' => $this->getTimeUntilStart($schedule),
                ];
            });
    }

    private function getTimeUntilStart($schedule)
    {
        if (!$schedule || !$schedule->date || !$schedule->start_time) {
            return null;
        }

        $now = Carbon::now();
        $scheduleStart = Carbon::createFromFormat('Y-m-d H:i:s', $schedule->date->format('Y-m-d') . ' ' . $schedule->start_time);

        if ($scheduleStart <= $now) {
            return null;
        }

        $diffInMinutes = $now->diffInMinutes($scheduleStart);

        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' menit lagi';
        } elseif ($diffInMinutes < 1440) { // less than 24 hours
            $hours = floor($diffInMinutes / 60);
            $minutes = $diffInMinutes % 60;
            return $hours . 'j ' . $minutes . 'm';
        } else {
            return $scheduleStart->format('d M H:i');
        }
    }

    public function getViewData(): array
    {
        return [
            'recent_bookings' => $this->getRecentBookings(8),
            'today_bookings' => $this->getTodayBookings(),
            'pending_actions' => $this->getPendingActions(),
            'upcoming_schedules' => $this->getUpcomingSchedules(6),
        ];
    }
}
