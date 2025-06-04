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
use Filament\Forms\Components\Select; // Import Select
use Filament\Forms\Components\TextInput; // Import TextInput
use Filament\Tables\Columns\TextColumn; // Import TextColumn

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark-square'; // Changed icon

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')
                    ->relationship('user', 'name') // Assuming 'name' is the display column in User model
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('field_id')
                    ->relationship('field', 'name') // Assuming 'name' is the display column in Field model
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('schedule_id')
                    ->relationship('schedule', 'id') // Displaying schedule ID, consider a more descriptive column
                    // You might want to create a custom display logic for schedules, e.g., "Date - Start Time"
                    // ->getOptionLabelFromRecordUsing(fn (Schedule $record) => "{$record->date->format('Y-m-d')} ({$record->start_time} - {$record->end_time})")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                TextInput::make('total_price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->searchable()->sortable(),
                TextColumn::make('field.name')->searchable()->sortable(),
                TextColumn::make('schedule.date')->date()->sortable()->label('Schedule Date'),
                // You can add more schedule details if needed, e.g., start_time
                // TextColumn::make('schedule.start_time')->time()->sortable()->label('Start Time'),
                TextColumn::make('status')->searchable()->sortable(),
                TextColumn::make('total_price')->money('IDR')->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PaymentRelationManager::class,
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
