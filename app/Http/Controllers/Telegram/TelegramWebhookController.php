<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Services\Interfaces\Telegram\TelegramServiceInterface;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramWebhookController extends Controller
{
    private TelegramServiceInterface $telegramService;
    private TelegramBotServiceInterface $telegramBotService;

    public function __construct(
        TelegramServiceInterface $telegramService,
        TelegramBotServiceInterface $telegramBotService
    ) {
        $this->telegramService = $telegramService;
        $this->telegramBotService = $telegramBotService;
    }

    public function catchWebhook(): array
    {
        $telegram = $this->telegramBotService->getBot();
        $telegram->useGetUpdatesWithoutDatabase();

        try {
            $serverResponse = $telegram->handle();
        } catch (TelegramException $exception) {
            return [
                'success' => false,
                'error'   => $exception->getMessage()
            ];
        }

        if ($serverResponse) {
            $this->telegramService->processWebhook(request()->all());
        }

        return ['success' => true];
    }
}
