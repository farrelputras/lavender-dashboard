<?php

namespace App\Filament\Resources\KendaraanResource\Pages;

use App\Filament\Resources\KendaraanResource;
use App\Filament\Resources\KendaraanResource\Widgets\KendaraanStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListKendaraans extends ListRecords
{
    protected static string $resource = KendaraanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Kendaraan Baru')
                ->icon('heroicon-o-plus'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            KendaraanStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'kendaraan' => Tab::make('Semua Kendaraan'),

            'tersedia' => Tab::make('Tersedia')->query(fn($query) => $query->where('status', 'TERSEDIA')),
            'disewa' => Tab::make('Disewa')->query(fn($query) => $query->where('status', 'DISEWA')),
            'perbaikan' => Tab::make('Servis')->query(fn($query) => $query->where('status', 'PERBAIKAN')),
        ];
    }
}
