<?php

namespace App\Telegram\Processes\ProcessState;

use App\Services\Models\CurrencyAccountService;
use App\Services\Models\CurrencyRateService;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;
use Illuminate\Database\Eloquent\Model;

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

    abstract public function process(Model $user, string $messageText): string;

    final protected function updateUserState(Model $user, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($user, $state, $stateAdditional);
    }

    final protected function buildBuyConfirmMessage(Model $user, ?float $currencyRate = null): string
    {
        $currency = $user->state_additional['buy-currency']
            ? mb_strtoupper($user->state_additional['buy-currency'])
            : 'USD';

        if (is_null($currencyRate)) {
            $currencyRate = $this->currencyRateService->getLatestCurrencyRate($currency)->sell;
        }

        $this->telegramUserService->updateStateAdditional($user, ['buy-currency-rate' => $currencyRate]);

        $sumUah = $user->state_additional['buy-currency-sum'] ?? 0;
        $sumUahFormat = $this->telegramBotService->format($sumUah, 5);
        $uahToCurrency = $this->telegramBotService->format($sumUah / $currencyRate, 2);

        return __('telegram.buyMessage', compact('currencyRate', 'uahToCurrency', 'currency', 'sumUahFormat'));
    }
}
