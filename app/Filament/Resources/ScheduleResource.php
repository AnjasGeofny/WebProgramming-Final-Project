<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScheduleResource\Pages;
use App\Models\Schedule;
use App\Models\Field;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\CheckboxList;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Carbon\Carbon;

class ScheduleResource extends Resource
{
    protected static ?string $model = Schedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Jadwal';

    protected static ?string $modelLabel = 'Jadwal';

    protected static ?string $pluralModelLabel = 'Jadwal';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Lapangan')
                    ->description('Pilih lapangan dan tanggal untuk membuat jadwal')
                    ->schema([
                        Select::make('field_id')
                            ->label('Lapangan')
                            ->relationship('field', 'name')
                            ->getOptionLabelFromRecordUsing(fn($record): string => "{$record->name} - Court {$record->court_number} ({$record->type})")
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([
                                DatePicker::make('date')
                                    ->label('Tanggal')
                                    ->required()
                                    ->minDate(today())
                                    ->displayFormat('d/m/Y')
                                    ->default(today()),

                                Toggle::make('is_available')
                                    ->label('Tersedia untuk Booking')
                                    ->default(true)
                                    ->helperText('Jadwal dapat dibooking oleh pengguna'),
                            ]),
                    ]),

                Section::make('Waktu Operasional')
                    ->description('Pilih jam operasional untuk hari ini')
                    ->schema([
                        CheckboxList::make('time_slots')
                            ->label('Pilih Jam Operasional')
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
                                '22:00-23:00' => '22:00 - 23:00',
                            ])
                            ->columns(4)
                            ->gridDirection('row')
                            ->default(['07:00-08:00', '08:00-09:00', '09:00-10:00', '10:00-11:00', '11:00-12:00', '13:00-14:00', '14:00-15:00', '15:00-16:00', '16:00-17:00', '17:00-18:00', '19:00-20:00', '20:00-21:00', '21:00-22:00'])
                            ->required()
                            ->helperText('Pilih jam-jam yang tersedia untuk booking. Bisa pilih multiple.'),

                        // Hidden fields for database constraints (temporary solution)
                        Forms\Components\Hidden::make('start_time')
                            ->default('07:00:00'),

                        Forms\Components\Hidden::make('end_time')
                            ->default('08:00:00'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('field.name')
                    ->label('Lapangan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(function ($record) {
                        if (!$record->field) {
                            return 'Field tidak ditemukan';
                        }
                        return $record->field->name . ' - Court ' . ($record->field->court_number ?? '?');
                    }),

                TextColumn::make('field.type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Futsal' => 'success',
                        'Badminton' => 'info',
                        'Badminton & Futsal' => 'purple',
                        default => 'gray',
                    }),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('start_time')
                    ->label('Jam Mulai')
                    ->formatStateUsing(fn($record) => substr($record->start_time, 0, 5))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('end_time')
                    ->label('Jam Selesai')
                    ->formatStateUsing(fn($record) => substr($record->end_time, 0, 5))
                    ->badge()
                    ->color('success')
                    ->sortable(),

                IconColumn::make('is_available')
                    ->label('Tersedia')
                    ->boolean()
                    ->alignCenter(),

                TextColumn::make('bookings_count')
                    ->label('Booking')
                    ->counts('bookings')
                    ->badge()
                    ->color(fn($state): string => $state > 0 ? 'success' : 'gray')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('field_id')
                    ->label('Lapangan')
                    ->relationship('field', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record): string => "{$record->name} - Court {$record->court_number}"),

                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(fn(Builder $query) => $query->whereDate('date', Carbon::today())),

                Filter::make('available')
                    ->label('Tersedia')
                    ->query(fn(Builder $query) => $query->where('is_available', true)),

                Filter::make('upcoming')
                    ->label('Akan Datang')
                    ->query(fn(Builder $query) => $query->where('date', '>', Carbon::today())),

                Filter::make('this_week')
                    ->label('Minggu Ini')
                    ->query(fn(Builder $query) => $query->whereBetween('date', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek()
                    ])),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('toggle_availability')
                    ->label('Toggle Ketersediaan')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Schedule $record) {
                        $record->update(['is_available' => !$record->is_available]);
                    })
                    ->color(fn(Schedule $record): string => $record->is_available ? 'warning' : 'success'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_available')
                        ->label('Tandai Tersedia')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn($records) => $records->each->update(['is_available' => true]))
                        ->color('success'),
                    Tables\Actions\BulkAction::make('mark_unavailable')
                        ->label('Tandai Tidak Tersedia')
                        ->icon('heroicon-o-x-circle')
                        ->action(fn($records) => $records->each->update(['is_available' => false]))
                        ->color('danger'),
                ]),
            ])
            ->defaultSort('date', 'asc')
            ->recordUrl(null);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSchedules::route('/'),
            'create' => Pages\CreateSchedule::route('/create'),
            'edit' => Pages\EditSchedule::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('date', '>=', today())->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}
