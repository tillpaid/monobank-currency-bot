<?php

declare(strict_types=1);

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class UnsetWebhook extends Command
{
    protected $signature = 'telegram:unset-webhook';

    public function handle(TelegramBotService $telegramBotService): void
    {
        try {
            $response = $telegramBotService->getBot()->deleteWebhook();

            if ($response->isOk()) {
                $this->output->writeln($response->getDescription());
            }
        } catch (TelegramException $exception) {
            $this->output->writeln($exception->getMessage());
        }
    }
}
