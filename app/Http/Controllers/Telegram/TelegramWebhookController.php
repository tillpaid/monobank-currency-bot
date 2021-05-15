<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Services\Interfaces\Telegram\TelegramServiceInterface;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramWebhookController extends Controller
{
    private $telegramService;
    private $telegramBotService;

    /**
     * TelegramWebhookController constructor.
     * @param TelegramServiceInterface $telegramService
     * @param TelegramBotServiceInterface $telegramBotService
     */
    public function __construct(TelegramServiceInterface $telegramService, TelegramBotServiceInterface $telegramBotService)
    {
        $this->telegramService = $telegramService;
        $this->telegramBotService = $telegramBotService;
    }

    /**
     * @return array
     */
    public function catchWebhook(): array
    {
        try {
            $telegram = $this->telegramBotService->getBot();
            $telegram->useGetUpdatesWithoutDatabase();

            $serverResponse = $telegram->handle();

            if ($serverResponse) {
                $this->telegramService->processWebhook(request()->all());
            }

            return ['success' => true];
        } catch (TelegramException $exception) {
            return [
                'success' => false,
                'error'   => $exception->getMessage()
            ];
        }
    }
}
