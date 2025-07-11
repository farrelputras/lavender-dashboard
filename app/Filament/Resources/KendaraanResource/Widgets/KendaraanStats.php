<?php

namespace App\Filament\Resources\KendaraanResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KendaraanStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Kendaraan Tersedia', '5'),
            Stat::make('Kendaraan Disewa', '5'),
            Stat::make('Kendaraan Diservis', '2'),
        ];
    }
}
