<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Filament\Resources\BookingResource\RelationManagers;
use App\Models\Booking;
use App\Models\User; // Import User model
use App\Models\Field; // Import Field model
use App\Models\Schedule; // Import Schedule model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Carbon\Carbon;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationLabel = 'Booking';

    protected static ?string $modelLabel = 'Booking';

    protected static ?string $pluralModelLabel = 'Booking';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('customer_name')
                    ->label('Nama Customer')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Masukkan nama customer'),

                Select::make('field_id')
                    ->label('Lapangan')
                    ->relationship('field', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record): string => "{$record->name} - Court {$record->court_number} ({$record->type}) - Rp " . number_format($record->price_per_hour, 0, ',', '.') . "/jam")
                    ->searchable()
                    ->preload()
                    ->required()
                    ->placeholder('Pilih lapangan')
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('schedule_ids', null);
                        $set('total_price', 0);
                    }),

                Select::make('schedule_ids')
                    ->label('Jadwal')
                    ->multiple()
                    ->options(function (Get $get) {
                        $fieldId = $get('field_id');
                        if (!$fieldId) {
                            return [];
                        }

                        $availableSchedules = Schedule::where('field_id', $fieldId)
                            ->where('is_available', true)
                            ->where('date', '>=', today())
                            ->orderBy('date')
                            ->orderBy('start_time')
                            ->get();

                        // Filter out schedules that have bookings
                        $filteredSchedules = $availableSchedules->filter(function ($schedule) {
                            return $schedule->getAllBookings()->count() === 0;
                        });

                        return $filteredSchedules
                            ->mapWithKeys(function ($schedule) {
                                return [
                                    $schedule->id => $schedule->date->format('d M Y') . ' (' .
                                        substr($schedule->start_time, 0, 5) . ' - ' .
                                        substr($schedule->end_time, 0, 5) . ')'
                                ];
                            });
                    })
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih jadwal (bisa lebih dari 1)')
                    ->live()
                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                        $fieldId = $get('field_id');
                        if (!$fieldId || !$state) {
                            $set('total_price', 0);
                            return;
                        }

                        $field = Field::find($fieldId);
                        if (!$field) {
                            $set('total_price', 0);
                            return;
                        }

                        $scheduleCount = count($state);
                        $totalPrice = $field->price_per_hour * $scheduleCount;
                        $set('total_price', $totalPrice);
                    }),

                Select::make('status')
                    ->label('Status Booking')
                    ->required()
                    ->options([
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ])
                    ->default('pending'),

                TextInput::make('total_price')
                    ->label('Total Harga')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('Akan dihitung otomatis')
                    ->disabled()
                    ->dehydrated(),

                Select::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->required()
                    ->options([
                        'transfer' => 'Transfer Bank',
                        'qris' => 'QRIS',
                        'cash' => 'Cash',
                    ])
                    ->placeholder('Pilih metode pembayaran'),

                Forms\Components\FileUpload::make('payment_proof')
                    ->label('Bukti Pembayaran')
                    ->image()
                    ->directory('payment-proofs')
                    ->disk('public')
                    ->visibility('public')
                    ->nullable()
                    ->helperText('Upload bukti pembayaran (opsional untuk cash)'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('field.name')
                    ->label('Lapangan')
                    ->formatStateUsing(function ($record) {
                        if (!$record->field) {
                            return 'Lapangan tidak ditemukan';
                        }
                        return $record->field->name . ' - Court ' . ($record->field->court_number ?? '?');
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('field.type')
                    ->label('Tipe')
                    ->formatStateUsing(function ($record) {
                        return $record->field ? $record->field->type : 'N/A';
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Futsal' => 'success',
                        'Badminton' => 'info',
                        'Basketball' => 'warning',
                        'Tennis' => 'danger',
                        'Volleyball' => 'primary',
                        'N/A' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('schedule_times')
                    ->label('Jadwal')
                    ->formatStateUsing(function ($record) {
                        $scheduleIds = null;

                        // Handle JSON string or array
                        if ($record->schedule_ids) {
                            $scheduleIds = is_string($record->schedule_ids) ?
                                json_decode($record->schedule_ids, true) :
                                $record->schedule_ids;
                        }

                        if ($scheduleIds && is_array($scheduleIds)) {
                            $schedules = Schedule::whereIn('id', $scheduleIds)->orderBy('date')->orderBy('start_time')->get();
                            $count = $schedules->count();

                            if ($count == 0) {
                                return 'Jadwal tidak ditemukan';
                            }

                            $firstSchedule = $schedules->first();

                            if (!$firstSchedule) {
                                return 'Jadwal tidak valid';
                            }

                            if ($count == 1) {
                                return $firstSchedule->date->format('d M') . ' (' . substr($firstSchedule->start_time, 0, 5) . '-' . substr($firstSchedule->end_time, 0, 5) . ')';
                            } else {
                                return $count . ' jadwal - ' . $firstSchedule->date->format('d M') . ' dst.';
                            }
                        }

                        // Fallback untuk data lama
                        if ($record->schedule) {
                            return $record->schedule->date->format('d M Y') . ' (' . substr($record->schedule->start_time, 0, 5) . '-' . substr($record->schedule->end_time, 0, 5) . ')';
                        }

                        return 'Tidak ada jadwal';
                    })
                    ->wrap()
                    ->tooltip(function ($record) {
                        $scheduleIds = null;

                        // Handle JSON string or array
                        if ($record->schedule_ids) {
                            $scheduleIds = is_string($record->schedule_ids) ?
                                json_decode($record->schedule_ids, true) :
                                $record->schedule_ids;
                        }

                        if ($scheduleIds && is_array($scheduleIds)) {
                            $schedules = Schedule::whereIn('id', $scheduleIds)->orderBy('date')->orderBy('start_time')->get();
                            if ($schedules->isEmpty()) {
                                return 'Tidak ada jadwal ditemukan';
                            }
                            return $schedules->map(function ($schedule) {
                                return $schedule->date->format('d M Y') . ' (' . substr($schedule->start_time, 0, 5) . '-' . substr($schedule->end_time, 0, 5) . ')';
                            })->join(' | ');
                        }
                        return null;
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'cancelled' => 'danger',
                        'completed' => 'primary',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('total_price')
                    ->label('Total Harga')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('payment_method')
                    ->label('Metode Bayar')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'cash' => 'Cash',
                        default => 'N/A',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'transfer' => 'info',
                        'qris' => 'success',
                        'cash' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),

                SelectFilter::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->options([
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        'cash' => 'Cash',
                    ]),

                SelectFilter::make('field_id')
                    ->label('Lapangan')
                    ->relationship('field', 'name'),

                Filter::make('today')
                    ->label('Hari Ini')
                    ->query(
                        fn(\Illuminate\Database\Eloquent\Builder $query) =>
                        $query->whereHas(
                            'schedule',
                            fn(\Illuminate\Database\Eloquent\Builder $query) =>
                            $query->whereDate('date', Carbon::today())
                        )
                    ),

                Filter::make('this_week')
                    ->label('Minggu Ini')
                    ->query(
                        fn(\Illuminate\Database\Eloquent\Builder $query) =>
                        $query->whereHas(
                            'schedule',
                            fn(\Illuminate\Database\Eloquent\Builder $query) =>
                            $query->whereBetween('date', [
                                Carbon::now()->startOfWeek(),
                                Carbon::now()->endOfWeek()
                            ])
                        )
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['field', 'schedule']));
    }

    public static function getRelations(): array
    {
        return [
            // PaymentRelationManager removed - payment fields now integrated directly into booking form
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}
