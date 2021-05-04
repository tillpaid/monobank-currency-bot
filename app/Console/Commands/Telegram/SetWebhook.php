<?php

namespace App\Console\Commands\Telegram;

use App\Helpers\TelegramBotHelper;
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
    public function __construct()
    {
        parent::__construct();
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
            }
        } catch (TelegramException $exception) {
            echo $exception->getMessage() . PHP_EOL;
        }
    }
}
