<?php

namespace App\Providers;

use App\Interfaces\BookingRepositoryInterface;
use App\Repositories\BookingRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(BookingRepositoryInterface::class, BookingRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
