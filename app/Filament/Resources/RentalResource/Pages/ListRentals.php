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
use App\Enums\StatusKendaraan;
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
use Filament\Forms\Get;
use App\Models\Kendaraan;
use Illuminate\Support\Facades\DB;

use Filament\Actions\Action as ModalAction;
use Illuminate\View\View;
use Carbon\Carbon;

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
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status_rental', 'BERJALAN'))
                ->badge(Rental::query()->where('status_rental', 'BERJALAN')->count()),

            'pembayaran' => Tab::make('Pembayaran')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status_rental', 'SELESAI'))
                ->badge(Rental::query()->where([
                    'status_rental' => 'SELESAI',
                    'status_bayar' => 'PENDING'
                ])->count()),
        ];
    }

    public function table(Table $table): Table
    {

        return $table
            ->defaultSort('tanggal_mulai', 'desc')
            ->columns([
                // All Tabs
                // TextColumn::make('id')
                //     ->label('ID Rental')
                //     ->sortable()
                //     ->searchable(),

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

                TextColumn::make('sisa_biaya')
                    ->label('Sisa Tagihan')
                    ->numeric()
                    ->prefix('Rp ')
                    ->state(function (Rental $record): float {
                        return $record->total_biaya - $record->biaya_dibayar;
                    })
                    ->color(fn($state) => $state > 0 ? 'danger' : 'success')
                    ->visible(fn(): bool => in_array($this->activeTab, ['pembayaran'])),

                TextColumn::make('status_bayar')
                    ->label('Status Bayar')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        StatusBayar::LUNAS => 'success',
                        StatusBayar::PENDING => 'warning',
                    })
                    ->sortable()
                    ->visible(fn(): bool => in_array($this->activeTab, ['pembayaran', 'pesanan'])),
            ])
            ->filters([
                // Tables\Filters\SelectFilter::make('status_rental')
                //     ->options(StatusRental::class),
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
                        Forms\Components\Grid::make(3)
                            ->schema([
                                //Left Side
                                Forms\Components\Section::make('Ringkasan Rental')
                                    ->columnSpan(2)
                                    ->description('Cek kembali informasi rental sebelum menyelesaikan.')
                                    ->columns(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('penyewa.nama')
                                            ->label('Nama Penyewa')
                                            ->default(fn(Rental $record): string => $record->penyewa->nama)
                                            ->disabled(),

                                        Forms\Components\TextInput::make('kendaraan.nopol')
                                            ->label('Kendaraan')
                                            ->default(fn(Rental $record): string => $record->kendaraan->nopol)
                                            ->disabled(),

                                        Forms\Components\DateTimePicker::make('tanggal_mulai')
                                            ->label('Tgl/Wkt Mulai Sewa')
                                            ->default(fn(Rental $record) => $record->tanggal_mulai)
                                            ->seconds(false)
                                            ->displayFormat('d F Y H:i')
                                            ->disabled(),

                                        Forms\Components\TextInput::make('bbm_awal')
                                            ->label('BBM Awal')
                                            ->default(fn(Rental $record) => $record->bbm_awal)
                                            ->suffix('Kotak')
                                            ->disabled(),
                                    ]),

                                Forms\Components\Section::make('Kalkulasi Biaya')
                                    ->columnSpan(1)
                                    ->schema([
                                        Forms\Components\Placeholder::make('Durasi')
                                            ->content(function (Get $get, Rental $record): string {

                                                $startDate = $record->tanggal_mulai;
                                                $endDate = $get('tanggal_selesai');

                                                if (!$startDate || !$endDate) return '0 Hari 0 Jam 0 Menit';

                                                $start = Carbon::parse($startDate);
                                                $end = Carbon::parse($endDate);

                                                if ($start->gte($end)) return 'Tanggal selesai harus setelah tanggal mulai.';

                                                $totalSeconds = $start->diffInSeconds($end);
                                                $days = floor($totalSeconds / 86400);

                                                $remainingSecDays = $totalSeconds % 86400;
                                                $hours = floor($remainingSecDays / 3600);

                                                $remainingSecHours = $remainingSecDays % 3600;
                                                $minutes = floor($remainingSecHours / 60);

                                                if ($hours >= 24) {
                                                    $days++;
                                                    $hours = $hours - 24;
                                                }

                                                return $days . ' Hari ' . $hours . ' Jam ' . $minutes . ' Menit';
                                            }),

                                        Forms\Components\Placeholder::make('biaya_basic')
                                            ->label('Biaya Awal')
                                            ->content(function (Get $get, Rental $record): string {
                                                $kendaraan = $record->kendaraan;
                                                $startDate = $record->tanggal_mulai;
                                                $endDate = $get('tanggal_selesai');

                                                if (!$startDate || !$endDate) return 'Rp 0';

                                                $start = Carbon::parse($startDate);
                                                $end = Carbon::parse($endDate);
                                                if ($start->gte($end)) return 'Rp 0';

                                                //perhitungan hari (ngikut yg atas -> durasi)
                                                $totalSeconds = $start->diffInSeconds($end);
                                                $days = floor($totalSeconds / 86400);

                                                $remainingSeconds = $totalSeconds - ($days * 86400);
                                                $hours = floor($remainingSeconds / 3600);

                                                if ($hours >= 24) {
                                                    $days++;
                                                    $hours = $hours - 24;
                                                };

                                                $remainingSeconds = $remainingSeconds - ($hours * 3600);
                                                $minutes = floor($remainingSeconds / 60);

                                                if ($minutes > 30) {
                                                    $hours++;
                                                    $minutes = $minutes - 30;
                                                };

                                                if ($hours > 12) {
                                                    $days++;
                                                    $hours = 0;
                                                    $minutes = 0.;
                                                };

                                                //perhitungan harga
                                                $harga24 = $kendaraan->harga_24jam ?? 0;
                                                $harga12 = $kendaraan->harga_12jam ?? 0;
                                                $harga6 = $kendaraan->harga_6jam ?? 0;

                                                //perhitungan hari
                                                $dayPrice = $days * $harga24;

                                                //perhitungan sisa jam
                                                $hourPrice = 0;
                                                if ($hours > 6) $hourPrice = $harga12;
                                                elseif ($hours > 0) $hourPrice = $harga6;
                                                else $hourPrice = 0;

                                                $totalSewa = $dayPrice + $hourPrice;
                                                return 'Rp ' . number_format($totalSewa, 0, ',', '.');
                                            }),

                                        Forms\Components\Placeholder::make('biaya_bensin')
                                            ->label('Biaya Bensin')
                                            ->content(function (Get $get, Rental $record): string {

                                                $bbmAwal = $record->bbm_awal;
                                                $bbmKembali = $get('bbm_kembali');
                                                $kendaraan = $record->kendaraan;

                                                if (is_null($bbmAwal) || is_null($bbmKembali)) {
                                                    return 'Rp 0';
                                                }

                                                $bbmUsed = (int)$bbmAwal - (int)$bbmKembali;
                                                $biayaBensin = $bbmUsed * $kendaraan->bbm_per_kotak;

                                                return 'Rp ' . number_format($biayaBensin, 0, ',', '.');
                                            }),
                                    ]),
                            ]),


                        //User Input                        
                        Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('tanggal_selesai')
                                    ->label('Tanggal/Waktu Selesai Sewa')
                                    ->displayFormat('d F Y H:i')
                                    ->seconds(false)
                                    ->default(fn(Rental $record) => $record->tanggal_selesai)
                                    ->live(onBlur: true)
                                    ->required(),

                                Forms\Components\TextInput::make('bbm_kembali')
                                    ->label('BBM Kembali')
                                    ->numeric()
                                    ->required()
                                    ->suffix('Kotak')
                                    ->live(onBlur: true),

                                Forms\Components\TextInput::make('km_kembali')
                                    ->label('KM Kembali')
                                    ->numeric()
                                    ->suffix('km')
                                    ->default(fn(Rental $record) => $record->kilometer),

                                Forms\Components\TextInput::make('total_biaya')
                                    ->label('Total Biaya')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->default(fn(Rental $record) => $record->total_biaya)

                                    ->required(),

                                Forms\Components\Textarea::make('notes')
                                    ->label('Catatan')
                                    ->placeholder('Tulis catatan disini')
                                    ->default(fn(Rental $record): string => $record->notes ?? '')
                                    ->columnSpanFull(),
                            ]),

                    ])
                    ->action(function (Rental $record, array $data) {
                        //Update the rental status to 'SELESAI'
                        $record->update([
                            'tanggal_selesai' => $data['tanggal_selesai'],
                            'total_biaya' => $data['total_biaya'],
                            'notes' => $data['notes'],
                            'bbm_kembali' => $data['bbm_kembali'],
                            'status_rental' => StatusRental::SELESAI,
                        ]);

                        $record->kendaraan->update([
                            'status' => StatusKendaraan::TERSEDIA,
                            'bbm' => $data['bbm_kembali'],
                            'kilometer' => $data['km_kembali'],
                        ]);

                        Notification::make()
                            ->title('Rental Selesai')
                            ->body("Kendaraan {$record->kendaraan->nopol} saat ini tersedia.")
                            ->success()
                            ->send();
                    }),

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
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('penyewa.nama')
                                    ->label('Nama Penyewa')
                                    ->default(fn(Rental $record): string => $record->penyewa->nama)
                                    ->disabled(),

                                Forms\Components\TextInput::make('kendaraan.nopol')
                                    ->label('Kendaraan')
                                    ->default(fn(Rental $record): string => $record->kendaraan->nopol)
                                    ->disabled(),

                                Forms\Components\DateTimePicker::make('tanggal_selesai')
                                    ->label('Tgl/Waktu Motor Kembali')
                                    ->seconds(false)
                                    ->default(fn(Rental $record) => $record->tanggal_selesai)
                                    ->disabled(),

                                Forms\Components\TextInput::make('notes_rental')
                                    ->label('Catatan Rental')
                                    ->disabled()
                                    ->default(fn(Rental $record) => $record->notes),

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
                                    ->label('Tanggal Transaksi')
                                    ->default(now())
                                    ->required(),

                                Forms\Components\TextInput::make('nominal_transaksi')
                                    ->label('Nominal Transaksi')
                                    ->numeric()
                                    ->prefix('Rp')
                                    ->required(),
                            ]),

                        Forms\Components\TextInput::make('notes_bayar')
                            ->label('Catatan Pembayaran')
                            ->placeholder('Tulis catatan disini')
                            ->columnSpanFull(),
                    ])
                    ->action(function (Rental $record, array $data) {
                        $transactionData = [
                            'tanggal_transaksi' => $data['tanggal_transaksi'],
                            'nominal_transaksi' => $data['nominal_transaksi'],
                            'notes' => $data['notes_bayar'],
                        ];

                        $record->transaksi()->create($transactionData);

                        $newBiayaDibayar = $record->biaya_dibayar + $data['nominal_transaksi'];

                        $record->update([
                            'biaya_dibayar' => $newBiayaDibayar
                        ]);

                        if ($newBiayaDibayar >= $record->total_biaya) {
                            $record->update(['status_bayar' => StatusBayar::LUNAS]);
                        }

                        Notification::make()->title('Pembayaran berhasil dicatat')->success()->send();
                    }),
            ]);
    }
}
