<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Models\Rental;
use App\Observers\RentalObserver;

class EventServiceProvider extends ServiceProvider
{
    protected $observers = [
        Rental::class => [RentalObserver::class],
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
