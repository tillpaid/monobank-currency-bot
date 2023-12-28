<?php

declare(strict_types=1);

namespace App\Console\Commands\Monobank;

use App\Repositories\TelegramUserRepository;
use App\Services\Monobank\MonobankCurrencyService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Console\Command;
use Longman\TelegramBot\Exception\TelegramException;

class MonobankUpdate extends Command
{
    protected $signature = 'monobank:update';
    protected $description = 'Update currency rates';

    /**
     * @throws TelegramException
     */
    public function handle(
        MonobankCurrencyService $monobankCurrencyService,
        TelegramUserRepository $telegramUserRepository,
        TelegramBotService $telegramBotService
    ): void {
        $currencyRatesUpdated = $monobankCurrencyService->updateCurrencyRates();
        if (!$currencyRatesUpdated) {
            return;
        }

        $users = $telegramUserRepository->findAll();
        foreach ($users as $user) {
            $report = $telegramBotService->buildUserReport($user->getId());
            $telegramBotService->sendMessage($user->getChatId(), $report);
        }
    }
}
