<?php

namespace App\Filament\Resources\KendaraanResource\Widgets;

use App\Enums\StatusKendaraan;
use App\Models\Kendaraan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KendaraanStats extends BaseWidget
{
    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        return [
            Stat::make('Kendaraan Tersedia', Kendaraan::where('status', StatusKendaraan::TERSEDIA)->count()),
            Stat::make('Kendaraan Disewa', Kendaraan::where('status', StatusKendaraan::DISEWA)->count()),
            Stat::make('Kendaraan Diservis', Kendaraan::where('status', StatusKendaraan::PERBAIKAN)->count()),
        ];
    }
}
