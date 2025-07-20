<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KendaraanResource\Pages;
use App\Enums\StatusKendaraan;
use App\Filament\Resources\KendaraanResource\Widgets\KendaraanStats;
use App\Models\Kendaraan;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;

use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Infolists\Components\TextEntry;

use Filament\Pages\SubNavigationPosition;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;

use Filament\Support\Enums\FontWeight;

class KendaraanResource extends Resource
{
    protected static ?string $model = Kendaraan::class;

    protected static ?string $navigationIcon = 'fas-motorcycle';
    protected static ?string $navigationGroup = 'Informasi & Data';
    protected static ?string $modelLabel = 'Kendaraan';
    protected static ?string $pluralModelLabel = 'Kendaraan';

    protected static ?int $navigationSort = 1;
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static ?string $navigationBadgeTooltip = 'Total Kendaraan';

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([

                        //Section 1 Left Side
                        Forms\Components\Group::make()
                            ->schema([


                                Forms\Components\Section::make('Informasi Utama')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\Select::make('jenis')
                                                    ->label('Jenis Kendaraan')
                                                    ->required()
                                                    ->options([
                                                        'MOBIL' => 'Mobil',
                                                        'MOTOR' => 'Motor',
                                                    ]),

                                                Forms\Components\TextInput::make('model')
                                                    ->label('Model')
                                                    ->placeholder('Merk Model, cth: Toyota Avanza')
                                                    ->required(),

                                                Forms\Components\TextInput::make('nopol')
                                                    ->label('Plat Nomor')
                                                    ->required()
                                                    ->placeholder('N 1234 ABC')
                                                    ->unique(ignoreRecord: true),

                                                Forms\Components\TextInput::make('tahun')
                                                    ->label('Tahun')
                                                    ->numeric()
                                                    ->minValue(1900)
                                                    ->maxValue(now()->year)
                                                    ->required(),

                                                Forms\Components\TextInput::make('harga_6jam')
                                                    ->label('Harga 6 Jam')
                                                    ->required()
                                                    ->prefix('Rp ')
                                                    ->numeric()
                                                    ->default(25000),

                                                Forms\Components\TextInput::make('harga_12jam')
                                                    ->label('Harga 12 Jam')
                                                    ->required()
                                                    ->prefix('Rp ')
                                                    ->numeric()
                                                    ->default(35000),

                                                Forms\Components\TextInput::make('harga_24jam')
                                                    ->label('Harga 24 Jam')
                                                    ->required()
                                                    ->prefix('Rp ')
                                                    ->numeric()
                                                    ->default(50000),

                                                Forms\Components\TextInput::make('bbm_per_kotak')
                                                    ->label('Harga BBM Per Kotak')
                                                    ->required()
                                                    ->prefix('Rp ')
                                                    ->numeric()
                                                    ->default(5000),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Informasi Lainnya')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\DatePicker::make('tgl_pajak')
                                                    ->label('Tanggal Pajak'),
                                                // harus ada validity buat biar tgl & bulan saja

                                                Forms\Components\TextInput::make('no_gps')
                                                    ->label('No. GPS')
                                                    ->prefix('+62 ')
                                                    ->numeric()
                                                    ->doesntStartWith(0),

                                                Forms\Components\TextInput::make('stnk_nama')
                                                    ->label('STNK a.n.')
                                                    ->autocapitalize('characters'),

                                                Forms\Components\TextInput::make('imei')
                                                    ->label('IMEI')
                                                    ->numeric()
                                                    ->length(15),
                                            ])
                                    ]),
                            ])->columnSpan(2),

                        //Section 2 Right Side
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Section::make('Foto Kendaraan')
                                    ->schema([
                                        Forms\Components\FileUpload::make('gambar')
                                            ->label('Foto Kendaraan')
                                            ->image()
                                            ->hiddenLabel(),
                                    ]),

                                Forms\Components\Section::make('Informasi Dinamis')
                                    ->schema([
                                        Forms\Components\TextInput::make('kilometer')
                                            ->label('Kilometer Saat Ini')
                                            ->numeric()
                                            ->suffix('km')
                                            ->default(0),

                                        Forms\Components\Select::make('status')
                                            ->label('Status Kendaraan')
                                            ->required()
                                            ->options(StatusKendaraan::class)
                                            ->default('TERSEDIA'),
                                    ]),
                            ])->columnSpan(1),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Stack::make([
                    ImageColumn::make('gambar')
                        ->height('100%')
                        ->width('100%'),

                    Stack::make([
                        TextColumn::make('nopol')
                            ->label('Plat Nomor')
                            ->size(TextColumn\TextColumnSize::Large)
                            ->weight(FontWeight::Bold)
                            ->searchable()
                            ->sortable(),

                        TextColumn::make('model')
                            ->label('Model')
                            ->searchable()
                            ->sortable(),
                    ]),

                    TextColumn::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn($state) => match ($state) {
                            StatusKendaraan::TERSEDIA => 'success',
                            StatusKendaraan::DISEWA => 'warning',
                            StatusKendaraan::PERBAIKAN => 'danger',
                        }),
                ])->space(3),
            ])
            ->contentGrid([
                'md' => 3,
            ])
            ->filters([
                //filter mobil atau motor
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    // public static function infolist(Infolist $infolist): Infolist
    // {
    //     return $infolist
    //         ->schema([
    //             TextEntry::make('nopol'),
    //         ]);
    // }

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
            'view' => Pages\ViewKendaraan::route('/{record}'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewKendaraan::class,
            Pages\EditKendaraan::class,
        ]);
    }
}
