<?php

namespace App\Filament\Pages;

use App\Filament\Resources\KendaraanResource\Widgets\KendaraanStats;
use App\Models\Kendaraan;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getWidgets(): array
    {
        return [
            KendaraanStats::class,
        ];
    }
}
