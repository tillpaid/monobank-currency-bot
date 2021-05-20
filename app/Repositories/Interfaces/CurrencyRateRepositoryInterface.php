<?php

namespace App\Repositories\Interfaces;

use App\Models\CurrencyRate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface CurrencyRateRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface CurrencyRateRepositoryInterface
{
    /**
     * CurrencyRateRepositoryInterface constructor.
     * @param CurrencyRate $currencyRate
     * @param Carbon $carbon
     */
    public function __construct(CurrencyRate $currencyRate, Carbon $carbon);

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
