<?php

namespace App\Repositories\Interfaces;

use App\Models\CurrencyRate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface CurrencyRateRepositoryInterface
{
    public function __construct(CurrencyRate $currencyRate, Carbon $carbon);

    public function getLatestCurrencyRate(string $currency): ?Model;

    public function getLastTwoCurrencyRates(string $currency): ?Collection;

    public function getCurrencyRatesOfLastMonth(string $currency): ?Collection;
}
