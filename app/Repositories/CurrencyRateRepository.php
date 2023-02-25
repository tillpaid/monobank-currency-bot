<?php

namespace App\Repositories;

use App\Models\CurrencyRate;
use App\Repositories\Interfaces\CurrencyRateRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CurrencyRateRepository implements CurrencyRateRepositoryInterface
{
    private CurrencyRate $model;
    private Carbon $carbon;

    public function __construct(CurrencyRate $currencyRate, Carbon $carbon)
    {
        $this->model = $currencyRate;
        $this->carbon = $carbon;
    }

    public function getLatestCurrencyRate(string $currency): ?Model
    {
        return $this->model
            ->where('currency', $currency)
            ->latest('id')
            ->first();
    }

    public function getLastTwoCurrencyRates(string $currency): ?Collection
    {
        $rates = $this->model
            ->where('currency', $currency)
            ->orderBy('id', 'DESC')
            ->take(2)
            ->get();

        return $rates->count() == 2 ? $rates : null;
    }

    public function getCurrencyRatesOfLastMonth(string $currency): ?Collection
    {
        $startDate = $this->carbon->subMonth()->format('Y-m-d H:i:s');

        return $this->model
            ->where('currency', $currency)
            ->where('created_at', '>=', $startDate)
            ->get();
    }
}
