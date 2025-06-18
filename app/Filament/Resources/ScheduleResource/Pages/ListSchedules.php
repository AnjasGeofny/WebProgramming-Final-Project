<?php

namespace App\Filament\Resources\ScheduleResource\Pages;

use App\Filament\Resources\ScheduleResource;
use App\Models\Field;
use App\Models\Schedule;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class ListSchedules extends ListRecords
{
    protected static string $resource = ScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Buat Jadwal'),

            Actions\Action::make('quick_create')
                ->label('Buat Jadwal Cepat')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->form([
                    Section::make('Pilih Lapangan & Tanggal')
                        ->schema([
                            Select::make('field_ids')
                                ->label('Lapangan')
                                ->multiple()
                                ->options(
                                    Field::all()->mapWithKeys(function ($field) {
                                        return [$field->id => "{$field->name} - Court {$field->court_number} ({$field->type})"];
                                    })
                                )
                                ->required()
                                ->searchable()
                                ->preload(),

                            DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->required()
                                ->minDate(today())
                                ->default(today()),

                            DatePicker::make('end_date')
                                ->label('Tanggal Selesai')
                                ->required()
                                ->minDate(today())
                                ->default(today()->addDays(7))
                                ->afterOrEqual('start_date'),

                            Toggle::make('is_available')
                                ->label('Tersedia untuk Booking')
                                ->default(true),
                        ]),

                    Section::make('Waktu Operasional Default')
                        ->description('Pilih jam operasional yang akan diterapkan untuk semua hari')
                        ->schema([
                            CheckboxList::make('time_slots')
                                ->label('Jam Operasional')
                                ->options([
                                    '07:00-08:00' => '07:00 - 08:00',
                                    '08:00-09:00' => '08:00 - 09:00',
                                    '09:00-10:00' => '09:00 - 10:00',
                                    '10:00-11:00' => '10:00 - 11:00',
                                    '11:00-12:00' => '11:00 - 12:00',
                                    '12:00-13:00' => '12:00 - 13:00',
                                    '13:00-14:00' => '13:00 - 14:00',
                                    '14:00-15:00' => '14:00 - 15:00',
                                    '15:00-16:00' => '15:00 - 16:00',
                                    '16:00-17:00' => '16:00 - 17:00',
                                    '17:00-18:00' => '17:00 - 18:00',
                                    '18:00-19:00' => '18:00 - 19:00',
                                    '19:00-20:00' => '19:00 - 20:00',
                                    '20:00-21:00' => '20:00 - 21:00',
                                    '21:00-22:00' => '21:00 - 22:00',
                                ])
                                ->columns(4)
                                ->default(['07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00', '17:00-18:00', '19:00-20:00', '20:00-21:00', '21:00-22:00'])
                                ->required(),
                        ]),
                ])
                ->action(function (array $data) {
                    $createdCount = 0;
                    $skippedCount = 0;

                    $startDate = Carbon::parse($data['start_date']);
                    $endDate = Carbon::parse($data['end_date']);

                    foreach ($data['field_ids'] as $fieldId) {
                        $currentDate = $startDate->copy();

                        while ($currentDate->lte($endDate)) {
                            foreach ($data['time_slots'] as $timeSlot) {
                                [$startTime, $endTime] = explode('-', $timeSlot);

                                // Check if schedule already exists
                                $existingSchedule = Schedule::where('field_id', $fieldId)
                                    ->where('date', $currentDate->format('Y-m-d'))
                                    ->where('start_time', $startTime . ':00')
                                    ->where('end_time', $endTime . ':00')
                                    ->first();

                                if (!$existingSchedule) {
                                    Schedule::create([
                                        'field_id' => $fieldId,
                                        'date' => $currentDate->format('Y-m-d'),
                                        'start_time' => $startTime . ':00',
                                        'end_time' => $endTime . ':00',
                                        'is_available' => $data['is_available'],
                                    ]);
                                    $createdCount++;
                                } else {
                                    $skippedCount++;
                                }
                            }

                            $currentDate->addDay();
                        }
                    }

                    Notification::make()
                        ->title('Jadwal Bulk Berhasil Dibuat')
                        ->body("$createdCount jadwal baru dibuat. $skippedCount jadwal dilewati (sudah ada).")
                        ->success()
                        ->send();
                }),
        ];
    }
}
