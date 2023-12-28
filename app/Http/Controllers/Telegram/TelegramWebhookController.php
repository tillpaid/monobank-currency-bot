<?php

declare(strict_types=1);

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Illuminate\Support\Facades\Log;
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
        try {
            $serverResponse = $this->telegramBotService
                ->getBot()
                ->useGetUpdatesWithoutDatabase()
                ->handle()
            ;
        } catch (TelegramException $exception) {
            Log::error('Telegram webhook error', [$exception->getMessage()]);

            return ['success' => false, 'error' => 'Internal server error'];
        }

        if ($serverResponse) {
            $this->telegramService->processWebhook(request()->all());
        }

        return ['success' => true];
    }
}
