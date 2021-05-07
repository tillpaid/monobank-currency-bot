<?php

namespace App\Console\Commands\Telegram;

use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Services\Interfaces\Telegram\TelegramServiceInterface;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class SetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook';

    private $telegramService;
    private $telegramBotService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telegram set webhook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TelegramServiceInterface $telegramService, TelegramBotServiceInterface $telegramBotService)
    {
        parent::__construct();

        $this->telegramService = $telegramService;
        $this->telegramBotService = $telegramBotService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
