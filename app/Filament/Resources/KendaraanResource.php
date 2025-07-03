<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KendaraanResource\Pages;
use App\Filament\Resources\KendaraanResource\RelationManagers;
use App\Models\Kendaraan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KendaraanResource extends Resource
{
    protected static ?string $model = Kendaraan::class;

    protected static ?string $navigationIcon = 'fas-motorcycle';

    public static function getModel(): string
    {
        return \App\Models\Kendaraan::class;
    }

    public static function getModelLabel(): string
    {
        return 'Kendaraan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kendaraan';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Informasi & Data';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nopol')
                    ->label('Nomor Polisi')
                    ->required()
                    ->placeholder('N 1234 ABC')
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('jenis')
                    ->label('Jenis Kendaraan')
                    ->required()
                    ->options([
                        'MOBIL' => 'Mobil',
                        'MOTOR' => 'Motor',
                    ]),

                Forms\Components\TextInput::make('model')
                    ->label('Model')
                    ->placeholder('Beat Street, Avanza')
                    ->required(),

                Forms\Components\TextInput::make('tahun')
                    ->label('Tahun')
                    ->numeric()
                    ->minValue(1900)
                    ->maxValue(now()->year)
                    ->required(),

                Forms\Components\TextInput::make('kilometer')
                    ->label('Kilometer')
                    ->numeric()
                    ->suffix('km')
                    ->default(0),

                // Forms\Components\FileUpload::make('warna')
                //     ->disk()

                Forms\Components\Select::make('status')
                    ->label('Status Kendaraan')
                    ->required()
                    ->options([
                        'TERSEDIA' => 'Tersedia',
                        'DISEWA' => 'Disewa',
                        'PERBAIKAN' => 'Perbaikan',
                    ])
                    ->default('TERSEDIA'),
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
            'index' => Pages\ListKendaraans::route('/'),
            'create' => Pages\CreateKendaraan::route('/create'),
            'edit' => Pages\EditKendaraan::route('/{record}/edit'),
        ];
    }
}
