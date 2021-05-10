<?php

namespace App\Telegram\Processes\ProcessState;

use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Models\CurrencyRateService;
use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractProcessTelegramState implements ProcessTelegramStateInterface
{
    protected $telegramUserService;
    protected $currencyRateService;

    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        CurrencyRateService $currencyRateService
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->currencyRateService = $currencyRateService;
    }

    final protected function updateUserState(Model $user, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($user, $state, $stateAdditional);
    }

    final protected function buildBuyConfirmMessage(Model $user): string
    {
        $currency = $user->state_additional['buy-currency'] ?? 'usd';
        $currencyRate = $this->currencyRateService->getLatestCurrencyRate($currency)->sell;

        $sumUah = $user->state_additional['buy-currency-sum'] ?? 0;
        $uahToCurrency = round($sumUah / $currencyRate, 2);

        return __('telegram.buyMessage', ['currencyRate' => $currencyRate, 'uahToCurrency' => $uahToCurrency, 'currency' => $currency]);
    }
}
