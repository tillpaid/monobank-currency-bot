<?php

namespace App\Telegram\Processes\ProcessState;

use App\Services\Interfaces\Models\CurrencyAccountServiceInterface;
use App\Services\Interfaces\Models\CurrencyRateServiceInterface;
use App\Services\Interfaces\Models\TelegramUserServiceInterface;
use App\Services\Interfaces\Telegram\TelegramBotServiceInterface;
use App\Telegram\Processes\ProcessState\Interfaces\ProcessTelegramStateInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractProcessTelegramState implements ProcessTelegramStateInterface
{
    protected $telegramUserService;
    protected $currencyRateService;
    protected $currencyAccountService;
    protected $telegramBotService;

    public function __construct(
        TelegramUserServiceInterface $telegramUserService,
        CurrencyRateServiceInterface $currencyRateService,
        CurrencyAccountServiceInterface $currencyAccountService,
        TelegramBotServiceInterface $telegramBotService
    )
    {
        $this->telegramUserService = $telegramUserService;
        $this->currencyRateService = $currencyRateService;
        $this->currencyAccountService = $currencyAccountService;
        $this->telegramBotService = $telegramBotService;
    }

    final protected function updateUserState(Model $user, ?string $state, ?array $stateAdditional = null): bool
    {
        return $this->telegramUserService->updateState($user, $state, $stateAdditional);
    }

    final protected function buildBuyConfirmMessage(Model $user, ?float $currencyRate = null): string
    {
        $currency = $user->state_additional['buy-currency'] ?? 'usd';

        if (is_null($currencyRate)) {
            $currencyRate = $this->currencyRateService->getLatestCurrencyRate($currency)->sell;
        }

        $this->telegramUserService->updateStateAdditional($user, ['buy-currency-rate' => $currencyRate]);

        $sumUah = $user->state_additional['buy-currency-sum'] ?? 0;
        $uahToCurrency = number_format($sumUah / $currencyRate, 2, '.', ' ');

        return __('telegram.buyMessage', ['currencyRate' => $currencyRate, 'uahToCurrency' => $uahToCurrency, 'currency' => $currency]);
    }
}
