<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Field; // Pastikan model Field ada
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB; // Untuk query yang lebih kompleks jika diperlukan

class FieldsChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Booking per Tipe Lapangan';
    protected static ?int $sort = 4; // Urutan widget di dashboard
    protected static ?string $pollingInterval = '30s'; // Opsional

    protected function getData(): array
    {
        // Mengambil data booking dan mengelompokkannya berdasarkan tipe field
        // Ini mengasumsikan relasi 'field' ada di model Booking
        // dan model Field memiliki kolom 'type'
        $bookingsByFieldType = Booking::query()
            ->join('fields', 'bookings.field_id', '=', 'fields.id') // Join dengan tabel fields
            ->select('fields.type as field_type', DB::raw('count(bookings.id) as booking_count'))
            ->groupBy('fields.type')
            ->orderBy('booking_count', 'desc')
            ->get();

        $labels = $bookingsByFieldType->pluck('field_type')->toArray();
        $data = $bookingsByFieldType->pluck('booking_count')->toArray();

        // Warna-warna yang bisa digunakan untuk pie chart
        $backgroundColors = [
            'rgba(255, 99, 132, 0.7)',  // Merah
            'rgba(54, 162, 235, 0.7)', // Biru
            'rgba(255, 206, 86, 0.7)', // Kuning
            'rgba(75, 192, 192, 0.7)', // Teal
            'rgba(153, 102, 255, 0.7)', // Ungu
            'rgba(255, 159, 64, 0.7)', // Oranye
            'rgba(199, 199, 199, 0.7)', // Abu-abu
            'rgba(83, 102, 252, 0.7)', // Indigo
            'rgba(248, 102, 107, 0.7)', // Pink
            'rgba(102, 203, 107, 0.7)', // Hijau muda
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Bookings',
                    'data' => $data,
                    'backgroundColor' => array_slice($backgroundColors, 0, count($labels)), // Sesuaikan jumlah warna dengan jumlah label
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie'; // Jenis chart: pie atau doughnut
    }
}
