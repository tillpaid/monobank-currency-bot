<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramWebhookController extends Controller
{
    private TelegramService $telegramService;
    private TelegramBotService $telegramBotService;

    public function __construct(
        TelegramService $telegramService,
        TelegramBotService $telegramBotService
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
                'error' => $exception->getMessage(),
            ];
        }

        if ($serverResponse) {
            $this->telegramService->processWebhook(request()->all());
        }

        return ['success' => true];
    }
}
