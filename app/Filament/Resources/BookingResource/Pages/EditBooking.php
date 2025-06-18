<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooking extends EditRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Convert schedule_ids JSON back to array for form
        if (isset($data['schedule_ids']) && is_string($data['schedule_ids'])) {
            $data['schedule_ids'] = json_decode($data['schedule_ids'], true) ?? [];
        } elseif (isset($data['schedule_id']) && $data['schedule_id']) {
            // Backward compatibility: convert single schedule_id to array
            $data['schedule_ids'] = [$data['schedule_id']];
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Store original schedule IDs for cleanup
        $originalScheduleIds = $this->record->schedule_ids ?
            json_decode($this->record->schedule_ids, true) :
            ($this->record->schedule_id ? [$this->record->schedule_id] : []);

        // Convert schedule_ids array to JSON and set primary schedule_id
        if (isset($data['schedule_ids']) && !empty($data['schedule_ids'])) {
            $data['schedule_id'] = $data['schedule_ids'][0];
            $data['schedule_ids'] = json_encode($data['schedule_ids']);
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $booking = $this->record;

        // Get original schedule IDs for cleanup
        $originalScheduleIds = json_decode($this->record->getOriginal('schedule_ids'), true) ??
            ($this->record->getOriginal('schedule_id') ? [$this->record->getOriginal('schedule_id')] : []);

        // Get current schedule IDs
        $currentScheduleIds = json_decode($booking->schedule_ids, true) ??
            ($booking->schedule_id ? [$booking->schedule_id] : []);

        // Free up old schedules that are no longer booked
        $removedScheduleIds = array_diff($originalScheduleIds, $currentScheduleIds);
        foreach ($removedScheduleIds as $scheduleId) {
            $schedule = \App\Models\Schedule::find($scheduleId);
            if ($schedule) {
                $schedule->update(['is_available' => true]);
            }
        }

        // Mark current schedules as unavailable
        foreach ($currentScheduleIds as $scheduleId) {
            $schedule = \App\Models\Schedule::find($scheduleId);
            if ($schedule && in_array($booking->status, ['pending', 'completed'])) {
                $schedule->update(['is_available' => false]);
            }
        }
    }
}
