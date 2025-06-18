<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Field;
use App\Models\Booking;
use Carbon\Carbon;

class FieldListWidget extends Widget
{
    protected static string $view = 'filament.widgets.field-list-widget';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        $fields = Field::withCount([
            'schedules',
            'schedules as today_schedules_count' => function ($query) {
                $query->whereDate('date', Carbon::today());
            },
            'schedules as booked_schedules_count' => function ($query) {
                $query->whereDate('date', Carbon::today())
                    ->whereHas('bookings', function ($q) {
                        $q->whereIn('status', ['completed', 'pending']);
                    });
            }
        ])->get();

        $fieldsWithStats = $fields->map(function ($field) {
            $todaySchedules = $field->today_schedules_count;
            $bookedSchedules = $field->booked_schedules_count;
            $utilization = $todaySchedules > 0 ? ($bookedSchedules / $todaySchedules) * 100 : 0;

            // Get recent bookings for this field
            $recentBookings = Booking::whereHas('schedule', function ($query) use ($field) {
                $query->where('field_id', $field->id);
            })
                ->with(['user', 'schedule'])
                ->latest()
                ->take(3)
                ->get();

            return [
                'id' => $field->id,
                'name' => $field->name,
                'type' => $field->type,
                'price_per_hour' => $field->price_per_hour,
                'image' => $field->image,
                'today_schedules' => $todaySchedules,
                'booked_schedules' => $bookedSchedules,
                'utilization' => round($utilization, 1),
                'status' => $utilization > 80 ? 'busy' : ($utilization > 50 ? 'moderate' : 'available'),
                'recent_bookings' => $recentBookings
            ];
        });

        return [
            'fields' => $fieldsWithStats
        ];
    }
}
