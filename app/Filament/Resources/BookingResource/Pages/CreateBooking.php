<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Jika menggunakan multiple schedules, simpan sebagai JSON dalam satu booking
        if (isset($data['schedule_ids']) && !empty($data['schedule_ids'])) {
            // Set schedule_id ke yang pertama (untuk backward compatibility)
            $data['schedule_id'] = $data['schedule_ids'][0];

            // Store schedule_ids as JSON for reference
            $data['schedule_ids'] = json_encode($data['schedule_ids']);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $booking = $this->record;
        $scheduleIds = json_decode($booking->schedule_ids, true) ?? [$booking->schedule_id];

        // Update schedule availability untuk semua schedule yang dipilih
        foreach ($scheduleIds as $scheduleId) {
            $schedule = \App\Models\Schedule::find($scheduleId);
            if ($schedule && in_array($booking->status, ['pending', 'completed'])) {
                $schedule->update(['is_available' => false]);
            }
        }
    }
}
