<?php

declare(strict_types=1);

namespace App\Telegram\Processes\ProcessState;

use App\Models\TelegramUser;
use App\Repositories\CurrencyRateRepository;
use App\Services\Models\CurrencyAccountService;
use App\Services\Models\CurrencyRateService;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;

// TODO: Convert this class to interface
abstract class AbstractProcessTelegramState
{
    // TODO: Move props to child classes
    public function __construct(
        protected TelegramUserService $telegramUserService,
        protected CurrencyRateService $currencyRateService,
        protected CurrencyAccountService $currencyAccountService,
        protected CurrencyRateRepository $currencyRateRepository,
        protected TelegramBotService $telegramBotService,
    ) {}

    abstract public function getState(): ?string;

    abstract public function process(TelegramUser $telegramUser, string $messageText): string;

    final protected function buildBuyConfirmMessage(TelegramUser $telegramUser, ?float $currencyRate = null): string
    {
        $buyCurrency = $telegramUser->getStateAdditionalValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY);

        $currency = $buyCurrency ? mb_strtoupper($buyCurrency) : 'USD';
        $currencyLower = mb_strtolower($currency);

        if (null === $currencyRate) {
            $currencyRate = $this->currencyRateRepository->getLatestCurrencyRate($currencyLower)->getSell();
        }

        $this->telegramUserService->updateStateAdditional(
            $telegramUser,
            [TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_RATE => $currencyRate]
        );

        $sumUah = $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_SUM) ?? 0;

        $sumUahFormat = $this->telegramBotService->format($sumUah, 5);
        $uahToCurrency = $this->telegramBotService->format($sumUah / $currencyRate);

        return __('telegram.buyMessage', compact('currencyRate', 'uahToCurrency', 'currency', 'sumUahFormat'));
    }
}
