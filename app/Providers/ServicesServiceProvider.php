<?php

namespace App\Providers;

use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use App\Services\Interfaces\Models\TelegramUserSendRateServiceInterface;
use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Monobank\MonobankCurrencyServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Services\Interfaces\Telegram\TelegramServiceInterface;
use App\Services\Models\CurrencyAccountService;
use App\Services\Models\CurrencyRateService;
use App\Services\Models\TelegramUserSendRateService;
use App\Services\Models\TelegramUserService;
use App\Services\Monobank\MonobankCurrencyService;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
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
        $binds = [
            TelegramServiceInterface::class             => TelegramService::class,
            TelegramBotServiceInterface::class          => TelegramBotService::class,
            MonobankCurrencyServiceInterface::class     => MonobankCurrencyService::class,
            CurrencyRateServiceInterface::class         => CurrencyRateService::class,
            CurrencyAccountServiceInterface::class      => CurrencyAccountService::class,
            TelegramUserServiceInterface::class         => TelegramUserService::class,
            TelegramUserSendRateServiceInterface::class => TelegramUserSendRateService::class,
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
