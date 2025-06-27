<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalResource\Pages;
use App\Filament\Resources\RentalResource\RelationManagers;
use App\Models\Rental;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;

    protected static ?string $navigationIcon = 'heroicon-s-list-bullet';

    public static function getModel(): string
    {
        return \App\Models\Kendaraan::class;
    }

    public static function getModelLabel(): string
    {
        return 'Rental';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Rental';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Bisnis & Keuangan';
    }
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('penyewa_id')
                    ->label('Penyewa')
                    ->relationship('penyewa', 'nama') // adjust if the name column is different
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship('kendaraan', 'nopol') // or use 'model'
                    ->searchable()
                    ->required(),
                
                Forms\Components\DateTimePicker::make('rental_start_date')
                    ->label('Mulai Sewa')
                    ->required(),

                Forms\Components\DateTimePicker::make('rental_end_date')
                    ->label('Rencana Selesai Sewa')
                    ->required(),

                Forms\Components\TextInput::make('total_price')
                    ->label('Total Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListRentals::route('/'),
            'create' => Pages\CreateRental::route('/create'),
            'edit' => Pages\EditRental::route('/{record}/edit'),
        ];
    }
}
