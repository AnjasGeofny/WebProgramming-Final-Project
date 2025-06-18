<?php

namespace App\Filament\Widgets;

use App\Models\Schedule;
use App\Models\Booking;
use App\Models\Field;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ScheduleOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Get today's stats
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Total schedules today
        $todaySchedules = Schedule::whereDate('date', $today)->count();

        // Available schedules today
        $availableToday = Schedule::whereDate('date', $today)
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function ($query) {
                $query->where('status', '!=', 'cancelled');
            })
            ->count();

        // Bookings today
        $bookingsToday = Booking::whereHas('schedule', function ($query) use ($today) {
            $query->whereDate('date', $today);
        })->where('status', '!=', 'cancelled')->count();

        // Total revenue today
        $revenueToday = Booking::whereHas('schedule', function ($query) use ($today) {
            $query->whereDate('date', $today);
        })->where('status', '!=', 'cancelled')->sum('total_price');

        // Active fields
        $activeFields = Field::count();

        // Upcoming bookings (next 7 days)
        $upcomingBookings = Booking::whereHas('schedule', function ($query) {
            $query->whereBetween('date', [Carbon::today(), Carbon::today()->addDays(7)]);
        })->where('status', '!=', 'cancelled')->count();

        return [
            Stat::make('Jadwal Hari Ini', $todaySchedules)
                ->description('Total slot waktu tersedia')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Slot Tersedia', $availableToday)
                ->description('Dapat dibooking hari ini')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Booking Hari Ini', $bookingsToday)
                ->description('Sudah terbooking')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make('Revenue Hari Ini', 'Rp ' . number_format($revenueToday, 0, ',', '.'))
                ->description('Total pendapatan')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Lapangan Aktif', $activeFields)
                ->description('Total lapangan tersedia')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            Stat::make('Booking Mendatang', $upcomingBookings)
                ->description('7 hari ke depan')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),
        ];
    }
}