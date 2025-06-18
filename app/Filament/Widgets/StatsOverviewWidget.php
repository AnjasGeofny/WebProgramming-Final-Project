<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Field;
use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1; // Urutan widget di dashboard (paling atas setelah welcome)
    protected static ?string $pollingInterval = '15s'; // Opsional: refresh data secara berkala

    protected function getStats(): array
    {
        // 1. Jumlah booking yang dibuat hari ini
        $todayBookingsCount = Booking::whereDate('created_at', Carbon::today())->count();

        // 2. Pendapatan Hari Ini (berdasarkan total booking hari ini dengan status completed)
        $todayRevenue = Booking::query()
            ->where('status', 'completed')
            ->whereDate('created_at', Carbon::today())
            ->sum('total_price');

        // 3. Jumlah Booking yang statusnya 'pending'
        $pendingBookingsCount = Booking::where('status', 'pending')->count();

        // 4. Lapangan Aktif (total lapangan tersedia)
        $activeFieldsCount = Field::count();

        // 5. Slot Tersedia (jadwal yang masih bisa dibooking hari ini)
        $availableSlotsCount = Schedule::where('date', Carbon::today())
            ->where('is_available', true)
            ->count();

        // 6. Booking Mendatang (booking dengan jadwal 7 hari ke depan)
        $upcomingBookingsCount = Booking::whereHas('schedule', function ($query) {
            $query->whereBetween('date', [Carbon::today(), Carbon::today()->addDays(7)]);
        })
            ->whereIn('status', ['pending', 'completed'])
            ->count();

        return [
            Stat::make('Booking Hari Ini', $todayBookingsCount)
                ->description('Jumlah booking baru hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Revenue Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Dari booking completed hari ini')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make('Booking Pending', $pendingBookingsCount)
                ->description('Booking menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Lapangan Aktif', $activeFieldsCount)
                ->description('Total lapangan tersedia')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),
            Stat::make('Slot Tersedia', $availableSlotsCount)
                ->description('Dapat dibooking hari ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
            Stat::make('Booking Mendatang', $upcomingBookingsCount)
                ->description('7 hari ke depan')
                ->descriptionIcon('heroicon-m-forward')
                ->color('primary'),
        ];
    }
}