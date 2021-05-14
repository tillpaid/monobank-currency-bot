<?php

namespace App\Services\Interfaces\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Interface CurrencyAccountServiceInterface
 * @package App\Services\Interfaces\Models
 */
interface CurrencyAccountServiceInterface
{
    /**
     * @param int $userId
     * @param string $currency
     * @param float $uahValue
     * @param float $purchaseRate
     * @return bool
     */
    public function create(int $userId, string $currency, float $uahValue, float $purchaseRate): bool;

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
     * @param float $currencySum
     * @return void
     */
    public function sellCurrency(int $userId, string $currency, float $currencySum): void;

    /**
     * @param int $userId
     * @return array|null
     */
    public function getUserBalanceSum(int $userId): ?array;
}
