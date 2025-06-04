<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\Booking; // Import Booking model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select; // Import Select
use Filament\Forms\Components\TextInput; // Import TextInput
use Filament\Forms\Components\FileUpload; // Import FileUpload
use Filament\Tables\Columns\TextColumn; // Import TextColumn
use Filament\Tables\Columns\ImageColumn; // Import ImageColumn

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card'; // Changed icon

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('booking_id')
                    ->relationship('booking', 'id') // Displaying booking ID, consider a more descriptive column
                    // ->getOptionLabelFromRecordUsing(fn (Booking $record) => "Booking #{$record->id} - {$record->user->name} - {$record->field->name}")
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('payment_method')
                    ->required()
                    ->maxLength(255),
                Select::make('payment_status') // Using Select for predefined statuses
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ])
                    ->required(),
                FileUpload::make('proof')
                    ->image() // Specify that it's an image for preview and validation
                    ->directory('payment-proofs') // Optional: directory to store uploads
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('booking_id')->searchable()->sortable()->label('Booking ID'),
                // You can display more booking info like:
                // TextColumn::make('booking.user.name')->searchable()->sortable()->label('User'),
                // TextColumn::make('booking.field.name')->searchable()->sortable()->label('Field'),
                TextColumn::make('payment_method')->searchable()->sortable(),
                TextColumn::make('payment_status')->badge() // Display as a badge
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })->searchable()->sortable(),
                ImageColumn::make('proof')->square()->toggleable(), // Display image, make it toggleable
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
