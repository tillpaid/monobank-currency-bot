<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TelegramUser;

class TelegramUserAdditionalStateResolver
{
    public function getCurrency(TelegramUser $telegramUser): string
    {
        // TODO: A default value is don't needed here. Update the code to remove it. And the same for the other states in all places.
        $currency = $telegramUser->getStateAdditionalValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY);

        return $currency ?? 'usd';
    }

    public function getCurrencySum(TelegramUser $telegramUser): float
    {
        // TODO: A default value is don't needed here. Update the code to remove it. And the same for the other states in all places.
        $value = $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM);

        return $value ?? 0;
    }

    public function getCurrencySumAll(TelegramUser $telegramUser): float
    {
        // TODO: A default value is don't needed here. Update the code to remove it. And the same for the other states in all places.
        $value = $telegramUser->getStateAdditionalFloatValue(TelegramUser::STATE_ADDITIONAL_SELL_CURRENCY_SUM_ALL);

        return $value ?? 0;
    }

    public function getCurrencySumAllFormatted(TelegramUser $telegramUser): string
    {
        $currencySumAll = $this->getCurrencySumAll($telegramUser);

        return number_format($currencySumAll, 5, '.', ' ');
    }
}
