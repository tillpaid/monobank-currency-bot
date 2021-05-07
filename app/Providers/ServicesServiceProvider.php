<?php

namespace App\Providers;

use App\Services\CurrencyRateService;
use App\Services\Interfaces\CurrencyRateServiceInterface;
use App\Services\Interfaces\MonobankCurrencyServiceInterface;
use App\Services\Interfaces\TelegramBotServiceInterface;
use App\Services\Interfaces\TelegramServiceInterface;
use App\Services\MonobankCurrencyService;
use App\Services\TelegramBotService;
use App\Services\TelegramService;
use Illuminate\Support\ServiceProvider;

class ServicesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TelegramServiceInterface::class, TelegramService::class);
        $this->app->bind(TelegramBotServiceInterface::class, TelegramBotService::class);
        $this->app->bind(MonobankCurrencyServiceInterface::class, MonobankCurrencyService::class);
        $this->app->bind(CurrencyRateServiceInterface::class, CurrencyRateService::class);
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
