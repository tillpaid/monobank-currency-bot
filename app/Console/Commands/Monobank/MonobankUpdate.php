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

    public function __construct(
        MonobankCurrencyService $monobankCurrencyService,
        TelegramUserService $telegramUserService,
        TelegramBotService $telegramBotService
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
