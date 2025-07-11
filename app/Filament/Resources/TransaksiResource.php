<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransaksiResource\Pages;
use App\Filament\Resources\TransaksiResource\RelationManagers;
use App\Models\Transaksi;
use App\Models\Rental;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Bisnis & Keuangan';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?string $pluralModelLabel = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Detail Rental')
                    ->description('Pastikan detail rental sudah benar sebelum melakukan Transaksi.')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('rental_id')
                            ->label('ID Rental')
                            ->relationship(
                                name: 'rental',
                                titleAttribute: 'id',
                                // modifyQueryUsing: fn(Builder $query) => $query->where('status', 'BERJALAN')
                            )
                            ->searchable()
                            ->required()
                            ->live() // IMPORTANT: This makes the form reactive
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (is_null($state)) {
                                    // Clear fields if rental is deselected
                                    $set('total_biaya_display', 0);
                                    $set('sudah_dibayar_display', 0);
                                    $set('sisa_tagihan_display', 0);
                                    $set('rental.penyewa.nama', null);
                                    $set('rental.kendaraan.nopol', null);
                                    return;
                                }

                                $rental = Rental::with('Transaksi')->find($state);
                                if ($rental) {
                                    $totalBiaya = $rental->total_biaya;
                                    // Sum all previous payments for this rental
                                    $sudahDibayar = $rental->Transaksi->sum('total_bayar');
                                    $sisaTagihan = $totalBiaya - $sudahDibayar;

                                    // Use $set to update the placeholder fields
                                    $set('total_biaya_display', $totalBiaya);
                                    $set('sudah_dibayar_display', $sudahDibayar);
                                    $set('sisa_tagihan_display', $sisaTagihan);

                                    $set('rental.penyewa.nama', $rental->penyewa->nama ?? null);
                                    $set('rental.kendaraan.nopol', $rental->kendaraan->nopol ?? null);
                                }
                            }),

                        Forms\Components\TextInput::make('rental.penyewa.nama')
                            ->label('Nama Penyewa')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('rental.kendaraan.nopol')
                            ->label('Plat Nomor')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\TextInput::make('rental.kendaraan.model')
                            ->label('Kendaraan')
                            ->disabled()
                            ->dehydrated(false),
                    ]),

                Section::make('Ringkasan Transaksi')
                    ->schema([
                        // Use Placeholders for a clean, read-only summary
                        Forms\Components\Placeholder::make('total_biaya_display')
                            ->label('Total Biaya Rental')
                            // Format the number for better readability
                            ->content(fn($get): string => 'Rp ' . number_format($get('total_biaya_display') ?? 0, 0, ',', '.')),

                        Forms\Components\Placeholder::make('sudah_dibayar_display')
                            ->label('Sudah Dibayar')
                            ->content(fn($get): string => 'Rp ' . number_format($get('sudah_dibayar_display') ?? 0, 0, ',', '.')),

                        Forms\Components\Placeholder::make('sisa_tagihan_display')
                            ->label('Sisa Tagihan')
                            ->content(fn($get): string => 'Rp ' . number_format($get('sisa_tagihan_display') ?? 0, 0, ',', '.')),
                    ])->columns(3),

                Forms\Components\TextInput::make('total_biaya')
                    ->label('Total Biaya')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->prefix('Rp ')
                    ->placeholder('Masukkan total biaya'),

                Forms\Components\DateTimePicker::make('tanggal_bayar')
                    ->label('Tanggal Bayar')
                    ->required()
                    ->placeholder('Pilih tanggal bayar')
                    ->default(now()),

                TextInput::make('notes')
                    ->label('Catatan')
                    ->placeholder('Tulis catatan disini')
                    ->columnSpanFull(),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $rental = Rental::with('Transaksi')->find($data['rental_id']);

        if ($rental) {
            $totalBiayaRental = $rental->total_biaya;
            // Sum of payments already in the database
            $sudahDibayarSebelumnya = $rental->Transaksi->sum('total_bayar');
            // The new payment amount from the form
            $TransaksiSaatIni = $data['total_bayar'];

            // Calculate the new grand total paid amount
            $totalSudahDibayar = $sudahDibayarSebelumnya + $TransaksiSaatIni;

            // Set the status for this new payment record
            if ($totalSudahDibayar >= $totalBiayaRental) {
                $data['status'] = 'LUNAS';
            } else {
                $data['status'] = 'PENDING';
            }
        }

        return $data;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_transaksi')
                    ->label('Tanggal Transaksi')
                    ->date('j M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('rental.penyewa.nama')
                    ->label('Nama Penyewa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nominal_transaksi')
                    ->label('Nominal Transaksi')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),

                TextColumn::make('rental_id')
                    ->label('ID Rental')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('notes')
                    ->label('Notes')
                    ->wrap(),

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
}
