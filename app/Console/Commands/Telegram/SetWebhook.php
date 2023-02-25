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

    public function __construct(
        TelegramService $telegramService,
        TelegramBotService $telegramBotService
    ) {
        parent::__construct();

        $this->telegramService = $telegramService;
        $this->telegramBotService = $telegramBotService;
    }

    public function handle(): void
    {
        $url = config('telegram.botWebhookUrl');
        $telegram = $this->telegramBotService->getBot();

        try {
            $result = $telegram->setWebhook($url);

            if ($result->isOk()) {
                echo $result->getDescription() . PHP_EOL;
                $this->telegramService->sendMessageAboutChangeEnv();
            }
        } catch (TelegramException $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }
}
