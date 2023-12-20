<?php

namespace App\Console\Commands\Telegram;

use App\Services\Telegram\TelegramBotService;
use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class SetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook';
    protected $description = 'Telegram set webhook';

    private TelegramService $telegramService;
    private TelegramBotService $telegramBotService;

    public function handle(TelegramService $telegramService, TelegramBotService $telegramBotService): void
    {
        $this->init($telegramService, $telegramBotService);

        $url = config('telegram.botWebhookUrl');
        $telegram = $this->telegramBotService->getBot();

        try {
            $result = $telegram->setWebhook($url);

            if ($result->isOk()) {
                $this->output->writeln($result->getDescription());
                $this->telegramService->sendMessageAboutChangeEnv();
            }
        } catch (TelegramException $exception) {
            $this->output->writeln($exception->getMessage());
        }
    }

    private function init(TelegramService $telegramService, TelegramBotService $telegramBotService): void
    {
        $this->telegramService = $telegramService;
        $this->telegramBotService = $telegramBotService;
    }
}
