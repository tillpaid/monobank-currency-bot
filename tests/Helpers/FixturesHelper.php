<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Models\CurrencyAccount;
use App\Models\CurrencyRate;
use App\Models\TelegramUser;
use App\Models\TelegramUserSendRate;
use DateTime;
use Exception;

class FixturesHelper
{
    private const EUR = 'EUR';

    /**
     * @param null|array<string, float|string> $stateAdditional
     */
    public function createTelegramUser(
        string $chatId = '1',
        string $state = null,
        array $stateAdditional = null,
    ): TelegramUser {
        $telegramUser = new TelegramUser();
        $telegramUser->setChatId($chatId);
        $telegramUser->setState($state);
        $telegramUser->setStateAdditional($stateAdditional);
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
        $currencyAccount->setCurrency($currency);
        $currencyAccount->setPurchaseRate($purchaseRate);
        $currencyAccount->setCurrencyValue($currencyValue);
        $currencyAccount->setUahValue($currencyValue * $purchaseRate);
        $currencyAccount->setTelegramUser($telegramUser);
        $currencyAccount->save();

        return $currencyAccount;
    }

    /**
     * @throws Exception
     */
    public function createCurrencyRate(
        string $currency = self::EUR,
        float $sell = 40.7,
        float $buy = 41.39,
        string $createdAt = null,
    ): CurrencyRate {
        $currencyRate = new CurrencyRate();
        $currencyRate->setCurrency($currency);
        $currencyRate->setSell($sell);
        $currencyRate->setBuy($buy);

        if ($createdAt) {
            $currencyRate->setCreatedAt(new DateTime($createdAt));
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
        $telegramUserSendRate->setTelegramUserId($telegramUserId);
        $telegramUserSendRate->setCurrencyRateId($currencyRateId);
        $telegramUserSendRate->setCurrency($currency);
        $telegramUserSendRate->save();

        return $telegramUserSendRate;
    }
}
