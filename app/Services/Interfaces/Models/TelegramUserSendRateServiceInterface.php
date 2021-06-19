<?php

namespace App\Services\Interfaces\Models;

/**
 * Interface TelegramUserSendRateInterface
 * @package App\Services\Interfaces\Models
 */
interface TelegramUserSendRateServiceInterface
{
    /**
     * @param int $telegramUserId
     * @param int $currencyRateId
     * @return bool
     */
    public function checkIfRateChangeBeenSent(int $telegramUserId, int $currencyRateId): bool;

    /**
     * @param int $telegramUserId
     * @param int $currencyRateId
     * @param string $currency
     */
    public function updateSendRate(int $telegramUserId, int $currencyRateId, string $currency): void;
}
