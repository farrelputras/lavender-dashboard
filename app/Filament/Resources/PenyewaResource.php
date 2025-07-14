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
use Illuminate\View\ComponentSlot;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;

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
                Forms\Components\Section::make('Informasi Umum')
                    ->columns(6)
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Penyewa')
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('no_telp')
                            ->label('No. Telp')
                            ->required()
                            ->prefix('+62')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('asal')
                            ->label('Asal')
                            ->nullable()
                            ->columnSpan(2),

                        Forms\Components\Textarea::make('alamat')
                            ->label('Alamat')
                            ->required()
                            ->columnSpan(3),

                        Forms\Components\Radio::make('Jenis Kelamin')
                            ->label('Jenis Kelamin')
                            ->options([
                                'L' => 'Laki-laki',
                                'P' => 'Perempuan',
                            ])
                            ->required()
                            ->columnSpan(3),
                    ]),

                Forms\Components\Section::make('Jaminan')
                    ->schema([
                        Forms\Components\Select::make('jaminan1')
                            ->label('Jaminan 1')
                            ->options([
                                'KTP' => 'KTP',
                                'KTM' => 'KTM',
                                'SIM' => 'SIM',
                                'LAINNYA' => 'Lainnya',
                            ])
                            ->required(),

                        Forms\Components\FileUpload::make('foto_jaminan1')
                            ->image()
                            ->directory('fotoJaminan')
                            ->label('Foto Jaminan 1')
                            ->required(),

                        Forms\Components\Select::make('jaminan2')
                            ->label('Jaminan 2')
                            ->options([
                                'KTP' => 'KTP',
                                'KTM' => 'KTM',
                                'SIM' => 'SIM',
                                'LAINNYA' => 'Lainnya',
                            ]),

                        Forms\Components\FileUpload::make('foto_jaminan2')
                            ->image()
                            ->label('Foto Jaminan 2',)

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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\ImageEntry::make('foto_jaminan1')
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
