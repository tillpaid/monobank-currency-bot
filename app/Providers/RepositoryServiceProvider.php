<?php

namespace App\Providers;

use App\Repositories\CurrencyAccountRepository;
use App\Repositories\CurrencyRateRepository;
use App\Repositories\Interfaces\CurrencyAccountRepositoryInterface;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use App\Repositories\Interfaces\TelegramUserRepositoryInterface;
use App\Repositories\TelegramUserRepository;
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
        $binds = [
            CurrencyAccountRepositoryInterface::class => CurrencyAccountRepository::class,
            CurrencyRateRepositoryInterface::class    => CurrencyRateRepository::class,
            TelegramUserRepositoryInterface::class    => TelegramUserRepository::class
        ];

        foreach ($binds as $abstract => $concrete) {
            $this->app->bind($abstract, $concrete);
        }
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
