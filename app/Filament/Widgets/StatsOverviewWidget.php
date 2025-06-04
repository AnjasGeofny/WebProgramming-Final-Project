<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1; // Urutan widget di dashboard (paling atas setelah welcome)
    protected static ?string $pollingInterval = '15s'; // Opsional

    protected function getStats(): array
    {
        $todayBookingsCount = Booking::whereDate('created_at', Carbon::today())->count();

        // Asumsikan status 'paid' atau 'confirmed' yang dihitung sebagai pendapatan
        // Sesuaikan array status ini dengan logika bisnis Anda
        $revenueGeneratingStatus = ['paid', 'confirmed'];
        $todayRevenue = Booking::whereDate('created_at', Carbon::today())
            ->whereIn('status', $revenueGeneratingStatus)
            ->sum('total_price');

        $pendingBookingsCount = Booking::where('status', 'pending')->count(); // Sesuaikan 'pending' jika value status Anda berbeda

        $totalUsersCount = User::count();

        return [
            Stat::make('Bookings Hari Ini', $todayBookingsCount)
                ->description('Jumlah booking baru hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Dari booking yang terkonfirmasi/terbayar')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make('Booking Pending', $pendingBookingsCount)
                ->description('Booking menunggu konfirmasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Total Pengguna', $totalUsersCount)
                ->description('Jumlah pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
