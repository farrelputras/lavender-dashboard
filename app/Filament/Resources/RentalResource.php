<?php

namespace App\Filament\Resources;

use App\Enums\StatusBayar;
use App\Enums\StatusKendaraan;
use App\Enums\StatusRental;
use App\Filament\Resources\RentalResource\Pages;
use App\Filament\Resources\RentalResource\RelationManagers;
use App\Models\Kendaraan;
use App\Models\Rental;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Cache\Events\RetrievingKey;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Exists;

class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;

    protected static ?string $navigationIcon = 'heroicon-s-list-bullet';
    protected static ?string $navigationGroup = 'Bisnis & Keuangan';
    protected static ?string $modelLabel = 'Rental';
    protected static ?string $pluralModelLabel = 'Rental';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('penyewa_id')
                    ->label('Penyewa')
                    ->relationship('penyewa', 'nama')
                    ->searchable()
                    ->preload()
                    ->required(),

                Forms\Components\Select::make('kendaraan_id')
                    ->label('Kendaraan')
                    ->relationship(
                        name: 'kendaraan',
                        titleAttribute: 'nopol',
                        modifyQueryUsing: fn(Builder $query) => $query->where('status', StatusKendaraan::TERSEDIA)
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live(),

                Forms\Components\DateTimePicker::make('tanggal_mulai')
                    ->label('Tanggal/Waktu Mulai Sewa')
                    ->seconds(false)
                    ->displayFormat('d F Y H:i')
                    ->required()
                    ->default(now()),

                Forms\Components\DateTimePicker::make('tanggal_selesai')
                    ->label('Rencana Selesai Sewa')
                    ->seconds(false)
                    ->displayFormat('d F Y H:i'),

                Grid::make(4)
                    ->schema([
                        Forms\Components\TextInput::make('biaya_dibayar')
                            ->label('DP (jika ada)')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('total_biaya')
                            ->label('Total Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),

                        Forms\Components\TextInput::make('bbm_awal')
                            ->label('BBM Awal')
                            ->numeric()
                            ->required()
                            ->suffix('Kotak')
                            ->helperText(
                                function (Get $get): string {
                                    $kendaraanId = $get('kendaraan_id');
                                    if (!$kendaraanId) return '';

                                    $kendaraan = Kendaraan::find($kendaraanId);
                                    if (!$kendaraan) return '';

                                    $historyBbm = $kendaraan->bbm;
                                    if ($historyBbm != 0) {
                                        return 'Terakhir kali BBM: ' . $historyBbm . ' kotak.';
                                    } else return '';
                                }
                            ),

                        Forms\Components\Radio::make('status_bayar')
                            ->label('Status Pembayaran')
                            ->options(StatusBayar::class)
                            ->inline()
                            ->inlineLabel(false)
                            ->default('PENDING')
                            ->required(),
                    ]),

                Forms\Components\Radio::make('status_rental')
                    ->label('Status Rental')
                    ->options(StatusRental::class)
                    ->default('BERJALAN')
                    ->hidden(),

                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tulis catatan disini')
                    ->rows(3)
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_rental')
                    ->options(StatusRental::class),
            ])
            ->actions([
                //
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
            // 'edit' => Pages\EditRental::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScope(SoftDeletingScope::class);
    }
}
