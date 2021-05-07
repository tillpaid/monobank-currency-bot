<?php

namespace App\Providers;

use App\Repositories\CurrencyRateRepository;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(CurrencyRateRepositoryInterface::class, CurrencyRateRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
