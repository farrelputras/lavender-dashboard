<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenyewaResource\Pages;
use App\Filament\Resources\PenyewaResource\RelationManagers;
use App\Models\Penyewa;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenyewaResource extends Resource
{
    protected static ?string $model = Penyewa::class;

    protected static ?string $navigationIcon = 'heroicon-s-users';
    protected static ?string $navigationGroup = 'Informasi & Data';
    protected static ?string $modelLabel = 'Penyewa';
    protected static ?string $pluralModelLabel = 'Penyewa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->label('Nama Penyewa')
                    ->required(),

                Forms\Components\TextInput::make('no_telp')
                    ->label('No. Telp')
                    ->required()
                    ->prefix('+62'),

                Forms\Components\Textarea::make('alamat')
                    ->label('Alamat')
                    ->required()
                    ->rows(3),

                Forms\Components\TextInput::make('asal')
                    ->label('Asal')
                    ->nullable(),

                Forms\Components\Radio::make('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan',
                    ])
                    ->required(),

                Forms\Components\Select::make('jaminan1')
                    ->label('Jaminan 1')
                    ->options([
                        'KTP' => 'KTP',
                        'KTM' => 'KTM',
                        'SIM' => 'SIM',
                        'LAINNYA' => 'Lainnya',
                    ])
                    ->required(),

                // Forms\Components\FileUpload::make('jaminan2')
                //     ->disk()


                Forms\Components\Select::make('jaminan2')
                    ->label('Jaminan 2')
                    ->options([
                        'KTP' => 'KTP',
                        'KTM' => 'KTM',
                        'SIM' => 'SIM',
                        'LAINNYA' => 'Lainnya',
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Penyewa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('no_telp')
                    ->label('No. Telp')
                    ->prefix('+62')
                    ->searchable(),

                TextColumn::make('alamat')
                    ->label('Alamat')
                    ->searchable(),

                TextColumn::make('hutang')
                    ->label('Hutang')
                    ->numeric()
                    ->prefix('Rp ')
                    ->default(0)
                    ->sortable()
                    ->color(fn($state) => $state > 0 ? 'danger' : 'default'),
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
            'index' => Pages\ListPenyewas::route('/'),
            'create' => Pages\CreatePenyewa::route('/create'),
            'edit' => Pages\EditPenyewa::route('/{record}/edit'),
        ];
    }
}
