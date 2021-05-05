<?php

namespace App\Console\Commands\Telegram;

use App\Services\Interfaces\TelegramBotServiceInterface;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class UnsetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:unset-webhook';

    private $telegramBotService;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Telegram unset webhook';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(TelegramBotServiceInterface $telegramBotService)
    {
        parent::__construct();

        $this->telegramBotService = $telegramBotService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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
