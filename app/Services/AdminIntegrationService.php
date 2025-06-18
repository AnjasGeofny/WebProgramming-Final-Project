<?php

namespace App\Services;

use App\Models\Field;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminIntegrationService
{
    /**
     * Get real-time field availability data for admin panel
     */
    public function getFieldAvailabilityData($fieldId, $date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();

        $field = Field::findOrFail($fieldId);        // Get all schedules for this field on the specified date
        $schedules = Schedule::where('field_id', $fieldId)
            ->whereDate('date', $date)
            ->with(['bookings.user'])
            ->orderBy('start_time')
            ->get();

        $timeSlots = [];

        foreach ($schedules as $schedule) {
            $booking = $schedule->bookings->first();

            $timeSlots[] = [
                'schedule_id' => $schedule->id,
                'time_slot' => $schedule->start_time . ' - ' . $schedule->end_time,
                'is_available' => $schedule->is_available,
                'is_booked' => $booking ? true : false,
                'booking_info' => $booking ? [
                    'id' => $booking->id,
                    'customer_name' => $booking->user->name,
                    'customer_email' => $booking->user->email,
                    'status' => $booking->status,
                    'total_price' => $booking->total_price,
                    'payment_method' => $booking->payment_method ?? 'N/A',
                    'created_at' => $booking->created_at->format('d M Y H:i'),
                ] : null
            ];
        }

        return [
            'field' => [
                'id' => $field->id,
                'name' => $field->name,
                'court_number' => $field->court_number,
                'type' => $field->type,
                'location' => $field->location,
                'price_per_hour' => $field->price_per_hour,
            ],
            'date' => $date->format('Y-m-d'),
            'date_formatted' => $date->format('l, d F Y'),
            'time_slots' => $timeSlots
        ];
    }

    /**
     * Get comprehensive booking analytics
     */
    public function getBookingAnalytics($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->subDays(30);
        $endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();

        // Booking trends by date
        $bookingTrends = Booking::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();        // Revenue trends (berdasarkan booking yang completed)
        $revenueTrends = Booking::whereIn('status', ['completed', 'pending'])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Popular time slots
        $popularTimeSlots = Schedule::join('bookings', 'schedules.id', '=', 'bookings.schedule_id')
            ->selectRaw('schedules.start_time, schedules.end_time, COUNT(bookings.id) as booking_count')
            ->groupBy('schedules.start_time', 'schedules.end_time')
            ->orderBy('booking_count', 'desc')
            ->take(10)
            ->get();        // Field type performance
        $fieldTypePerformance = Field::join('bookings', 'fields.id', '=', 'bookings.field_id')
            ->whereIn('bookings.status', ['completed', 'pending'])
            ->selectRaw('
                fields.type,
                COUNT(bookings.id) as total_bookings,
                SUM(bookings.total_price) as total_revenue,
                AVG(bookings.total_price) as avg_price
            ')
            ->groupBy('fields.type')
            ->get();

        return [
            'period' => [
                'start_date' => $startDate->format('d M Y'),
                'end_date' => $endDate->format('d M Y'),
                'days' => $startDate->diffInDays($endDate) + 1
            ],
            'booking_trends' => $bookingTrends,
            'revenue_trends' => $revenueTrends,
            'popular_time_slots' => $popularTimeSlots,
            'field_type_performance' => $fieldTypePerformance,
        ];
    }

    /**
     * Get field utilization data
     */
    public function getFieldUtilization($period = 30)
    {
        $startDate = Carbon::now()->subDays($period);

        $utilization = Field::select([
            'fields.id',
            'fields.name',
            'fields.court_number',
            'fields.type',
            'fields.location',
            'fields.price_per_hour'
        ])
            ->withCount([
                'bookings as total_bookings' => function ($query) use ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
            ])->withSum([
                    'bookings as total_revenue' => function ($query) use ($startDate) {
                        $query->whereIn('status', ['completed', 'pending'])
                            ->where('created_at', '>=', $startDate);
                    }
                ], 'total_price')
            ->withCount([
                'schedules as total_slots' => function ($query) use ($startDate) {
                    $query->where('date', '>=', $startDate->format('Y-m-d'));
                }
            ])
            ->get()
            ->map(function ($field) {
                $utilizationRate = $field->total_slots > 0
                    ? round(($field->total_bookings / $field->total_slots) * 100, 2)
                    : 0;

                return [
                    'id' => $field->id,
                    'name' => $field->name,
                    'court_number' => $field->court_number,
                    'type' => $field->type,
                    'location' => $field->location,
                    'price_per_hour' => $field->price_per_hour,
                    'total_bookings' => $field->total_bookings,
                    'total_revenue' => $field->total_revenue ?? 0,
                    'total_slots' => $field->total_slots,
                    'utilization_rate' => $utilizationRate,
                    'avg_revenue_per_booking' => $field->total_bookings > 0
                        ? round(($field->total_revenue ?? 0) / $field->total_bookings, 0)
                        : 0
                ];
            })
            ->sortByDesc('utilization_rate');

        return $utilization;
    }

    /**
     * Sync user booking data with admin schedules
     */
    public function syncBookingData($bookingId)
    {
        $booking = Booking::with(['user', 'field', 'schedule'])->findOrFail($bookingId);        // Update schedule availability based on booking status
        if ($booking->status === 'completed') {
            $booking->schedule->update(['is_available' => false]);
        } elseif ($booking->status === 'cancelled') {
            $booking->schedule->update(['is_available' => true]);
        }

        return [
            'booking_id' => $booking->id,
            'user_name' => $booking->user->name,
            'field_name' => $booking->field->name,
            'court_number' => $booking->field->court_number,
            'schedule_time' => $booking->schedule->start_time . ' - ' . $booking->schedule->end_time,
            'schedule_date' => $booking->schedule->date->format('d M Y'),
            'status' => $booking->status,
            'payment_method' => $booking->payment_method ?? 'N/A',
            'total_price' => $booking->total_price,
        ];
    }

    /**
     * Get real-time dashboard stats
     */
    public function getDashboardStats()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            'today' => [
                'bookings' => Booking::whereDate('created_at', $today)->count(),
                'revenue' => Booking::whereIn('status', ['completed', 'pending'])
                    ->whereDate('created_at', $today)
                    ->sum('total_price'),
                'pending_bookings' => Booking::where('status', 'pending')
                    ->whereDate('created_at', $today)
                    ->count(),
            ],
            'this_month' => [
                'bookings' => Booking::where('created_at', '>=', $thisMonth)->count(),
                'revenue' => Booking::whereIn('status', ['completed', 'pending'])
                    ->where('created_at', '>=', $thisMonth)
                    ->sum('total_price'),
                'new_users' => User::where('created_at', '>=', $thisMonth)->count(),
            ],
            'totals' => [
                'fields' => Field::count(),
                'users' => User::count(),
                'bookings' => Booking::count(),
                'total_revenue' => Booking::whereIn('status', ['completed', 'pending'])
                    ->sum('total_price'),
            ]
        ];
    }

    /**
     * Get upcoming schedules for today and tomorrow
     */
    public function getUpcomingSchedules($limit = 20)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        return Schedule::with(['field', 'bookings.user'])
            ->whereIn('date', [$today, $tomorrow])
            ->where('is_available', true)
            ->whereDoesntHave('bookings')
            ->orderBy('date')
            ->orderBy('start_time')
            ->take($limit)
            ->get()
            ->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'field_name' => $schedule->field->name,
                    'court_number' => $schedule->field->court_number,
                    'type' => $schedule->field->type,
                    'date' => $schedule->date->format('d M Y'),
                    'time' => $schedule->start_time . ' - ' . $schedule->end_time,
                    'price' => $schedule->field->price_per_hour,
                    'is_today' => $schedule->date->isToday(),
                ];
            });
    }

    /**
     * Get field status for admin dashboard
     */
    public function getFieldStatus()
    {
        return Field::withCount([
            'schedules as today_schedules_count' => function ($query) {
                $query->whereDate('date', Carbon::today());
            },
            'schedules as booked_schedules_count' => function ($query) {
                $query->whereDate('date', Carbon::today())
                    ->whereHas('bookings', function ($q) {
                        $q->whereIn('status', ['completed', 'pending']);
                    });
            }
        ])->get()->map(function ($field) {
            $todaySchedules = $field->today_schedules_count;
            $bookedSchedules = $field->booked_schedules_count;
            $utilization = $todaySchedules > 0 ? ($bookedSchedules / $todaySchedules) * 100 : 0;

            return [
                'id' => $field->id,
                'name' => $field->name,
                'type' => $field->type,
                'today_schedules' => $todaySchedules,
                'booked_schedules' => $bookedSchedules,
                'utilization' => round($utilization, 1),
                'status' => $utilization > 80 ? 'busy' : ($utilization > 50 ? 'moderate' : 'available')
            ];
        });
    }

    /**
     * Get live bookings data
     */
    public function getLiveBookings()
    {
        return Booking::with(['user', 'schedule.field'])
            ->whereDate('created_at', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'customer_name' => $booking->user->name,
                    'field_name' => $booking->schedule->field->name,
                    'status' => $booking->status,
                    'total_price' => $booking->total_price,
                    'payment_method' => $booking->payment_method ?? 'N/A',
                    'created_at' => $booking->created_at->format('H:i'),
                    'time_ago' => $booking->created_at->diffForHumans()
                ];
            });
    }
}
