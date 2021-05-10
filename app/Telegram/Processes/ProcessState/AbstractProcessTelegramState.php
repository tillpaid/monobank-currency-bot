<?php

namespace App\Telegram\Processes\ProcessState;

use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractProcessTelegramState implements ProcessTelegramStateInterface
{
    protected $telegramUserService;
    protected $currencyRateService;
    protected $currencyAccountService;

    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        CurrencyRateServiceInterface $currencyRateService,
        CurrencyAccountServiceInterface $currencyAccountService
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->currencyRateService = $currencyRateService;
        $this->currencyAccountService = $currencyAccountService;
    }

    final protected function updateUserState(Model $user, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($user, $state, $stateAdditional);
    }

    final protected function buildBuyConfirmMessage(Model $user): string
    {
        $currency = $user->state_additional['buy-currency'] ?? 'usd';
        $currencyRate = $this->currencyRateService->getLatestCurrencyRate($currency)->sell;

        $this->telegramUserService->updateStateAdditional($user, ['buy-currency-rate' => $currencyRate]);

        $sumUah = $user->state_additional['buy-currency-sum'] ?? 0;
        $uahToCurrency = round($sumUah / $currencyRate, 2);

        return __('telegram.buyMessage', ['currencyRate' => $currencyRate, 'uahToCurrency' => $uahToCurrency, 'currency' => $currency]);
    }
}
