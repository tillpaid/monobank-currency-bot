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
    public function __construct(
        private readonly TelegramService $telegramService,
        private readonly TelegramBotService $telegramBotService,
    ) {}

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
