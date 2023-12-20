<?php

namespace App\Console\Commands\Monobank;

use App\Services\Models\TelegramUserService;
use App\Services\Monobank\MonobankCurrencyService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;

class MonobankUpdate extends Command
{
    protected $signature = 'monobank:update';
    protected $description = 'Monobank update';

    private MonobankCurrencyService $monobankCurrencyService;
    private TelegramUserService $telegramUserService;
    private TelegramBotService $telegramBotService;

    public function handle(
        MonobankCurrencyService $monobankCurrencyService,
        TelegramUserService $telegramUserService,
        TelegramBotService $telegramBotService
    ): void {
        $this->init($monobankCurrencyService, $telegramUserService, $telegramBotService);

        if ($this->monobankCurrencyService->updateCurrencyRates()) {
            $users = $this->telegramUserService->all();

            foreach ($users as $user) {
                $report = $this->telegramBotService->buildUserReport($user->id);
                $this->telegramBotService->sendMessage($user->chat_id, $report);
            }
        }
    }

    private function init(
        MonobankCurrencyService $monobankCurrencyService,
        TelegramUserService $telegramUserService,
        TelegramBotService $telegramBotService
    ): void {
        $this->monobankCurrencyService = $monobankCurrencyService;
        $this->telegramUserService = $telegramUserService;
        $this->telegramBotService = $telegramBotService;
    }
}
