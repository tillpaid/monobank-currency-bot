<?php

namespace App\Console\Commands\Monobank;

use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Monobank\MonobankCurrencyServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use Illuminate\Console\Command;

class MonobankUpdate extends Command
{
    protected $signature = 'monobank:update';
    protected $description = 'Monobank update';

    private MonobankCurrencyServiceInterface $monobankCurrencyService;
    private TelegramUserServiceInterface $telegramUserService;
    private TelegramBotServiceInterface $telegramBotService;

    public function __construct(
        MonobankCurrencyServiceInterface $monobankCurrencyService,
        TelegramUserServiceInterface $telegramUserService,
        TelegramBotServiceInterface $telegramBotService
    ) {
        parent::__construct();

        $this->monobankCurrencyService = $monobankCurrencyService;
        $this->telegramUserService = $telegramUserService;
        $this->telegramBotService = $telegramBotService;
    }

    public function handle(): void
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
