<?php

namespace App\Observers;

use App\Models\Kendaraan;
use App\Models\Rental;
use Illuminate\Support\Facades\Log;

class RentalObserver
{
    /**
     * Handle the Rental "created" event.
     */
    public function created(Rental $rental): void
    {
        $kendaraan = Kendaraan::find($rental->kendaraan_id);

        if ($kendaraan) {
            $kendaraan->status = 'DISEWA';
            $kendaraan->save();
        } else {
            Log::error("RentalObserver: Kendaraan with ID {$rental->kendaraan_id} not found.");
        }
    }

    /**
     * Handle the Rental "updated" event.
     */
    public function updated(Rental $rental): void
    {
        if ($rental->isDirty('status') && $rental->status === 'SELESAI') {

            $kendaraan = Kendaraan::find($rental->kendaraan_id);

            if ($kendaraan) {
                $kendaraan->status = 'TERSEDIA';
                $kendaraan->save();
            } else {
                Log::error("RentalObserver: Kendaraan with ID {$rental->kendaraan_id} not found.");
            }
        }
    }

    /**
     * Handle the Rental "deleted" event.
     */
    public function deleted(Rental $rental): void
    {
        //
    }

    /**
     * Handle the Rental "restored" event.
     */
    public function restored(Rental $rental): void
    {
        //
    }

    /**
     * Handle the Rental "force deleted" event.
     */
    public function forceDeleted(Rental $rental): void
    {
        //
    }
}
