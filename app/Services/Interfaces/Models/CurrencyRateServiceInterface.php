<?php

namespace App\Services\Interfaces\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface CurrencyRateServiceInterface
 * @package App\Services\Interfaces\Models
 */
interface CurrencyRateServiceInterface
{
    /**
     * @param string $currencyName
     * @param string $sell
     * @param string $buy
     * @return bool
     */
    public function createCurrencyRate(string $currencyName, string $sell, string $buy): bool;

    /**
     * @param string $currency
     * @return Model|null
     */
    public function getLatestCurrencyRate(string $currency): ?Model;

    /**
     * @param string $currency
     * @return Collection|null
     */
    public function getLastTwoCurrencyRates(string $currency): ?Collection;

    /**
     * @param string $currency
     * @return Collection|null
     */
    public function getCurrencyRatesOfLastMonth(string $currency): ?Collection;
}
