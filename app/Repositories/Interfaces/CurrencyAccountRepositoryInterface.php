<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface CurrencyAccountRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface CurrencyAccountRepositoryInterface
{
    /**
     * @param int $userId
     * @param string $currency
     * @return float|null
     */
    public function getUserCurrencySum(int $userId, string $currency): ?float;

    /**
     * @param int $userId
     * @param string $currency
     * @return Model|null
     */
    public function getFirstUserCurrencyAccount(int $userId, string $currency): ?Model;

    /**
     * @param int $userId
     * @param string $currency
     * @return Model|null
     */
    public function getLessProfitUserCurrencyAccount(int $userId, string $currency): ?Model;

    /**
     * @param int $userId
     * @return array|null
     */
    public function getUserBalanceSum(int $userId): ?array;
}
