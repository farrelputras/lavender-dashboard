<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KendaraanResource\Pages;
use App\Enums\StatusKendaraan;
use App\Filament\Resources\KendaraanResource\RelationManagers;
use App\Models\Kendaraan;
use Dom\Text;
use Filament\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KendaraanResource extends Resource
{
    protected static ?string $model = Kendaraan::class;

    protected static ?string $navigationIcon = 'fas-motorcycle';
    protected static ?string $navigationGroup = 'Informasi & Data';
    protected static ?string $modelLabel = 'Kendaraan';
    protected static ?string $pluralModelLabel = 'Kendaraan';

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

                Forms\Components\FileUpload::make('gambar')
                    ->label('Foto Kendaraan')
                    ->directory('fotoKendaraan'),

                Forms\Components\Select::make('status')
                    ->label('Status Kendaraan')
                    ->required()
                    ->options(StatusKendaraan::class)
                    ->default('TERSEDIA'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nopol')
                    ->label('Plat Nomor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model')
                    ->label('Model')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        StatusKendaraan::TERSEDIA => 'success',
                        StatusKendaraan::DISEWA => 'warning',
                        StatusKendaraan::PERBAIKAN => 'danger',
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
