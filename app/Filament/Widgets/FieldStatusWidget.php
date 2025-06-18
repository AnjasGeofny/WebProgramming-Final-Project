<?php

namespace App\Filament\Widgets;

use App\Models\Field;
use App\Models\Schedule;
use App\Models\Booking;
use App\Services\AdminIntegrationService;
use Filament\Widgets\Widget;
use Carbon\Carbon;

class FieldStatusWidget extends Widget
{
    protected static string $view = 'filament.widgets.field-status-widget';
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '30s';

    public function getFieldsData()
    {
        $integrationService = new AdminIntegrationService();

        // Get all fields with their current status
        $fields = Field::with([
            'schedules' => function ($query) {
                $query->whereDate('date', Carbon::today())
                    ->orderBy('start_time');
            }
        ])
            ->get()
            ->map(function ($field) {
                $todaySchedules = $field->schedules;
                $totalSlots = $todaySchedules->count();
                $bookedSlots = $todaySchedules->filter(function ($schedule) {
                    return $schedule->bookings()->exists();
                })->count();

                $availableSlots = $totalSlots - $bookedSlots;
                $utilizationRate = $totalSlots > 0 ? round(($bookedSlots / $totalSlots) * 100, 1) : 0;

                // Get current status (what's happening now)
                $currentTime = Carbon::now()->format('H:i:s');
                $currentSchedule = $todaySchedules->first(function ($schedule) use ($currentTime) {
                    return $currentTime >= $schedule->start_time && $currentTime <= $schedule->end_time;
                });

                $currentStatus = 'Available';
                $currentBooking = null;
                if ($currentSchedule) {
                    $booking = $currentSchedule->bookings()->first();
                    if ($booking) {
                        $currentStatus = 'Occupied';
                        $currentBooking = [
                            'customer' => $booking->customer_name ?? ($booking->user?->name ?? 'Unknown'),
                            'until' => $currentSchedule->end_time,
                            'status' => $booking->status
                        ];
                    } else {
                        $currentStatus = $currentSchedule->is_available ? 'Available' : 'Maintenance';
                    }
                }

                // Get next booking
                $nextSchedule = $todaySchedules->first(function ($schedule) use ($currentTime) {
                    return $currentTime < $schedule->start_time && $schedule->bookings()->exists();
                });
                $nextBooking = null;
                if ($nextSchedule) {
                    $booking = $nextSchedule->bookings()->first();
                    $nextBooking = [
                        'customer' => $booking->customer_name ?? ($booking->user?->name ?? 'Unknown'),
                        'start_time' => $nextSchedule->start_time,
                        'end_time' => $nextSchedule->end_time
                    ];
                }

                return [
                    'id' => $field->id,
                    'name' => $field->name,
                    'court_number' => $field->court_number,
                    'type' => $field->type,
                    'location' => $field->location,
                    'total_slots' => $totalSlots,
                    'booked_slots' => $bookedSlots,
                    'available_slots' => $availableSlots,
                    'utilization_rate' => $utilizationRate,
                    'current_status' => $currentStatus,
                    'current_booking' => $currentBooking,
                    'next_booking' => $nextBooking,
                    'price_per_hour' => $field->price_per_hour
                ];
            });

        return $fields;
    }

    public function getViewData(): array
    {
        return [
            'fields' => $this->getFieldsData(),
            'todayStats' => $this->getTodayStats()
        ];
    }

    public function getTodayStats()
    {
        $today = Carbon::today();

        return [
            'total_bookings' => Booking::whereDate('created_at', $today)->count(),
            'completed_bookings' => Booking::whereDate('created_at', $today)
                ->where('status', 'completed')->count(),
            'pending_bookings' => Booking::whereDate('created_at', $today)
                ->where('status', 'pending')->count(),
            'total_revenue' => Booking::whereDate('created_at', $today)
                ->whereIn('status', ['completed', 'pending'])
                ->sum('total_price'),
            'fields_in_use' => Schedule::whereDate('date', $today)
                ->whereHas('bookings')
                ->distinct('field_id')
                ->count('field_id'),
            'total_fields' => Field::count()
        ];
    }
}
