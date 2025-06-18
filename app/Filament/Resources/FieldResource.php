<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FieldResource\Pages;
use App\Filament\Resources\FieldResource\RelationManagers;
use App\Models\Field;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;

class FieldResource extends Resource
{
    protected static ?string $model = Field::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Lapangan';

    protected static ?string $modelLabel = 'Lapangan';

    protected static ?string $pluralModelLabel = 'Lapangan';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nama Lapangan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Balikpapan Sport Center'),

                TextInput::make('location')
                    ->label('Lokasi')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Jl. MT Haryono No.17, Balikpapan'),

                Select::make('type')
                    ->label('Tipe Olahraga')
                    ->required()
                    ->options([
                        'Futsal' => 'Futsal',
                        'Badminton' => 'Badminton',
                        'Badminton & Futsal' => 'Badminton & Futsal',
                    ])
                    ->searchable(),

                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Lapangan')
                    ->image()
                    ->directory('field-images')
                    ->disk('public')
                    ->visibility('public')
                    ->nullable()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(2048)
                    ->helperText('Upload gambar lapangan (opsional). Format: JPG, PNG, WEBP. Max: 2MB')
                    ->columnSpanFull(),

                TextInput::make('court_number')
                    ->label('Nomor Lapangan')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->maxValue(10)
                    ->helperText('Nomor lapangan (1-10)'),

                TextInput::make('price_per_hour')
                    ->label('Harga per Jam')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('75000')
                    ->helperText('Masukkan harga dalam rupiah tanpa titik atau koma'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('image')
                    ->label('Gambar')
                    ->formatStateUsing(function ($state, $record) {
                        if ($state) {
                            $url = asset('storage/' . $state);
                            return new \Illuminate\Support\HtmlString(
                                '<div class="flex justify-center">' .
                                '<img src="' . $url . '" class="w-16 h-12 object-cover rounded-lg shadow-sm border border-gray-200" onerror="this.style.display=\'none\'; this.parentNode.innerHTML=\'<div class=\\\'w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs\\\'>No Image</div>\'">' .
                                '</div>'
                            );
                        }
                        return new \Illuminate\Support\HtmlString(
                            '<div class="flex justify-center">' .
                            '<div class="w-16 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400 text-xs border">Default</div>' .
                            '</div>'
                        );
                    })
                    ->html()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Lapangan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('court_number')
                    ->label('Court')
                    ->alignCenter()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                TextColumn::make('type')
                    ->label('Tipe Olahraga')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Futsal' => 'success',
                        'Badminton' => 'info',
                        'Badminton & Futsal' => 'purple',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('location')
                    ->label('Lokasi')
                    ->limit(40)
                    ->searchable()
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
                    }),

                TextColumn::make('price_per_hour')
                    ->label('Harga/Jam')
                    ->money('IDR')
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('bookings_count')
                    ->label('Total Booking')
                    ->counts('bookings')
                    ->badge()
                    ->color('primary')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Olahraga')
                    ->options([
                        'Futsal' => 'Futsal',
                        'Badminton' => 'Badminton',
                        'Badminton & Futsal' => 'Badminton & Futsal',
                    ]),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListFields::route('/'),
            'create' => Pages\CreateField::route('/create'),
            'edit' => Pages\EditField::route('/{record}/edit'),
        ];
    }
}
