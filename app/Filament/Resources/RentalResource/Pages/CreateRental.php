<?php

namespace App\Filament\Resources\RentalResource\Pages;

use App\Filament\Resources\RentalResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateRental extends CreateRecord
{
    protected static string $resource = RentalResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data) {

            $rental = static::getModel()::create($data);

            $rental->kendaraan()->update([
                'bbm' => $data['bbm_awal']
            ]);

            return $rental;
        });
    }
}
