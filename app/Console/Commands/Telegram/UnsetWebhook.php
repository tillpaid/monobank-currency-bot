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

    public function __construct(TelegramBotService $telegramBotService)
    {
        parent::__construct();

        $this->telegramBotService = $telegramBotService;
    }

    public function handle(): void
    {
        $telegram = $this->telegramBotService->getBot();

        try {
            $result = $telegram->deleteWebhook();

            if ($result->isOk()) {
                echo $result->getDescription() . PHP_EOL;
            }
        } catch (TelegramException $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }
}
