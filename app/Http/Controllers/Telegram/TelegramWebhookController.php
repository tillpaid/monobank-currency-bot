<?php

namespace App\Http\Controllers\Telegram;

use App\Helpers\TelegramBotHelper;
use App\Http\Controllers\Controller;
use App\Services\Interfaces\TelegramServiceInterface;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramWebhookController extends Controller
{
    private $service;

    public function __construct(TelegramServiceInterface $service)
    {
        $this->service = $service;
    }

    public function catchWebhook()
    {
        try {
            $telegram = TelegramBotHelper::getBot();
            $telegram->useGetUpdatesWithoutDatabase();

            $serverResponse = $telegram->handle();

            if ($serverResponse) {
                $this->service->processWebhook(request()->all());
            }
        } catch (TelegramException $exception) {
            return [
                'success' => false,
                'error'   => $exception->getMessage()
            ];
        }
    }
}
