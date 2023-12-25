<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState;

use App\Models\TelegramUser;
use App\Services\Models\CurrencyAccountService;
use App\Services\Models\CurrencyRateService;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;

abstract class AbstractProcessTelegramState
{
    protected TelegramUserService $telegramUserService;
    protected CurrencyRateService $currencyRateService;
    protected CurrencyAccountService $currencyAccountService;
    protected TelegramBotService $telegramBotService;

    public function __construct(
        TelegramUserService $telegramUserService,
        CurrencyRateService $currencyRateService,
        CurrencyAccountService $currencyAccountService,
        TelegramBotService $telegramBotService
    ) {
        $this->telegramUserService = $telegramUserService;
        $this->currencyRateService = $currencyRateService;
        $this->currencyAccountService = $currencyAccountService;
        $this->telegramBotService = $telegramBotService;
    }

    abstract public function process(TelegramUser $telegramUser, string $messageText): string;

    final protected function updateUserState(TelegramUser $telegramUser, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($telegramUser, $state, $stateAdditional);
    }

    final protected function buildBuyConfirmMessage(TelegramUser $telegramUser, ?float $currencyRate = null): string
    {
        $currency = $telegramUser->state_additional['buy-currency']
            ? mb_strtoupper($telegramUser->state_additional['buy-currency'])
            : 'USD';
        $currencyLower = mb_strtolower($currency);

        if (null === $currencyRate) {
            $currencyRate = $this->currencyRateService->getLatestCurrencyRate($currencyLower)->sell;
        }

        $this->telegramUserService->updateStateAdditional($telegramUser, ['buy-currency-rate' => $currencyRate]);

        $sumUah = $telegramUser->state_additional['buy-currency-sum'] ?? 0;
        $sumUahFormat = $this->telegramBotService->format($sumUah, 5);
        $uahToCurrency = $this->telegramBotService->format($sumUah / $currencyRate, 2);

        return __('telegram.buyMessage', compact('currencyRate', 'uahToCurrency', 'currency', 'sumUahFormat'));
    }
}
