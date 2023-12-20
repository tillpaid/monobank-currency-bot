<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class UnsetWebhook extends Command
{
    protected $signature = 'telegram:unset-webhook';
    protected $description = 'Telegram unset webhook';

    private TelegramBotService $telegramBotService;

    public function handle(TelegramBotService $telegramBotService): void
    {
        $this->init($telegramBotService);

        $telegram = $this->telegramBotService->getBot();

        try {
            $result = $telegram->deleteWebhook();

            if ($result->isOk()) {
                $this->output->writeln($result->getDescription());
            }
        } catch (TelegramException $exception) {
            $this->output->writeln($exception->getMessage());
        }
    }

    private function init(TelegramBotService $telegramBotService): void
    {
        $this->telegramBotService = $telegramBotService;
    }
}
