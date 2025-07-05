<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalResource\Pages;
use App\Filament\Resources\RentalResource\RelationManagers;
use App\Models\Rental;
use Dom\Text;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;

class RentalResource extends Resource
{
    protected static ?string $model = Rental::class;

    protected static ?string $navigationIcon = 'heroicon-s-list-bullet';

    public static function getModel(): string
    {
        return \App\Models\Rental::class;
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

                Forms\Components\DateTimePicker::make('tanggal_mulai')
                    ->label('Mulai Sewa')
                    ->required(),

                Forms\Components\DateTimePicker::make('tanggal_selesai')
                    ->label('Rencana Selesai Sewa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID Sewa')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('penyewa.nama')
                    ->label('Penyewa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kendaraan.model')
                    ->label('Kendraan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kendaraan.nopol')
                    ->label('Plat Nomor')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('tanggal_mulai')
                    ->label('Mulai Sewa')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match ($state) {
                        'BERJALAN' => 'warning',
                        'SELESAI' => 'success',
                    })
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('selesaikan_rental')
                    ->label('SELESAIKAN')
                    ->visible(fn(Rental $record): bool => $record->status === 'BERJALAN')
                    ->form([
                        Forms\Components\Placeholder::make('rental_summary')
                            ->label('Ringkasan')
                            ->content(function (Rental $record): HtmlString {
                                // Ambil informasi penyewa dan kendaraan
                                $penyewaInfo = "Penyewa: " . $record->penyewa->nama;
                                $kendaraanInfo = "Kendaraan: " . $record->kendaraan->nopol;

                                // Atur locale ke Bahasa Indonesia dan format tanggal/waktu
                                $tanggalMulai = $record->tanggal_mulai->locale('id_ID');
                                $tanggalSewa = "Tanggal Sewa: " . $tanggalMulai->translatedFormat('j F Y');
                                $waktuSewa = "Waktu Mulai Sewa: " . $tanggalMulai->format('H:i');

                                // Gabungkan semua informasi menjadi satu string dengan tag <br> untuk baris baru
                                $fullstring = $penyewaInfo . "<br>" . $kendaraanInfo . "<br><br>" . $tanggalSewa . "<br>" . $waktuSewa;

                                return new HtmlString($fullstring);
                            }),

                        //User Input                        
                        Forms\Components\DateTimePicker::make('tanggal_selesai')
                            ->label('Tanggal Selesai Sewa')
                            ->required(),

                        Forms\Components\TextInput::make('total_biaya')
                            ->label('Total Biaya')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->placeholder('Tulis catatan disini')
                            ->rows(3),
                    ])
                    ->action(function (Rental $record, array $data) {
                        //Update the rental status to 'SELESAI'
                        $record->update([
                            'tanggal_selesai' => $data['tanggal_selesai'],
                            'total_biaya' => $data['total_biaya'],
                            'notes' => $data['notes'],
                            'status' => 'SELESAI',
                        ]);

                        Notification::make()
                            ->title('Rental Selesai')
                            ->body("Kendaraan {$record->kendaraan->nopol} saat ini tersedia.")
                            ->success()
                            ->send();
                    })
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
