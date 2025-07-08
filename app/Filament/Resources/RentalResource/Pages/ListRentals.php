<?php

namespace App\Filament\Resources\RentalResource\Pages;

use App\Filament\Resources\RentalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Models\Rental;
use App\Enums\StatusRental;
use App\Enums\StatusBayar;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Filament\Forms\Contracts\HasForms;

use Filament\Actions\Action as ModalAction;
use Illuminate\View\View;

class ListRentals extends ListRecords
{
    protected static string $resource = RentalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Rental Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'pesanan' => Tab::make('Semua Pesanan'),

            'pesanan_aktif' => Tab::make('Pesanan Aktif')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status_rental', 'BERJALAN')),

            'pembayaran' => Tab::make('Pembayaran')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status_rental', 'SELESAI')),
        ];
    }

    public function table(Table $table): Table
    {
        $activeTab = $this->activeTab;

        return $table
            ->columns([
                // All Tabs
                TextColumn::make('id')
                    ->label('ID Rental')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('penyewa.nama')
                    ->label('Penyewa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kendaraan.model')
                    ->label('Kendaraan')
                    ->searchable(),

                TextColumn::make('kendaraan.nopol')
                    ->label('Plat Nomor')
                    ->searchable()
                    ->sortable(),

                // Tab Pesanan Aktif
                TextColumn::make('tanggal_mulai')
                    ->label('Mulai Sewa')
                    ->dateTime('j F Y, H:i')
                    ->sortable()
                    ->visible(fn(): bool => in_array($this->activeTab, ['pesanan_aktif'])),

                TextColumn::make('status_rental')
                    ->label('Status Rental')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        StatusRental::BERJALAN => 'warning',
                        StatusRental::SELESAI => 'success',
                    })
                    ->sortable()
                    ->visible(fn(): bool => in_array($this->activeTab, ['pesanan', 'pesanan_aktif'])),

                // Tab Pembayaran
                TextColumn::make('total_biaya')
                    ->label('Total Biaya')
                    ->numeric()
                    ->prefix('Rp ')
                    ->visible(fn(): bool => in_array($this->activeTab, ['pembayaran'])),

                TextColumn::make('biaya_dibayar')
                    ->label('Sudah Dibayar')
                    ->numeric()
                    ->prefix('Rp ')
                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                    ->visible(fn(): bool => in_array($this->activeTab, ['pembayaran'])),

                TextColumn::make('sisa_biaya')
                    ->label('Sisa Tagihan')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable()
                    ->state(function (Rental $record): float {
                        return $record->total_biaya - $record->biaya_dibayar;
                    })
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->visible(fn(): bool => in_array($this->activeTab, ['pembayaran'])),

                TextColumn::make('status_bayar')
                    ->label('Status Pembayaran')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        StatusBayar::LUNAS => 'success',
                        StatusBayar::PENDING => 'warning',
                    })
                    ->sortable()
                    ->visible(fn(): bool => in_array($this->activeTab, ['pembayaran', 'pesanan'])),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status_rental')
                    ->options(StatusRental::class),
            ])
            ->actions([
                // Selesaikan Rental Action
                Action::make('selesaikan_rental')
                    ->button()
                    ->label('SELESAIKAN')
                    ->icon('heroicon-o-check-circle')
                    // RULE 1: Only show if the rental status is 'BERJALAN'
                    ->visible(fn(Rental $record): bool => $record->status_rental === StatusRental::BERJALAN)
                    ->form([
                        Forms\Components\Section::make('Ringkasan Rental')
                            ->description('Cek kembali informasi rental sebelum menyelesaikan.')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('penyewa.nama')
                                    ->label('Nama Penyewa')
                                    ->default(fn(Rental $record): string => $record->penyewa->nama)
                                    ->disabled(),

                                Forms\Components\TextInput::make('kendaraan.nopol')
                                    ->label('Plat Nomor')
                                    ->default(fn(Rental $record): string => $record->kendaraan->nopol)
                                    ->disabled(),

                                Forms\Components\DateTimePicker::make('tanggal_mulai')
                                    ->label('Tanggal/Waktu Mulai Sewa')
                                    ->default(fn(Rental $record) => $record->tanggal_mulai)
                                    ->disabled(),

                                Forms\Components\Placeholder::make('kalkulasi_biaya')
                                    ->label('Kalkulasi Biaya')
                                    // ->content(function (Rental $record): string {

                                    // })
                                    ->live(),
                            ]),

                        //User Input                        
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('tanggal_selesai')
                                    ->label('Tanggal/Waktu Selesai Sewa')
                                    ->required(),

                                Forms\Components\TextInput::make('total_biaya')
                                    ->label('Total Biaya')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('Tulis catatan disini')
                                    ->default(fn(Rental $record): string => $record->notes ?? '')
                                    ->rows(3)
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->action(function (Rental $record, array $data) {
                        //Update the rental status to 'SELESAI'
                        $record->update([
                            'tanggal_selesai' => $data['tanggal_selesai'],
                            'total_biaya' => $data['total_biaya'],
                            'notes' => $data['notes'],
                            'status_rental' => StatusRental::SELESAI,
                        ]);

                        $record->kendaraan->update(['status' => 'TERSEDIA']);

                        Notification::make()
                            ->title('Rental Selesai')
                            ->body("Kendaraan {$record->kendaraan->nopol} saat ini tersedia.")
                            ->success()
                            ->send();
                    })
                // ->registerModalActions([
                //     ModalAction::make('submitAndPay')
                //         ->label('Submit + Bayar')
                //         ->color('primary')
                //         // This is the logic for the NEW button
                //         ->action(function (HasForms $livewire, Rental $record, array $data) {

                //             // 2. Run the "selesaikan" logic
                //             $record->update([
                //                 'tanggal_selesai' => $data['tanggal_selesai'],
                //                 'total_biaya' => $data['total_biaya'],
                //                 'notes' => $data['notes'],
                //                 'status_rental' => StatusRental::SELESAI,
                //             ]);
                //             $record->kendaraan->update(['status' => 'TERSEDIA']);
                //             Notification::make()
                //                 ->title('Rental Selesai')
                //                 ->body("Kendaraan {$record->kendaraan->nopol} saat ini tersedia.")
                //                 ->success()
                //                 ->send();

                //             // 3. Programmatically open the 'bayar' modal for the same record
                //             $livewire->mountAction('bayar', ['record' => $record->id]);
                //         }),
                // ])
                // // NEW: Add the custom content area to the modal footer
                // ->modalContentFooter(fn(Action $action): View => view(
                //     'filament.actions.selesaikan-rental-footer',
                //     ['action' => $action]
                // ))
                ,

                // Bayar Action
                Action::make('bayar')
                    ->button()
                    ->label('BAYAR')
                    ->icon('heroicon-o-banknotes')
                    // RULE 2: Only show if the payment status is NOT 'LUNAS'
                    ->visible(fn(Rental $record): bool => $record->status_rental === StatusRental::SELESAI && $record->status_bayar !== StatusBayar::LUNAS)
                    ->form([
                        Section::make('Pembayaran')
                            ->description('Periksa kembali detail pembayaran.')
                            ->columns(2)
                            ->schema([
                                Forms\Components\TextInput::make('penyewa.nama')
                                    ->label('Nama Penyewa')
                                    ->default(fn(Rental $record): string => $record->penyewa->nama)
                                    ->disabled(),

                                Forms\Components\TextInput::make('kendaraan.nopol')
                                    ->label('Plat Nomor')
                                    ->default(fn(Rental $record): string => $record->kendaraan->nopol)
                                    ->disabled(),

                                Grid::make(3)
                                    ->schema([
                                        Forms\Components\Placeholder::make('total_biaya')
                                            ->label('Total Biaya')
                                            ->content(fn(Rental $record): string => 'Rp ' . number_format($record->total_biaya, 0, ',', '.')),

                                        Forms\Components\Placeholder::make('biaya_dibayar')
                                            ->label('Sudah Dibayar')
                                            ->content(function (Rental $record): HtmlString {
                                                $formattedValue = 'Rp ' . number_format($record->biaya_dibayar, 0, ',', '.');

                                                return new HtmlString("<span style='color: #22c55e; font-weight: 500;'>" . e($formattedValue) . "</span>");
                                            }),

                                        Forms\Components\Placeholder::make('sisa_biaya')
                                            ->label('Sisa Tagihan')
                                            ->content(function (Rental $record): HtmlString {
                                                $sisa = $record->total_biaya - $record->biaya_dibayar;
                                                $formattedValue = 'Rp ' . number_format($sisa, 0, ',', '.');

                                                $color = $sisa > 0 ? '#ef4444' : '#22c55e';

                                                return new HtmlString("<span style='color: {$color}; font-weight: 500;'>" . e($formattedValue) . "</span>");
                                            }),
                                    ])


                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('tanggal_transaksi')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\TextInput::make('nominal_transaksi')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                            ])
                    ])
                    ->action(function (Rental $record, array $data) {
                        $record->transaksi()->create($data);

                        $newBiayaDibayar = $record->biaya_dibayar + $data['nominal_transaksi'];
                        $record->update(['biaya_dibayar' => $newBiayaDibayar]);
                        if ($record->biaya_dibayar >= $record->total_biaya) {
                            $record->update(['status_bayar' => StatusBayar::LUNAS]);
                        }

                        Notification::make()->title('Pembayaran berhasil dicatat')->success()->send();
                    }),
            ]);
    }
}
