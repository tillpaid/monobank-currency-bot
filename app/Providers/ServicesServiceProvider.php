<?php

namespace App\Providers;

use App\Services\Interfaces\TelegramBotServiceInterface;
use App\Services\Interfaces\TelegramServiceInterface;
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
