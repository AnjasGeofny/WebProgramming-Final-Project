<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BookingsChart extends ChartWidget
{
    protected static ?string $heading = 'Bookings per Hari (7 Hari Terakhir)';
    protected static ?string $pollingInterval = '10s'; // Opsional: refresh data secara berkala
    protected static ?int $sort = 2; // Urutan widget di dashboard

    protected function getData(): array
    {
        $data = Trend::model(Booking::class)
            ->between(
                start: now()->subDays(6), // Mulai dari 6 hari yang lalu (total 7 hari termasuk hari ini)
                end: now(),             // Sampai hari ini
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Bookings',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                    'borderColor' => 'rgb(54, 162, 235)', // Biru
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'tension' => 0.1,
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Jenis chart: line, bar, pie, doughnut, radar, polarArea
    }
}
