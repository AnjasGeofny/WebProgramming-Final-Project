<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\Schedule;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateSchedule extends CreateRecord
{
    protected static string $resource = ScheduleResource::class;

    private $timeSlots = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove time_slots from data as it's not a database field
        $timeSlots = $data['time_slots'] ?? [];
        unset($data['time_slots']);

        // Store time slots in a property to use in afterCreate
        $this->timeSlots = $timeSlots;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Get the created record
        $baseSchedule = $this->record;
        $createdCount = 0;

        // Create individual schedules for each selected time slot
        if (!empty($this->timeSlots)) {
            foreach ($this->timeSlots as $timeSlot) {
                [$startTime, $endTime] = explode('-', $timeSlot);

                // Check if schedule already exists for this time slot
                $existingSchedule = Schedule::where('field_id', $baseSchedule->field_id)
                    ->where('date', $baseSchedule->date)
                    ->where('start_time', $startTime . ':00')
                    ->where('end_time', $endTime . ':00')
                    ->first();

                if (!$existingSchedule) {
                    Schedule::create([
                        'field_id' => $baseSchedule->field_id,
                        'date' => $baseSchedule->date,
                        'start_time' => $startTime . ':00',
                        'end_time' => $endTime . ':00',
                        'is_available' => $baseSchedule->is_available,
                    ]);
                    $createdCount++;
                }
            }

            // Delete the base schedule as we've created individual ones
            $baseSchedule->delete();

            // Show success notification
            Notification::make()
                ->title('Jadwal Berhasil Dibuat')
                ->body("$createdCount jadwal baru telah dibuat untuk tanggal {$baseSchedule->date->format('d M Y')}")
                ->success()
                ->send();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
