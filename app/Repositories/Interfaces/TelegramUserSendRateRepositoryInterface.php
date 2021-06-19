<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface TelegramUserSendRateRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface TelegramUserSendRateRepositoryInterface
{
    /**
     * @param int $telegramUserId
     * @param int $currencyRateId
     * @return bool
     */
    public function rowExists(int $telegramUserId, int $currencyRateId): bool;

    /**
     * @param int $telegramUserId
     * @param string $currency
     * @return Model|null
     */
    public function getSendRate(int $telegramUserId, string $currency): ?Model;
}
