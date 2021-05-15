<?php

namespace App\Console\Commands\Monobank;

use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Monobank\MonobankCurrencyServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use Illuminate\Console\Command;

class MonobankUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monobank:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monobank update';

    private $monobankCurrencyService;
    private $telegramUserService;
    private $telegramBotService;

    /**
     * Create a new command instance.
     * @param MonobankCurrencyServiceInterface$monobankCurrencyService
     * @param TelegramUserServiceInterface $telegramUserService,
     * @param TelegramBotServiceInterface $telegramBotService
     * @return void
     */
    public function __construct(
        MonobankCurrencyServiceInterface $monobankCurrencyService,
        TelegramUserServiceInterface $telegramUserService,
        TelegramBotServiceInterface $telegramBotService
    )
    {
        parent::__construct();

        $this->monobankCurrencyService = $monobankCurrencyService;
        $this->telegramUserService = $telegramUserService;
        $this->telegramBotService = $telegramBotService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->monobankCurrencyService->updateCurrencyRates()) {
            $users = $this->telegramUserService->all();

            foreach ($users as $user) {
                $report = $this->telegramBotService->buildUserReport($user->id);
                $this->telegramBotService->sendMessage($user->chat_id, $report);
            }
        }
    }
}
