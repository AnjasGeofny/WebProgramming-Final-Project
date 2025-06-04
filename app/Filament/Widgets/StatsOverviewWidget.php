<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB; // Pastikan DB facade diimpor jika menggunakan fungsi DB raw

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1; // Urutan widget di dashboard (paling atas setelah welcome)
    protected static ?string $pollingInterval = '15s'; // Opsional: refresh data secara berkala

    protected function getStats(): array
    {
        // 1. Jumlah booking yang dibuat hari ini
        $todayBookingsCount = Booking::whereDate('created_at', Carbon::today())->count();

        // 2. Pendapatan Hari Ini (berdasarkan pembayaran yang statusnya 'paid' HARI INI)
        $todayRevenue = Booking::query()
            // Join dengan tabel payments
            ->join('payments', 'bookings.id', '=', 'payments.booking_id')
            // Filter hanya yang status pembayarannya 'paid'
            ->where('payments.payment_status', 'paid')
            // Filter pembayaran yang statusnya menjadi 'paid' pada hari ini
            // Asumsi kolom 'updated_at' di tabel 'payments' mencerminkan kapan status terakhir diubah.
            // Jika Anda ingin berdasarkan tanggal booking dibuat, gunakan: ->whereDate('bookings.created_at', Carbon::today())
            ->whereDate('payments.updated_at', Carbon::today())
            // Jumlahkan 'total_price' dari tabel 'bookings'
            ->sum('bookings.total_price');

        // 3. Jumlah Booking yang statusnya 'pending' (di tabel bookings)
        // Ini mengasumsikan 'bookings.status' = 'pending' berarti booking belum dikonfirmasi atau dibayar.
        // Jika 'pending' secara spesifik berarti 'menunggu pembayaran', Anda mungkin perlu logika yang berbeda
        // yang juga memeriksa tabel 'payments'. Untuk saat ini, kita biarkan seperti ini.
        $pendingBookingsCount = Booking::where('status', 'pending')->count();

        // 4. Total Pengguna Terdaftar
        $totalUsersCount = User::count();

        return [
            Stat::make('Bookings Hari Ini', $todayBookingsCount)
                ->description('Jumlah booking baru hari ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Dari pembayaran yang lunas hari ini') // Deskripsi diperbarui
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary'),
            Stat::make('Booking Pending', $pendingBookingsCount)
                ->description('Booking menunggu konfirmasi/pembayaran') // Deskripsi bisa disesuaikan
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Total Pengguna', $totalUsersCount)
                ->description('Jumlah pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}