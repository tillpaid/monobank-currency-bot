<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Models\CurrencyAccount;
use App\Models\CurrencyRate;
use App\Models\TelegramUser;
use App\Models\TelegramUserSendRate;

class FixturesHelper
{
    private const EUR = 'EUR';

    public function createTelegramUser(
        string $chatId = '1',
        string $state = null,
        array $stateAdditional = null,
    ): TelegramUser {
        $telegramUser = new TelegramUser();
        $telegramUser->chat_id = $chatId;
        $telegramUser->state = $state;
        $telegramUser->state_additional = $stateAdditional;
        $telegramUser->save();

        return $telegramUser;
    }

    public function createCurrencyAccount(
        TelegramUser $telegramUser,
        string $currency = self::EUR,
        float $currencyValue = 100,
        float $purchaseRate = 37,
    ): CurrencyAccount {
        $currencyAccount = new CurrencyAccount();
        $currencyAccount->currency = $currency;
        $currencyAccount->purchase_rate = $purchaseRate;
        $currencyAccount->currency_value = $currencyValue;
        $currencyAccount->uah_value = $currencyValue * $purchaseRate;
        $currencyAccount->telegramUser()->associate($telegramUser);
        $currencyAccount->save();

        return $currencyAccount;
    }

    public function createCurrencyRate(
        string $currency = self::EUR,
        float $sell = 40.7,
        float $buy = 41.39,
        string $createdAt = null,
    ): CurrencyRate {
        $currencyRate = new CurrencyRate();
        $currencyRate->currency = $currency;
        $currencyRate->sell = $sell;
        $currencyRate->buy = $buy;

        if ($createdAt) {
            $currencyRate->created_at = $createdAt;
        }

        $currencyRate->save();

        return $currencyRate;
    }

    public function createTelegramUserSendRate(
        int $telegramUserId,
        int $currencyRateId,
        string $currency = self::EUR,
    ): TelegramUserSendRate {
        $telegramUserSendRate = new TelegramUserSendRate();
        $telegramUserSendRate->telegram_user_id = $telegramUserId;
        $telegramUserSendRate->currency_rate_id = $currencyRateId;
        $telegramUserSendRate->currency = $currency;
        $telegramUserSendRate->save();

        return $telegramUserSendRate;
    }
}
