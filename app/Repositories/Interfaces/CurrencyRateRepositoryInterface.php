<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface CurrencyRateRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface CurrencyRateRepositoryInterface
{
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
}
