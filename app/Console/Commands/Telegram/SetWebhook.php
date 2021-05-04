<?php

namespace App\Console\Commands\Telegram;

use App\Helpers\TelegramBotHelper;
use App\Services\Interfaces\TelegramServiceInterface;
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
    public function __construct(TelegramServiceInterface $telegramService)
    {
        parent::__construct();

        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = config('telegram.botWebhookUrl');
        $telegram = TelegramBotHelper::getBot();

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
