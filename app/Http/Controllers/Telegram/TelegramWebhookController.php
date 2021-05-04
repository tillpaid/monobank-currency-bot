<?php

namespace App\Http\Controllers\Telegram;

use App\Helpers\TelegramBotHelper;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\TelegramServiceInterface;

class TelegramWebhookController extends Controller
{
    private $service;

    public function __construct(TelegramServiceInterface $service)
    {
        $this->service = $service;
    }

    public function catchWebhook()
    {
        $telegram = TelegramBotHelper::getBot();
        $telegram->useGetUpdatesWithoutDatabase();

        $serverResponse = $telegram->handle();

        if ($serverResponse) {
            $this->service->processWebhook(request()->all());
        }
    }
}
