<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class SetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';

    public function handle(TelegramService $telegramService, TelegramBotService $telegramBotService): void
    {
        try {
            $response = $telegramBotService->getBot()->setWebhook(config('telegram.botWebhookUrl'));

            if ($response->isOk()) {
                $this->output->writeln($response->getDescription());
                $telegramService->sendMessageAboutChangeEnv();
            }
        } catch (TelegramException $exception) {
            $this->output->writeln($exception->getMessage());
        }
    }
}
