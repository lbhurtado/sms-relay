<?php

namespace App\Providers;

use App\{Contact, Ticket};
use Illuminate\Support\ServiceProvider;
use App\Observers\{ContactObserver, TicketObserver};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Ticket::observe(TicketObserver::class);
        Contact::observe(ContactObserver::class);
    }
}
