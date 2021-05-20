<?php

namespace App\Repositories;

use App\Models\CurrencyRate;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CurrencyRateRepository
 * @package App\Repositories
 */
class CurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    /**
     * @var CurrencyRate
     */
    private $model;

    /**
     * @var Carbon
     */
    private $carbon;

    /**
     * CurrencyRateRepository constructor.
     * @param CurrencyRate $currencyRate
     * @param Carbon $carbon
     */
    public function __construct(CurrencyRate $currencyRate, Carbon $carbon)
    {
        $this->model = $currencyRate;
        $this->carbon = $carbon;
    }

    /**
     * @param string $currency
     * @return Model|null
     */
    public function getLatestCurrencyRate(string $currency): ?Model
    {
        return $this->model
            ->where('currency', $currency)
            ->latest('id')
            ->first();
    }

    /**
     * @param string $currency
     * @return Collection|null
     */
    public function getLastTwoCurrencyRates(string $currency): ?Collection
    {
        $rates = $this->model
            ->where('currency', $currency)
            ->orderBy('id', 'DESC')
            ->take(2)
            ->get();

        return $rates->count() == 2 ? $rates : null;
    }

    /**
     * @param string $currency
     * @return Collection|null
     */
    public function getCurrencyRatesOfLastMonth(string $currency): ?Collection
    {
        $startDate = $this->carbon->subMonth()->format('Y-m-d H:i:s');

        return $this->model
            ->where('currency', $currency)
            ->where('created_at', '>=', $startDate)
            ->get();
    }
}
