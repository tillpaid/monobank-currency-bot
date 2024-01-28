<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TelegramUser;
use App\Repositories\CurrencyRateRepository;
use App\Services\Models\TelegramUserService;
use App\Services\Telegram\TelegramBotService;

readonly class BuyCurrencyService
{
    public function __construct(
        private CurrencyRateRepository $currencyRateRepository,
        private TelegramUserService $telegramUserService,
        private TelegramBotService $telegramBotService,
    ) {}

    public function prepareBuyCurrencyAndGetConfirmMessage(TelegramUser $telegramUser, ?float $currencyRate = null): string
    {
        $buyCurrencyValue = $telegramUser->getStateAdditionalValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY);

        $currency = mb_strtoupper($buyCurrencyValue ?? 'USD');
        $currencyLower = mb_strtolower($currency);

        $currencyRate ??= $this->currencyRateRepository->getLatestCurrencyRate($currencyLower)->getSell();

        $this->telegramUserService->updateStateAdditional(
            $telegramUser,
            [TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_RATE => $currencyRate]
        );

        $sumUah = $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_BUY_CURRENCY_SUM) ?? 0;
        $sumUahFormat = $this->telegramBotService->format($sumUah, 5);
        $uahToCurrency = $this->telegramBotService->format($sumUah / $currencyRate);

        return __(
            'telegram.buyMessage',
            compact('currencyRate', 'uahToCurrency', 'currency', 'sumUahFormat')
        );
    }
}
